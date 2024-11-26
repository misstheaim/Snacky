<?php

namespace App\Models;

use App\Observers\SnackObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

#[ObservedBy([SnackObserver::class])]
class Snack extends Model
{
    use HasFactory, HasFilamentComments;

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

    public function notifications() :HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function filamentComments() :MorphMany
    {
        return $this->morphMany(CustomFilamentComment::class, 'subject');
    }

    public function approvedByUser() :BelongsToMany
    {
        return $this->belongsToMany(User::class, 'snack_approved_by_user')->withTimestamps();
    }
}
