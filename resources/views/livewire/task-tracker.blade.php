<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 p-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Daily Task Tracker
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                Track your daily progress and build consistent habits
            </p>

            <!-- User Info and Logout -->
            <div class="flex justify-center items-center gap-4 mb-4">
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    Logged in as: {{ session('user_email') }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="flex justify-center gap-3">
                <a href="{{ url('/widget-demo') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Embed Widget
                </a>
                <a href="{{ url('/widget') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Widget
                </a>
            </div>
        </div>

        <!-- Task Type Selector -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Select Task Type</h2>

            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($this->taskTypes as $id => $label)
                    <div class="relative group">
                        <button
                            wire:click="setTaskType({{ $id }})"
                            class="px-4 py-2 rounded-lg border-2 transition-all duration-200 {{ $selectedTaskTypeId === $id
                                ? 'border-blue-500 bg-blue-500 text-white'
                                : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-300 dark:hover:border-blue-400' }}"
                        >
                            {{ $label }}
                        </button>

                        @if($this->currentTaskType && !$this->currentTaskType->is_built_in && $selectedTaskTypeId === $id)
                            <button
                                wire:click="removeCustomTaskType({{ $id }})"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600"
                                title="Remove custom task type"
                            >
                                ×
                            </button>
                        @endif
                    </div>
                @endforeach

                <button
                    wire:click="$set('showAddTaskType', true)"
                    class="px-4 py-2 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-blue-300 dark:hover:border-blue-400 hover:text-blue-500 dark:hover:text-blue-400 transition-all duration-200"
                >
                    + Custom
                </button>
            </div>

            <!-- Custom Task Type Input -->
            @if($showAddTaskType)
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="newTaskType"
                            wire:keydown.enter="addCustomTaskType"
                            placeholder="Enter custom task name"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                        <input
                            type="number"
                            wire:model="newTaskTypeGoal"
                            placeholder="Daily goal"
                            min="1"
                            max="10000"
                            class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center dark:bg-gray-700 dark:text-white"
                        >
                        <button
                            wire:click="addCustomTaskType"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200"
                        >
                            Add
                        </button>
                    </div>
                    @error('newTaskType')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                    @error('newTaskTypeGoal')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>

        <!-- Current Task Display -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $this->currentTaskType ? $this->currentTaskType->display_name : 'Select Task Type' }}
                </h2>
                @if($this->currentTaskType)
                    <p class="text-gray-600 dark:text-gray-300">
                        Daily Goal: {{ number_format($dailyGoal) }}
                    </p>
                @endif
            </div>

            @if($this->currentTaskType)
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                        <span>Progress</span>
                        <span>{{ number_format($this->progressPercentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div
                            class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500 ease-out"
                            style="width: {{ $this->progressPercentage }}%"
                        ></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mt-2">
                        <span>{{ number_format($this->todayTotal) }} completed</span>
                        <span>{{ number_format($this->remaining) }} remaining</span>
                    </div>
                </div>

                <!-- Quick Add Form -->
                <div class="flex gap-3 justify-center">
                    <input
                        type="number"
                        wire:model="count"
                        wire:keydown.enter="addTask"
                        placeholder="Count"
                        min="1"
                        class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center dark:bg-gray-700 dark:text-white"
                    >
                    <button
                        wire:click="addTask"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 transition-colors duration-200"
                    >
                        <span wire:loading.remove>Add</span>
                        <span wire:loading>Adding...</span>
                    </button>
                </div>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    Please select a task type to start tracking
                </div>
            @endif
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Today's Total -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                    {{ number_format($this->todayTotal) }}
                </div>
                <div class="text-gray-600 dark:text-gray-300">Today</div>
            </div>

            <!-- 7-Day Total -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                    {{ number_format($this->weekTotal) }}
                </div>
                <div class="text-gray-600 dark:text-gray-300 mb-1">Last 7 Days</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Week {{ $this->weekNumber }}
                </div>
            </div>

            <!-- 30-Day Total -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                    {{ number_format($this->monthTotal) }}
                </div>
                <div class="text-gray-600 dark:text-gray-300 mb-1">Last 30 Days</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Day {{ $this->dayOut30 }} / 30
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
            <div class="space-y-3">
                @if($this->currentTaskType)
                    @php
                        $recentTasks = \App\Models\Task::forTaskType($selectedTaskTypeId)
                            ->with('taskType')
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp

                    @forelse($recentTasks as $task)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $task->count }} {{ $task->taskType->display_name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $task->created_at->format('M j, g:i A') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No tasks recorded yet. Start by adding your first one above!
                        </div>
                    @endforelse
                @else
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                        Select a task type to view recent activity
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
