<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
    ];

    /**
     * Dates casting
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'archived_at' => 'datetime',
        'amount' => 'decimal:2',
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
     * Computed Attributes
     * ----------------------------
     */

    /**
     * Get project status (auto-computed from tasks)
     */
    public function getStatusAttribute(): string
    {
        $tasks = $this->tasks;

        if ($tasks->isEmpty()) {
            return 'Not Started';
        }

        // If at least one task is ongoing (1–99)
        if ($tasks->whereBetween('progress', [1, 99])->count() > 0) {
            return 'Ongoing';
        }

        // If all tasks are completed
        if ($tasks->every(fn($task) => $task->progress === 100)) {
            return 'Completed';
        }

        return 'Not Started';
    }

    /**
     * Get project progress (average of task progress)
     */
    public function getProgressAttribute(): int
    {
        $tasks = $this->tasks;

        if ($tasks->isEmpty()) {
            return 0;
        }

        return (int) round($tasks->avg('progress'));
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
}
