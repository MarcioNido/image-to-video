<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    protected bool $seed = true;

    /**
     * Integration User is created in the seeder
     * @return TestCase
     */
    public function actingAsIntegrationUser(): TestCase
    {
        /** @var User $integrationUser */
        $integrationUser = User::query()->where('email', 'integration@bdi.com.br')->firstOrFail();
        return $this->actingAs($integrationUser);
    }
}
