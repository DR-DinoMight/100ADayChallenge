# 100ADayChallenge Widget Embedding Guide

This guide shows you how to embed your current task tallies on any website using iframes.

## Widget URL

The base widget URL is: `{{ url('/widget') }}`

## Available Parameters

| Parameter | Values | Default | Description |
|-----------|--------|---------|-------------|
| `type` | `push_ups`, `sit_ups`, `squats`, `burpees`, `pull_ups`, or custom | `push_ups` | The type of task to display |
| `theme` | `light`, `dark` | `light` | Widget colour scheme |
| `size` | `small`, `medium`, `large` | `medium` | Widget size (currently affects spacing) |

## Basic Examples

### 1. Default Push-ups Widget
```html
<iframe
    src="{{ url('/widget') }}"
    width="350"
    height="200"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
</iframe>
```

### 2. Dark Theme Squats Widget
```html
<iframe
    src="{{ url('/widget?type=squats&theme=dark') }}"
    width="350"
    height="200"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);">
</iframe>
```

### 3. Sit-ups Widget with Custom Styling
```html
<iframe
    src="{{ url('/widget?type=sit_ups&theme=light') }}"
    width="400"
    height="220"
    frameborder="0"
    scrolling="no"
    style="border: none; border-radius: 12px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);">
</iframe>
```

## Advanced Embedding

### Responsive Widget Container
```html
<div class="widget-container" style="max-width: 400px; margin: 0 auto;">
    <iframe
        src="{{ url('/widget?type=push_ups&theme=light') }}"
        width="100%"
        height="200"
        frameborder="0"
        scrolling="no"
        style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    </iframe>
</div>
```

### Multiple Widgets Side by Side
```html
<div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
    <iframe
        src="{{ url('/widget?type=push_ups&theme=light') }}"
        width="300"
        height="180"
        frameborder="0"
        scrolling="no"
        style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    </iframe>

    <iframe
        src="{{ url('/widget?type=squats&theme=dark') }}"
        width="300"
        height="180"
        frameborder="0"
        scrolling="no"
        style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    </iframe>
</div>
```

## WordPress Integration

### Using Shortcode
Add this to your `functions.php`:
```php
function hundred_a_day_widget_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'push_ups',
        'theme' => 'light',
        'width' => '350',
        'height' => '200'
    ), $atts);

    $url = '{{ url('/widget') }}?type=' . urlencode($atts['type']) . '&theme=' . urlencode($atts['theme']);

    return '<iframe src="' . esc_url($url) . '" width="' . esc_attr($atts['width']) . '" height="' . esc_attr($atts['height']) . '" frameborder="0" scrolling="no" style="border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"></iframe>';
}
add_shortcode('hundred_a_day', 'hundred_a_day_widget_shortcode');
```

Then use in posts/pages:
```
[hundred_a_day type="push_ups" theme="dark" width="400" height="220"]
```

## React Component Example

```jsx
const HundredADayWidget = ({ type = 'push_ups', theme = 'light', width = 350, height = 200 }) => {
    const widgetUrl = `{{ url('/widget') }}?type=${encodeURIComponent(type)}&theme=${encodeURIComponent(theme)}`;

    return (
        <iframe
            src={widgetUrl}
            width={width}
            height={height}
            frameBorder="0"
            scrolling="no"
            style={{
                border: 'none',
                borderRadius: '8px',
                boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)'
            }}
            title={`${type} Progress Widget`}
        />
    );
};

// Usage
<HundredADayWidget type="squats" theme="dark" width={400} height={220} />
```

## Vue.js Component Example

```vue
<template>
  <iframe
    :src="widgetUrl"
    :width="width"
    :height="height"
    frameborder="0"
    scrolling="no"
    :style="iframeStyle"
    :title="`${type} Progress Widget`"
  />
</template>

<script>
export default {
  name: 'HundredADayWidget',
  props: {
    type: {
      type: String,
      default: 'push_ups'
    },
    theme: {
      type: String,
      default: 'light'
    },
    width: {
      type: Number,
      default: 350
    },
    height: {
      type: Number,
      default: 200
    }
  },
  computed: {
    widgetUrl() {
      return `{{ url('/widget') }}?type=${encodeURIComponent(this.type)}&theme=${encodeURIComponent(this.theme)}`;
    },
    iframeStyle() {
      return {
        border: 'none',
        borderRadius: '8px',
        boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)'
      };
    }
  }
};
</script>

<!-- Usage -->
<!-- <HundredADayWidget type="squats" theme="dark" :width="400" :height="220" /> -->
```

## Features

- **Real-time Data**: Shows current day, week, and month totals
- **Progress Bar**: Visual representation of daily goal progress
- **Auto-refresh**: Updates every 5 minutes
- **Responsive**: Adapts to different screen sizes
- **Theme Support**: Light and dark themes
- **Multiple Task Types**: Support for all predefined and custom task types
- **Professional Design**: Clean, modern interface with smooth animations

## Widget Dimensions

| Size | Width | Height | Recommended Use |
|------|-------|--------|-----------------|
| Small | 300px | 180px | Sidebars, small content areas |
| Medium | 350px | 200px | Default size, most use cases |
| Large | 400px | 220px | Featured content, larger displays |

## Troubleshooting

### Widget Not Loading
- Check if the URL is accessible
- Ensure your website allows iframe embedding
- Check browser console for errors

### Widget Too Small/Large
- Adjust the `width` and `height` attributes
- Use responsive sizing with percentage widths
- Consider the content area where you're embedding

### Styling Issues
- The widget uses Tailwind CSS for styling
- Custom CSS can be applied to the iframe container
- Ensure your website's CSS doesn't interfere with the iframe

## Support

For technical support or feature requests, please visit your main tracker at: {{ url('/tracker') }}
