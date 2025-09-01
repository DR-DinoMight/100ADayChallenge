<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $taskTypeLabel }} Progress Widget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { margin: 0; padding: 0; }
        .widget-container { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }

        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animation classes */
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .slide-up { animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="bg-transparent">
    <div class="widget-container fade-in">
        @if($theme === 'dark')
            <div class="bg-gray-900 text-white rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3">
                    <h2 class="text-lg font-bold text-center">{{ $taskTypeLabel }} Progress</h2>
                </div>

                <!-- Progress Section -->
                <div class="p-4">
                    <!-- Today's Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">Today's Progress</span>
                            <span class="text-sm text-gray-300">{{ number_format($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500 ease-out slide-up"
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>{{ number_format($todayTotal) }} / {{ number_format($dailyGoal) }}</span>
                            <span>{{ number_format($remaining) }} remaining</span>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center slide-up" style="animation-delay: 0.1s">
                            <div class="text-2xl font-bold text-blue-400">{{ number_format($todayTotal) }}</div>
                            <div class="text-xs text-gray-400">Today</div>
                        </div>
                        <div class="text-center slide-up" style="animation-delay: 0.2s">
                            <div class="text-2xl font-bold text-green-400">{{ number_format($weekTotal) }}</div>
                            <div class="text-xs text-gray-400">7 Days</div>
                        </div>
                        <div class="text-center slide-up" style="animation-delay: 0.3s">
                            <div class="text-2xl font-bold text-purple-400">{{ number_format($monthTotal) }}</div>
                            <div class="text-xs text-gray-400">30 Days</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white text-gray-900 rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-4 py-3">
                    <h2 class="text-lg font-bold text-center text-white">{{ $taskTypeLabel }} Progress</h2>
                </div>

                <!-- Progress Section -->
                <div class="p-4">
                    <!-- Today's Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Today's Progress</span>
                            <span class="text-sm text-gray-600">{{ number_format($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500 ease-out slide-up"
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ number_format($todayTotal) }} / {{ number_format($dailyGoal) }}</span>
                            <span>{{ number_format($remaining) }} remaining</span>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center slide-up" style="animation-delay: 0.1s">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($todayTotal) }}</div>
                            <div class="text-xs text-gray-500">Today</div>
                        </div>
                        <div class="text-center slide-up" style="animation-delay: 0.2s">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($weekTotal) }}</div>
                            <div class="text-xs text-gray-500">7 Days</div>
                        </div>
                        <div class="text-center slide-up" style="animation-delay: 0.3s">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($monthTotal) }}</div>
                            <div class="text-xs text-gray-500">30 Days</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-2">
            <a href="{{ url('/tracker') }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                Powered by 100ADayChallenge
            </a>
        </div>
    </div>

    <script>
        // Auto-refresh widget every 5 minutes
        setInterval(function() {
            window.location.reload();
        }, 5 * 60 * 1000);

        // Resize iframe to fit content
        function resizeWidget() {
            const height = document.body.scrollHeight;
            window.parent.postMessage({
                type: 'resize',
                height: height
            }, '*');
        }

        // Resize on load and after animations
        window.addEventListener('load', resizeWidget);
        setTimeout(resizeWidget, 800); // After animations complete
    </script>
</body>
</html>
