<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'task_type_id',
        'count',
        'completed_date',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'count' => 'integer',
    ];

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    public function scopeForTaskType($query, $taskTypeId)
    {
        return $query->where('task_type_id', $taskTypeId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('completed_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_date', [$startDate, $endDate]);
    }
}
