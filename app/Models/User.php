<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; // or use Passport if you prefer

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Define the possible user roles as constants.
     * This allows for easy reference and avoids typos (e.g., User::ROLE_ADMIN).
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PHARMACY_OWNER = 'pharmacy_owner';
    public const ROLE_PHARMACIST = 'pharmacist';
    public const ROLE_PHARMACY_TECHNICIAN = 'pharmacy_technician';
    public const ROLE_PHARMACY_DISPENSER = 'Dispenser';
    public const ROLE_PHARMACY_CASHIER = 'cashier';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_USER = 'user';

    /**
     * Get an array of all available roles for validation or dropdowns.
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_USER,
            self::ROLE_PHARMACY_OWNER,
            self::ROLE_PHARMACIST,
            self::ROLE_PHARMACY_TECHNICIAN,
            self::ROLE_PHARMACY_DISPENSER,
            self::ROLE_PHARMACY_CASHIER,
            self::ROLE_CUSTOMER,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pharmacy_id',
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'role',
        'is_active',
        'last_login',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed', // Laravel 7+: Automatically hash the password on save
        'is_active' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
        'role' => 'user',
    ];
    /**
     * Get the pharmacy that this user belongs to.
     * A user with a role like 'super_admin' might not belong to any specific pharmacy (pharmacy_id is null).
     */
public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function hasPharmacy(): bool
    {
        return !is_null($this->pharmacy_id);
    }

    
    public function pharmacies()
{
    return $this->belongsToMany(Pharmacy::class, 'pharmacy_user', 'user_id', 'pharmacy_id');
}

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include users of a given role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|array  $role
     */
    public function scopeRole($query, $role)
    {
        return $query->whereIn('role', (array) $role);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param  string|array  $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        return in_array($this->role, (array) $role);
    }

    /**
     * Check if the user is a super admin (often has unrestricted access).
     */
public function isAdmin(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
        ]);
    }

    /**
     * Check if the user is associated with a pharmacy (owner, pharmacist, tech).
     */
    public function isPharmacyStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_PHARMACY_OWNER,
            self::ROLE_PHARMACIST,
            self::ROLE_PHARMACY_TECHNICIAN,
            self::ROLE_PHARMACY_DISPENSER,
            self::ROLE_PHARMACY_CASHIER,
        ]);
    }

    /**
     * Interact with the user's password.
     * Using the 'hashed' cast is the modern Laravel way, but a mutator is also valid.
     */
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }
}