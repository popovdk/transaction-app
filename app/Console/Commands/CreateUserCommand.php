<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create {email} {password} {name}';
    protected $description = 'Create a new user';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');

        try {
            if (User::where('email', $email)->exists()) {
                $this->error('User with this email already exists');
                return Command::FAILURE;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->info('User created successfully');
            $this->info("User ID: {$user->id}");
            $this->info("Email: {$user->email}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create user: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
} 