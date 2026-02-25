<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Project extends Model
{

    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'location',
        'sub_sector',
        'source_of_fund',
        'funding_year',
        'amount',
        'start_date',
        'due_date',
        'archived_at',
        'description',
        'progress',
    ];

    /**
     * Dates casting
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'archived_at' => 'datetime',
        'amount' => 'decimal:2',
        'progress' => 'decimal:2',

    ];

    /**
     * ----------------------------
     * Relationships
     * ----------------------------
     */

    /**
     * A project has many tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)
            ->whereNull('archived_at');
    }

    /**
     * ----------------------------
     * Query Scopes
     * ----------------------------
     */

    /**
     * Scope active projects
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope archived projects
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * ----------------------------
     * Helpers
     * ----------------------------
     */

    /**
     * Archive the project
     */
    public function archive(): void
    {
        $this->update([
            'archived_at' => now(),
        ]);
    }

    /**
     * Restore the project
     */
    public function restore(): void
    {
        $this->update([
            'archived_at' => null,
        ]);
    }

    public function activityLogs()
    {
        return $this->hasMany(ProjectActivityLog::class);
    }

    // ===============================
    // PROJECT STATUS LOGIC
    // ===============================

    public function getStatusAttribute()
    {
        // Completed
        if ($this->progress >= 100) {
            return 'completed';
        }

        // Overdue (strictly before today)
        if ($this->progress < 100 && $this->due_date->lt(today())) {
            return 'overdue';
        }

        // Not Started
        if ($this->progress == 0) {
            return 'not_started';
        }

        return 'ongoing';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'completed'   => 'Completed',
            'overdue'     => 'Overdue',
            'not_started' => 'Not Started',
            default       => 'Ongoing',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'completed'   => 'bg-success-subtle text-success',
            'overdue'     => 'bg-danger-subtle text-danger',
            'not_started' => 'bg-secondary-subtle text-secondary',
            default       => 'bg-primary-subtle text-primary',
        };
    }

    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            'completed'   => 'bi-check-circle-fill',
            'overdue'     => 'bi-exclamation-triangle-fill',
            'not_started' => 'bi-dash-circle-fill',
            default       => 'bi-arrow-repeat',
        };
    }

    public function recalculateProgress()
    {
        $average = $this->tasks()->avg('progress') ?? 0;

        $this->update([
            'progress' => round($average, 2),
        ]);
    }

    public function getStatusBorderClassAttribute()
    {
        return match ($this->status) {
            'completed'   => 'status-completed',
            'overdue'     => 'status-overdue',
            'not_started' => 'status-not-started',
            default       => 'status-ongoing',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | BASE SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted($query)
    {
        return $query->where('progress', 100);
    }

    public function scopeOverdue($query)
    {
        return $query->where('progress', '<', 100)
            ->whereDate('due_date', '<', today());
    }

    public function scopeOngoing($query)
    {
        return $query->whereBetween('progress', [1, 99])
            ->where(function ($q) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '>=', today());
            });
    }

    public function scopeNotStarted($query)
    {
        return $query->where('progress', 0)
            ->where(function ($q) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '>=', today());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeAssignedTo(Builder $query, $userId)
    {
        return $query->whereHas('tasks', function ($q) use ($userId) {
            $q->where('assigned_user_id', $userId);
        });
    }

    public function scopeDueSoon($query)
    {
        return $query->where('progress', '<', 100)
            ->whereBetween('due_date', [today(), today()->addDays(7)]);
    }
}
