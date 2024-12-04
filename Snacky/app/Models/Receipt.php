<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property mixed $pivot */

class Receipt extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function snacks(): BelongsToMany
    {
        return $this->belongsToMany(Snack::class)->withTimestamps()->withPivot('item_count');
    }

    public function receiptSnack(): HasMany
    {
        return $this->hasMany(ReceiptSnack::class);
    }
}
