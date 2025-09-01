<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class WidgetController extends Controller
{
    public function show(Request $request)
    {
        $taskTypeName = $request->get('type', 'push_ups');
        $theme = $request->get('theme', 'light');
        $size = $request->get('size', 'medium');

        // Get the task type from the database
        $taskType = TaskType::where('name', $taskTypeName)->first();

        if (!$taskType) {
            // Fallback to push-ups if task type not found
            $taskType = TaskType::where('name', 'push_ups')->first();
        }

        if (!$taskType) {
            abort(404, 'Task type not found');
        }

        // Get current tallies using the task type ID
        $todayTotal = Task::forTaskType($taskType->id)
            ->forDate(now()->toDateString())
            ->sum('count');

        $weekTotal = Task::forTaskType($taskType->id)
            ->forDateRange(
                now()->subDays(6)->startOfDay(),
                now()->endOfDay()
            )
            ->sum('count');

        $monthTotal = Task::forTaskType($taskType->id)
            ->forDateRange(
                now()->subDays(29)->startOfDay(),
                now()->endOfDay()
            )
            ->sum('count');

        $dailyGoal = $taskType->daily_goal;
        $progressPercentage = $dailyGoal > 0 ? min(100, ($todayTotal / $dailyGoal) * 100) : 0;

        $data = [
            'taskType' => $taskType->name,
            'taskTypeLabel' => $taskType->display_name,
            'todayTotal' => $todayTotal,
            'weekTotal' => $weekTotal,
            'monthTotal' => $monthTotal,
            'dailyGoal' => $dailyGoal,
            'progressPercentage' => $progressPercentage,
            'remaining' => max(0, $dailyGoal - $todayTotal),
            'theme' => $theme,
            'size' => $size,
        ];

        return view('widgets.task-tracker', $data);
    }
}
