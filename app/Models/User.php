<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function getAllUsers($with = false)
    {
        $query = $this->select('id', 'name', 'email')
            ->where('name', '!=', 'Super Admin User');

        if ($with) {
            $query->with('roles:name');
        }

        return $query->get();
    }

    public function addUser($user)
    {
        return $this->create($user);
    }

    public function getUserById($id)
    {
        return $this->findOrFail($id);
    }

    public function updateUser($user, $newUser)
    {
        return $user->update($newUser);
    }

    public function deleteUser($id)
    {
        return $this->where('id', $id)/*->first()?*/->delete();
    }
}
