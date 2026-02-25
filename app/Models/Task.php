<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Task extends Model
{

    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_type',
        'assigned_user_id',
        'start_date',
        'due_date',
        'progress',
        'created_by',
        'archived_at',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
        'archived_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class)
            ->latest();
    }

    public function activityLogs()
    {
        return $this->hasMany(TaskActivityLog::class)->latest();
    }

    public function getStatusAttribute()
    {
        $progress = (int) $this->progress;
        $due = $this->due_date ? Carbon::parse($this->due_date) : null;
        $today = Carbon::today();

        // COMPLETED
        if ($progress === 100) {
            return 'completed';
        }

        // OVERDUE (only if not completed)
        if ($due && $due->lt($today)) {
            return 'overdue';
        }

        // NOT STARTED
        if ($progress === 0) {
            return 'not_started';
        }

        // ONGOING
        return 'ongoing';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'overdue' => 'Overdue',
            'not_started' => 'Not Started',
            'ongoing' => 'Ongoing',
            default => '—',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'completed' => 'bg-success-subtle text-success',
            'overdue' => 'bg-danger-subtle text-danger',
            'not_started' => 'bg-secondary-subtle text-secondary',
            'ongoing' => 'bg-primary-subtle text-primary',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusBorderClassAttribute()
    {
        return match ($this->status) {
            'completed' => 'status-completed',
            'overdue' => 'status-overdue',
            'not_started' => 'status-not-started',
            'ongoing' => 'status-ongoing',
            default => '',
        };
    }

    public function scopeStatus($query, $filter)
    {
        if ($filter === 'completed') {
            return $query->where('progress', 100);
        }

        if ($filter === 'overdue') {
            return $query->where('progress', '<', 100)
                ->whereDate('due_date', '<', today());
        }

        if ($filter === 'not_started') {
            return $query->where('progress', 0)
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        }

        if ($filter === 'ongoing') {
            return $query->whereBetween('progress', [1, 99])
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhereDate('due_date', '>=', today());
                });
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | BASE SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeAssignedTo(Builder $query, $userId)
    {
        return $query->where('assigned_user_id', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted(Builder $query)
    {
        return $query->where('progress', 100);
    }

    public function scopeOverdue(Builder $query)
    {
        return $query->where('progress', '<', 100)
            ->whereDate('due_date', '<', today());
    }

    public function scopeOngoing(Builder $query)
    {
        return $query->whereBetween('progress', [1, 99])
            ->where(function ($q) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '>=', today());
            });
    }

    public function scopeNotStarted(Builder $query)
    {
        return $query->where('progress', 0)
            ->where(function ($q) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '>=', today());
            });
    }

    public function scopeDueSoon(Builder $query)
    {
        return $query->where('progress', '<', 100)
            ->whereBetween('due_date', [today(), today()->addDays(7)]);
    }
}
