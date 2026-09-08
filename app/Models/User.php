<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasUnreadMessages()
    {
        return $this->messages()->where('is_read', false)->exists();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
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
