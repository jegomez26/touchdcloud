<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the support ticket this history entry belongs to
     */
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get the user who made this change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific field changes
     */
    public function scopeField($query, string $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get human readable action description
     */
    public function getActionDescriptionAttribute(): string
    {
        return match($this->action) {
            'created' => 'Ticket created',
            'updated' => 'Ticket updated',
            'assigned' => 'Ticket assigned',
            'status_changed' => 'Status changed',
            'priority_changed' => 'Priority changed',
            'resolved' => 'Ticket resolved',
            'closed' => 'Ticket closed',
            'reopened' => 'Ticket reopened',
            'commented' => 'Comment added',
            'attachment_added' => 'Attachment added',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        if ($this->field && $this->old_value && $this->new_value) {
            return "Changed {$this->field} from '{$this->old_value}' to '{$this->new_value}'";
        }

        if ($this->field && $this->new_value) {
            return "Set {$this->field} to '{$this->new_value}'";
        }

        return $this->action_description;
    }
}
