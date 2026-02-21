<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @author Kuldeep
 */
class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_code',
        'original_url',
        'user_id',
        'company_id',
        'hits',
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

        static::creating(function ($shortUrl) {
            if (empty($shortUrl->short_code)) {
                $shortUrl->short_code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique short code.
     *
     * @return string
     * @author Kuldeep
     */
    protected static function generateUniqueCode(): string
    {
        do {
            $code = Str::random(8);
        } while (static::where('short_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who created the short URL.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the short URL.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Kuldeep
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the full short URL.
     *
     * @return string
     * @author Kuldeep
     */
    public function getShortUrlAttribute(): string
    {
        return url('/s/' . $this->short_code);
    }

    /**
     * Increment the hit count for this short URL.
     *
     * @return void
     * @author Kuldeep
     */
    public function incrementHits(): void
    {
        $this->increment('hits');
    }
}
