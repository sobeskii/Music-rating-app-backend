<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlaggedReviewReason extends Model
{
    use HasFactory;

    protected $fillable = ['rating_id','flagged_part'];

    protected $table = 'flagged_review_reasons';

    public function rating() : BelongsTo
    {
        return $this->belongsTo('App\Models\Rating','user_ratings');
    }
    public function moderation_rule() : BelongsTo
    {
        return $this->belongsTo('App\Models\ModerationRule');
    }
}
