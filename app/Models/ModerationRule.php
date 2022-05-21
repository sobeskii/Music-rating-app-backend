<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModerationRule extends Model
{
    use HasFactory;

    protected $table = 'moderation_rules';

    protected $fillable = [ 'name','reason','active','detect_after_count','mute_minutes'];

    public $timestamps = false;

    public function flaggedReviewReasons() : HasMany
    {
        return $this->hasMany('App\Models\FlaggedReviewReason');
    }
}
