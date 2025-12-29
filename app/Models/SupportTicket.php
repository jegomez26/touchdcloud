<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'user_id',
        'assigned_to',
        'category_id',
        'resolved_at',
        'resolution_notes',
        'attachments',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * Generate a unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        do {
            $ticketNumber = 'TK-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    /**
     * Get the user who created the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin assigned to the ticket
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the category for this ticket
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class);
    }

    /**
     * Get the comments for this ticket
     */
    public function comments(): HasMany
    {
        return $this->hasMany(SupportTicketComment::class);
    }

    /**
     * Get the attachments for this ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class);
    }

    /**
     * Get the history for this ticket
     */
    public function history(): HasMany
    {
        return $this->hasMany(SupportTicketHistory::class);
    }

    /**
     * Get the readable type name
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'bug_report' => 'Bug Report',
            'feature_request' => 'Feature Request',
            'technical_issue' => 'Technical Issue',
            'account_issue' => 'Account Issue',
            'billing_question' => 'Billing Question',
            'general_inquiry' => 'General Inquiry',
            'complaint' => 'Complaint',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Get the readable priority name
     */
    public function getPriorityNameAttribute(): string
    {
        return ucfirst($this->priority);
    }

    /**
     * Get the readable status name
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    /**
     * Get the priority color class
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-yellow-100 text-yellow-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-purple-100 text-purple-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for assigned tickets
     */
    public function scopeAssignedTo($query, int $adminId)
    {
        return $query->where('assigned_to', $adminId);
    }

    /**
     * Scope for unassigned tickets
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }
}
