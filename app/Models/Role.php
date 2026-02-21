<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Kuldeep
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all users with this role in a company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Kuldeep
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_company_role')
                    ->withPivot('company_id')
                    ->withTimestamps();
    }

    /**
     * Get all companies that have users with this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Kuldeep
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_company_role')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }
}

