<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @author Kuldeep
 */
class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'company_id',
        'role_id',
        'invited_by',
        'token',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     *
     * @return void
     * @author Kuldeep
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(60);
            }
        });
    }

    /**
     * Get the company that the invitation is for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the role being invited to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who sent the invitation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Check if invitation is accepted.
     *
     * @return bool
     * @author Kuldeep
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
