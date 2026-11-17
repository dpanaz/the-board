<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BoardMember extends Authenticatable
{
    use Notifiable;

    protected $table = 'board_members';

    protected $fillable = ['name', 'email', 'password', 'company', 'industry', 'title', 'phone', 'website', 'bio'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'email_verified_at' => 'datetime'];
    }

    public function isApproved(): bool
    {
        return $this->status === 'active';
    }
}
