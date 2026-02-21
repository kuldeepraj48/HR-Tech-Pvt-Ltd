<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @author Kuldeep
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
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
        'password' => 'hashed',
    ];

    /**
     * Get the company that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all roles for the user across companies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Kuldeep
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_company_role')
                    ->withPivot('company_id')
                    ->withTimestamps();
    }

    /**
     * Get all short URLs created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kuldeep
     */
    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }

    /**
     * Get all invitations sent by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kuldeep
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Check if user has a specific role in a company.
     *
     * @param string $roleName
     * @param int|null $companyId If null, checks in user's current company (or all companies if company_id is null)
     * @return bool
     * @author Kuldeep
     */
    public function hasRole(string $roleName, ?int $companyId = null): bool
    {
        $query = $this->roles()->where('roles.name', $roleName);
        
        // If no company ID provided, use user's current company
        // If user has no company_id (e.g., SuperAdmin), check across all companies
        if ($companyId === null && $this->company_id !== null) {
            $companyId = $this->company_id;
        }
        
        if ($companyId !== null) {
            $query->wherePivot('company_id', $companyId);
        }
        
        return $query->exists();
    }

    /**
     * Get role for user in a specific company.
     *
     * @param int $companyId
     * @return Role|null
     * @author Kuldeep
     */
    public function getRoleInCompany(int $companyId): ?Role
    {
        return $this->roles()
                    ->wherePivot('company_id', $companyId)
                    ->first();
    }
}
