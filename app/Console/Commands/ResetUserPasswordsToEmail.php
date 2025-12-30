<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordsToEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-user-passwords-to-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set all user passwords to their email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting user passwords to their email...');

        $users = User::whereNotIn('role', ['admin', 'dietician'])->get();
        $count = $users->count();

        if ($count === 0) {
            $this->info('No users found to reset.');
            return;
        }

        $this->withProgressBar($users, function ($user) {
            // Setting the plain email string. 
            // The 'password' => 'hashed' cast in User model will handle the hashing automatically.
            $user->password = $user->email;
            $user->save();
        });

        $this->newLine();
        $this->info("Command completed. {$count} passwords were reset.");
    }
}

