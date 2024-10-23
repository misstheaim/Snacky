<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Snack extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'uzum_category_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes() :HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
