# API Documentation

## Core Classes

### SM\Core\Plugin
The main plugin class that handles initialization and core functionality.

```php
namespace SM\Core;

class Plugin {
    /**
     * Get the plugin instance.
     *
     * @return Plugin
     */
    public static function get_instance(): Plugin;
    
    /**
     * Initialize plugin hooks.
     */
    protected function init_hooks(): void;
    
    /**
     * Load plugin translations.
     */
    public function load_translations(): void;
    
    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts_styles(): void;
}
```

### SM\Frontend\Shortcodes
Handles all shortcode functionality for displaying sermons and related content.

```php
namespace SM\Frontend;

class Shortcodes {
    /**
     * Get the shortcodes instance.
     *
     * @return Shortcodes
     */
    public static function get_instance(): Shortcodes;
    
    /**
     * Display a list of podcast URLs.
     *
     * @param array<string, string|null> $atts Shortcode parameters.
     * @return string List or error message.
     */
    public function display_podcasts_list(array $atts): string;
    
    /**
     * Display a grid of images for series, preachers, topics or books.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Grid of images or error message.
     */
    public function display_images(array $atts): string;
    
    /**
     * Display sermons based on the provided attributes.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Sermons list or error message.
     */
    public function display_sermons(array $atts): string;
}
```

## Hooks and Filters

### Actions
- `sm_init` - Fires when the plugin is initialized
- `sm_enqueue_scripts` - Fires when scripts and styles are enqueued
- `sm_sermon_save` - Fires when a sermon is saved
- `sm_sermon_delete` - Fires when a sermon is deleted

### Filters
- `sm_sermon_content` - Filter sermon content before display
- `sm_sermon_excerpt` - Filter sermon excerpt before display
- `sm_sermon_meta` - Filter sermon meta data
- `sm_sermon_image_size` - Filter sermon image size
- `sm_sermon_shortcode_atts` - Filter shortcode attributes

## Models

### SM\Models\Sermon
Represents a sermon post type with additional meta data.

```php
namespace SM\Models;

class Sermon {
    /**
     * Get sermon audio URL.
     *
     * @return string|null
     */
    public function get_audio_url(): ?string;
    
    /**
     * Get sermon video URL.
     *
     * @return string|null
     */
    public function get_video_url(): ?string;
    
    /**
     * Get sermon series.
     *
     * @return array<\WP_Term>
     */
    public function get_series(): array;
    
    /**
     * Get sermon preacher.
     *
     * @return array<\WP_Term>
     */
    public function get_preacher(): array;
}
```

## Usage Examples

### Basic Shortcode Usage
```php
// Display latest sermons
[sermons per_page="10" order="DESC" orderby="date"]

// Display sermon series images
[sermon_images display="series" columns="3"]

// Display podcast links
[list_podcasts include="itunes,android"]
```

### Template Integration
```php
// Get sermon data in template
$sermon = new SM\Models\Sermon(get_the_ID());
$audio_url = $sermon->get_audio_url();
$series = $sermon->get_series();
```

### Custom Shortcode
```php
add_shortcode('custom_sermon_list', function($atts) {
    $args = shortcode_atts([
        'per_page' => 5,
        'order' => 'DESC',
        'orderby' => 'date'
    ], $atts);
    
    return SM\Frontend\Shortcodes::get_instance()->display_sermons($args);
});
``` 