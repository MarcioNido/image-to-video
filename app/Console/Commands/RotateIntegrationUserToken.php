<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RotateIntegrationUserToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rotate-integration-user-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rotate the Integration User token';

    public function handle(): int
    {
        /** @var User $user */
        $user = User::query()->where('email', 'integration@bdi.com.br')->firstOrFail();
        
        $user->tokens()->delete();

        $token = $user->createToken(
            'Integration User Token',
            ['video:integration']
        )->plainTextToken;

        $this->info("Integration User token: $token");

        return CommandAlias::SUCCESS;
    }
}
