<?php

namespace App\Models;

use App\Observers\SnackObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([SnackObserver::class])]
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

    public function receipts() :BelongsToMany
    {
        return $this->belongsToMany(Receipt::class)->withTimestamps()->withPivot('item_count');
    }

    public function receiptSnack() :HasMany
    {
        return $this->hasMany(ReceiptSnack::class);
    }

    public function comments() :HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
