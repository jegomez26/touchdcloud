<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicketComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'comment',
        'is_internal',
        'is_admin_reply',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_admin_reply' => 'boolean',
    ];

    /**
     * Get the support ticket this comment belongs to
     */
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get the user who made this comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for public comments (not internal)
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope for internal comments
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope for admin replies
     */
    public function scopeAdminReplies($query)
    {
        return $query->where('is_admin_reply', true);
    }

    /**
     * Scope for user replies
     */
    public function scopeUserReplies($query)
    {
        return $query->where('is_admin_reply', false);
    }
}
