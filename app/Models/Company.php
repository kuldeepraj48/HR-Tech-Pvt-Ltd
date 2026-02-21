<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Kuldeep
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all users belonging to this company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kuldeep
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all roles assigned to users in this company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Kuldeep
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_company_role')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }

    /**
     * Get all short URLs created by users in this company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kuldeep
     */
    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }

    /**
     * Get all invitations for this company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Kuldeep
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
