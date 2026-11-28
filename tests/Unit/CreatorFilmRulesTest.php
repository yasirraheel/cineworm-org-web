<?php

namespace Tests\Unit;

use App\Models\User;
use App\Movies;
use Tests\TestCase;

class CreatorFilmRulesTest extends TestCase
{
    public function test_free_catalogue_scope_excludes_approved_paid_films(): void
    {
        $sql = Movies::query()->freeStreamable()->toSql();

        $this->assertStringContainsString('creator_sale_enabled', $sql);
        $this->assertStringContainsString('creator_sale_status', $sql);
    }

    public function test_user_without_a_plan_is_not_a_paid_subscriber(): void
    {
        $user = new User();

        $this->assertFalse($user->hasPaidSubscription());
    }

    public function test_staff_access_is_allowed_without_a_subscription_record(): void
    {
        $user = new User();
        $user->usertype = 'Admin';

        $this->assertTrue($user->hasPaidSubscription());
    }
}
