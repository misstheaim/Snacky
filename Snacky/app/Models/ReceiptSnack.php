<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReceiptSnack extends Model
{
    use HasFactory;

    protected $table = 'receipt_snack';

    protected $guarded = [];

    public function snack() :BelongsTo
    {
        return $this->belongsTo(Snack::class);
    }

    public function receipt() :BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }
}
