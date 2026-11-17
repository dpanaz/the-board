<?php

namespace App\Console\Commands;

use App\Models\BoardMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateBoardAdmin extends Command
{
    protected $signature = 'board:create-admin {email} {--name=}';

    protected $description = 'Create the initial approved Board administrator with a random temporary password';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));
        $name = (string) ($this->option('name') ?: 'Board Administrator');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A valid email is required.');
            return self::FAILURE;
        }

        if (BoardMember::where('email', $email)->exists()) {
            $this->error('A Board account already exists for this email.');
            return self::FAILURE;
        }

        $password = Str::random(32);
        $admin = new BoardMember();
        $admin->name = $name;
        $admin->email = $email;
        $admin->password = Hash::make($password);
        $admin->role = 'super_admin';
        $admin->status = 'active';
        $admin->save();

        $this->warn('Initial password (store securely and change after first sign-in):');
        $this->line($password);

        return self::SUCCESS;
    }
}
