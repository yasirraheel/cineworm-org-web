<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'user_image', 'mobile', 'remember_token','usertype', 'zoom_access_token', 'zoom_refresh_token', 'zoom_account_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'usertype' => 'User',
    ];

    protected static function booted()
    {
        static::saving(function (self $user) {
            if (empty($user->email) || ($user->exists && !$user->isDirty('email'))) {
                return;
            }

            static::releaseDeletedEmailIfNeeded($user->email, $user->getKey());
        });

        static::deleting(function (self $user) {
            if ($user->isForceDeleting() || empty($user->email)) {
                return;
            }

            $user->releaseEmailForReuse();
        });
    }

    public static function uniqueEmailRule($ignoreId = null)
    {
        $rule = Rule::unique('users', 'email')->where(function ($query) {
            $query->whereNull('deleted_at');
        });

        if (!is_null($ignoreId)) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }

    public static function releaseDeletedEmailIfNeeded(string $email, $ignoreId = null): void
    {
        static::onlyTrashed()
            ->where('email', $email)
            ->when(!is_null($ignoreId), function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->get()
            ->each(function (self $deletedUser) {
                $deletedUser->releaseEmailForReuse();
            });
    }

    public function releaseEmailForReuse(): void
    {
        if (str_contains((string) $this->email, '__deleted__')) {
            return;
        }

        $suffix = '__deleted__' . $this->getKey() . '_' . time();
        $maxBaseLength = 255 - strlen($suffix);
        $baseEmail = substr((string) $this->email, 0, max(0, $maxBaseLength));

        $this->forceFill([
            'email' => $baseEmail . $suffix,
        ])->saveQuietly();
    }

    public static function getUserInfo($id)
    {
        return User::find($id);
    }

    public static function getUserFullname($id)
    {
        $userinfo = User::find($id);

        if ($userinfo) {
            return $userinfo->name;
        } else {
            return '';
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPassword($token));
    }

    /**
     * Check if user has an active paid subscription or staff access
     */
    public function hasPaidSubscription(): bool
    {
        if (in_array($this->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return true;
        }

        if (empty($this->plan_id) || empty($this->exp_date)) {
            return false;
        }

        $today = strtotime(date('m/d/Y'));
        if ($today > (int) $this->exp_date) {
            return false;
        }

        $plan = \App\SubscriptionPlan::find($this->plan_id);
        if ($plan && (float) $plan->plan_price > 0) {
            return true;
        }

        if ((float) $this->plan_amount > 0) {
            return true;
        }

        return \App\Transactions::where('user_id', $this->id)
            ->where('payment_amount', '>', 0)
            ->exists();
    }

    /**
     * Get user's current subscription plan object
     */
    public function getSubscriptionPlan()
    {
        if (!empty($this->plan_id)) {
            return \App\SubscriptionPlan::find($this->plan_id);
        }
        return null;
    }
}

class CustomPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url('password/reset/' . $this->token);

        return (new MailMessage)
            ->subject('Reset Password')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.password', ['url' => $url]);
    }
}
