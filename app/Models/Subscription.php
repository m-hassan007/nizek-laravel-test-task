<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    // Define the fillable fields or guarded fields
    protected $fillable = [
        'user_id',
        'stripe_plan',
        'stripe_status',
        'starts_at',
        'ends_at',
    ];

    // Define the relationship to the user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
