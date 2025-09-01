@php
    $taskTypes = \App\Models\TaskType::orderBy('is_built_in', 'desc')->orderBy('display_name')->get();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100ADayChallenge Widget Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Widget Demo</h1>
            <p class="text-xl text-gray-600">See how your task tracker looks when embedded on other websites</p>
        </div>

        <!-- Quick Embed Generator -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Embed Code Generator</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                    <select id="embedType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($taskTypes as $type)
                            <option value="{{ $type->name }}">{{ $type->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                    <select id="embedTheme" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Width</label>
                    <input type="number" id="embedWidth" value="350" min="250" max="600" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Height</label>
                    <input type="number" id="embedHeight" value="200" min="150" max="400" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="bg-gray-100 p-4 rounded-lg mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Generated Embed Code:</span>
                    <button id="copyButton" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
                        Copy Code
                    </button>
                </div>
                <pre id="embedCode" class="text-sm text-gray-800 overflow-x-auto whitespace-pre-wrap bg-white p-3 rounded border"></pre>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                <div id="widgetPreview" class="inline-block"></div>
            </div>
        </div>

        <!-- Widget Showcase -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Default Push-ups Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Default Push-ups</h3>
                <iframe
                    src="{{ url('/widget') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=push_ups, theme=light</code>
                </div>
            </div>

            <!-- Dark Theme Squats Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dark Theme Squats</h3>
                <iframe
                    src="{{ url('/widget?type=squats&theme=dark') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=squats, theme=dark</code>
                </div>
            </div>

            <!-- Sit-ups Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sit-ups Progress</h3>
                <iframe
                    src="{{ url('/widget?type=sit_ups&theme=light') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=sit_ups, theme=light</code>
                </div>
            </div>

            <!-- Burpees Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Burpees Tracker</h3>
                <iframe
                    src="{{ url('/widget?type=burpees&theme=light') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=burpees, theme=light</code>
                </div>
            </div>

            <!-- Pull-ups Widget -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pull-ups Progress</h3>
                <iframe
                    src="{{ url('/widget?type=pull_ups&theme=light') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=pull_ups, theme=light</code>
                </div>
            </div>

            <!-- Dark Theme Push-ups -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dark Push-ups</h3>
                <iframe
                    src="{{ url('/widget?type=push_ups&theme=dark') }}"
                    width="100%"
                    height="200"
                    frameborder="0"
                    scrolling="no"
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                </iframe>
                <div class="mt-4 text-sm text-gray-600">
                    <code class="bg-gray-100 px-2 py-1 rounded">type=push_ups, theme=dark</code>
                </div>
            </div>
        </div>

        <!-- Embed Code Examples -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Embed Code</h2>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Basic Widget</h3>
                    <div class="bg-gray-100 p-4 rounded-lg overflow-x-auto">
                        <pre><code>&lt;iframe
    src="{{ url('/widget') }}"
    width="350"
    height="200"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"&gt;
&lt;/iframe&gt;</code></pre>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Dark Theme Squats</h3>
                    <div class="bg-gray-100 p-4 rounded-lg overflow-x-auto">
                        <pre><code>&lt;iframe
    src="{{ url('/widget?type=squats&theme=dark') }}"
    width="350"
    height="200"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"&gt;
&lt;/iframe&gt;</code></pre>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Responsive Widget</h3>
                    <div class="bg-gray-100 p-4 rounded-lg overflow-x-auto">
                        <pre><code>&lt;div style="max-width: 400px; margin: 0 auto;"&gt;
    &lt;iframe
        src="{{ url('/widget?type=push_ups&theme=light') }}"
        width="100%"
        height="200"
        frameborder="0"
        scrolling="no"
        style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"&gt;
    &lt;/iframe&gt;
&lt;/div&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Parameters -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Parameters</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Values</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">type</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">push_ups, sit_ups, squats, burpees, pull_ups, custom</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">push_ups</td>
                            <td class="px-6 py-4 text-sm text-gray-500">The type of task to display</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">theme</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">light, dark</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">light</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Widget colour scheme</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">size</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">small, medium, large</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">medium</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Widget size (affects spacing)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Back to Tracker -->
        <div class="text-center">
            <a href="{{ url('/tracker') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                Back to Task Tracker
            </a>
        </div>
    </div>

    <script>
        // Embed code generator functionality
        function generateEmbedCode() {
            const type = document.getElementById('embedType').value;
            const theme = document.getElementById('embedTheme').value;
            const width = document.getElementById('embedWidth').value;
            const height = document.getElementById('embedHeight').value;

            const baseUrl = '{{ url('/widget') }}';
            const params = new URLSearchParams();
            if (type !== 'push_ups') params.append('type', type);
            if (theme !== 'light') params.append('theme', theme);

            const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;

            const embedCode = `<iframe
    src="${url}"
    width="${width}"
    height="${height}"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
</iframe>`;

            document.getElementById('embedCode').textContent = embedCode;

            // Update preview
            const preview = document.getElementById('widgetPreview');
            preview.innerHTML = embedCode;
        }

        // Copy to clipboard functionality
        document.getElementById('copyButton').addEventListener('click', async function() {
            const embedCode = document.getElementById('embedCode').textContent;

            try {
                await navigator.clipboard.writeText(embedCode);

                // Show success feedback
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                this.classList.add('bg-green-600');

                setTimeout(() => {
                    this.textContent = originalText;
                    this.classList.remove('bg-green-600');
                    this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy: ', err);
                alert('Failed to copy to clipboard. Please copy manually.');
            }
        });

        // Generate embed code when inputs change
        document.getElementById('embedType').addEventListener('change', generateEmbedCode);
        document.getElementById('embedTheme').addEventListener('change', generateEmbedCode);
        document.getElementById('embedWidth').addEventListener('input', generateEmbedCode);
        document.getElementById('embedHeight').addEventListener('input', generateEmbedCode);

        // Generate initial embed code
        generateEmbedCode();
    </script>
</body>
</html>
