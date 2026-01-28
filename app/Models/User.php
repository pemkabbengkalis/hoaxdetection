<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Tables;
use Filament\Facades\Filament;



// class User extends Authenticatable
// {
//     /** @use HasFactory<\Database\Factories\UserFactory> */
//     use HasFactory, Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $fillable = [
//         'name',
//         'email',
//         'password',
//     ];

//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var list<string>
//      */
//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /**
//      * Get the attributes that should be cast.
//      *
//      * @return array<string, string>
//      */
//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'password' => 'hashed',
//         ];
//     }

//     function isAdmin(){
//         return $this->role == 'admin';
//     }
//      function isKadis(){
//         return $this->role == 'kadis';
//     }

//      function isTeam(){
//         return $this->role == 'team';
//     }
//       function isValidator(){
//         return $this->role == 'validator';
//     }

// }



//------------adrian-------------------------------------//
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN     = 'admin';
    public const ROLE_KADIS     = 'kadis';
    public const ROLE_TEAM      = 'team';
    public const ROLE_VALIDATOR = 'validator';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔐 universal checker
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // 🧠 helper (opsional)
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }


    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    //-----------------end of adrian-------------------------------//
}
