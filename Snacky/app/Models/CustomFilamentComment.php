<?php

namespace App\Models;

use App\Observers\FilamentCommentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Parallax\FilamentComments\Models\FilamentComment;

#[ObservedBy([FilamentCommentObserver::class])]
class CustomFilamentComment extends FilamentComment
{
}
