<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskType;
use Livewire\Component;
use Carbon\Carbon;

class TaskTracker extends Component
{
    public ?int $selectedTaskTypeId = null;
    public int $count = 0;
    public int $dailyGoal = 100;
    public string $newTaskType = '';
    public int $newTaskTypeGoal = 50;
    public bool $showAddTaskType = false;

    protected $rules = [
        'count' => 'required|integer|min:1',
        'selectedTaskTypeId' => 'required|exists:task_types,id',
        'dailyGoal' => 'required|integer|min:1',
    ];

    protected $messages = [
        'newTaskType.required' => 'Task type name is required.',
        'newTaskType.min' => 'Task type name must be at least 1 character.',
        'newTaskType.max' => 'Task type name cannot exceed 50 characters.',
        'newTaskTypeGoal.required' => 'Daily goal is required.',
        'newTaskTypeGoal.min' => 'Daily goal must be at least 1.',
        'newTaskTypeGoal.max' => 'Daily goal cannot exceed 10,000.',
    ];

    protected string $layout = 'components.layouts.app';

    public function mount()
    {
        // Set default task type to push-ups
        $defaultTaskType = TaskType::where('name', 'push_ups')->first();
        if ($defaultTaskType) {
            $this->selectedTaskTypeId = $defaultTaskType->id;
            $this->dailyGoal = $defaultTaskType->daily_goal;
        }
    }

    public function addTask()
    {
        $this->validate();

        Task::create([
            'task_type_id' => $this->selectedTaskTypeId,
            'count' => $this->count,
            'completed_date' => now()->toDateString(),
        ]);

        $this->count = 0;
        $this->dispatch('task-added');
    }

    public function setTaskType(int $taskTypeId)
    {
        $taskType = TaskType::find($taskTypeId);
        if ($taskType) {
            $this->selectedTaskTypeId = $taskTypeId;
            $this->dailyGoal = $taskType->daily_goal;
            $this->showAddTaskType = false;
            $this->newTaskType = '';
            $this->newTaskTypeGoal = 50;
        }
    }

    public function addCustomTaskType()
    {
        $this->validate([
            'newTaskType' => 'required|string|min:1|max:50',
            'newTaskTypeGoal' => 'required|integer|min:1|max:10000',
        ]);

        $customType = strtolower(trim($this->newTaskType));

        // Check if task type already exists
        $existingTaskType = TaskType::where('name', $customType)->first();
        if ($existingTaskType) {
            $this->addError('newTaskType', 'This task type already exists.');
            return;
        }

        // Create new task type
        $newTaskType = TaskType::create([
            'name' => $customType,
            'display_name' => ucwords(str_replace('_', ' ', $customType)),
            'daily_goal' => $this->newTaskTypeGoal,
            'is_built_in' => false,
        ]);

        // Set it as the current task type
        $this->selectedTaskTypeId = $newTaskType->id;
        $this->dailyGoal = $newTaskType->daily_goal;
        $this->showAddTaskType = false;
        $this->newTaskType = '';
        $this->newTaskTypeGoal = 50;
    }

    public function removeCustomTaskType(int $taskTypeId)
    {
        $taskType = TaskType::find($taskTypeId);
        if ($taskType && !$taskType->is_built_in) {
            // If we're currently using this task type, switch to push-ups
            if ($this->selectedTaskTypeId === $taskTypeId) {
                $defaultTaskType = TaskType::where('name', 'push_ups')->first();
                if ($defaultTaskType) {
                    $this->selectedTaskTypeId = $defaultTaskType->id;
                    $this->dailyGoal = $defaultTaskType->daily_goal;
                }
            }

            $taskType->delete();
        }
    }

    public function getTodayTotalProperty()
    {
        if (!$this->selectedTaskTypeId) return 0;

        return Task::forTaskType($this->selectedTaskTypeId)
            ->forDate(now()->toDateString())
            ->sum('count');
    }

    public function getWeekTotalProperty()
    {
        if (!$this->selectedTaskTypeId) return 0;

        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        return Task::forTaskType($this->selectedTaskTypeId)
            ->forDateRange($startDate, $endDate)
            ->sum('count');
    }

    public function getMonthTotalProperty()
    {
        if (!$this->selectedTaskTypeId) return 0;

        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        return Task::forTaskType($this->selectedTaskTypeId)
            ->forDateRange($startDate, $endDate)
            ->sum('count');
    }

    public function getWeekProgressProperty()
    {
        // For 7-day rolling period, we're always on day 7 (current day)
        return 7;
    }

    public function getMonthProgressProperty()
    {
        // For 30-day rolling period, we're always on day 30 (current day)
        return 30;
    }

    public function getWeekNumberProperty()
    {
        if (!$this->selectedTaskTypeId) return 1;

        // Get the first entry date for this task type
        $firstEntry = Task::forTaskType($this->selectedTaskTypeId)
            ->orderBy('completed_date')
            ->first();

        if (!$firstEntry) {
            return 1; // If no entries yet, start at week 1
        }

        $firstDate = $firstEntry->completed_date;
        $currentDate = now();

        // Calculate weeks since first entry
        $weeksSinceStart = $firstDate->diffInWeeks($currentDate) + 1;

        //return rounded down value
        return floor($weeksSinceStart);
    }

    public function getDayOut30Property()
    {
        if (!$this->selectedTaskTypeId) return 1;

        $firstEntry = Task::forTaskType($this->selectedTaskTypeId)
            ->orderBy('completed_date')
            ->first();

        if (!$firstEntry) {
            return 1; // If no entries yet, start at day 1
        }

        $firstDate = $firstEntry->completed_date;
        $currentDate = now();
        $daysSinceStart = $currentDate->diffInDays($firstDate);
        return abs(floor($daysSinceStart));
    }

    public function getProgressPercentageProperty()
    {
        if ($this->dailyGoal <= 0) {
            return 0;
        }

        $percentage = ($this->todayTotal / $this->dailyGoal) * 100;
        return min(100, $percentage);
    }

    public function getRemainingProperty()
    {
        return max(0, $this->dailyGoal - $this->todayTotal);
    }

    public function getTaskTypesProperty()
    {
        return TaskType::orderBy('is_built_in', 'desc')
            ->orderBy('display_name')
            ->get()
            ->keyBy('id')
            ->map(function ($taskType) {
                return $taskType->display_name;
            });
    }

    public function getCurrentTaskTypeProperty()
    {
        if (!$this->selectedTaskTypeId) return null;
        return TaskType::find($this->selectedTaskTypeId);
    }

    public function render()
    {
        return view('livewire.task-tracker');
    }
}
