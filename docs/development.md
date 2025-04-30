# Development Documentation

## Code Standards

### PHP Standards
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use type hints and return type declarations where possible
- Document all functions and classes with PHPDoc
- Use meaningful variable and function names
- Follow PSR-4 autoloading standards

### JavaScript Standards
- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use ES6+ features where supported
- Document complex functions with JSDoc
- Use meaningful variable and function names
- Follow modular architecture

### CSS Standards
- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Use BEM methodology for class naming
- Keep specificity low
- Use CSS variables for theming
- Follow mobile-first approach

## Extending the Plugin

### Creating Custom Shortcodes
```php
// Register custom shortcode
add_shortcode('custom_sermon_list', function($atts) {
    $args = shortcode_atts([
        'per_page' => 5,
        'order' => 'DESC',
        'orderby' => 'date'
    ], $atts);
    
    return SM\Frontend\Shortcodes::get_instance()->display_sermons($args);
});

// Usage: [custom_sermon_list per_page="10"]
```

### Adding Custom Taxonomies
```php
// Register custom taxonomy
add_action('init', function() {
    register_taxonomy('custom_taxonomy', 'sermon', [
        'label' => 'Custom Taxonomy',
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'custom-taxonomy']
    ]);
});
```

### Creating Custom Widgets
```php
class Custom_Sermon_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'custom_sermon_widget',
            'Custom Sermon Widget',
            ['description' => 'Display custom sermon information']
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . $instance['title'] . $args['after_title'];
        
        // Widget content
        $sermons = new WP_Query([
            'post_type' => 'sermon',
            'posts_per_page' => $instance['number']
        ]);
        
        if ($sermons->have_posts()) {
            while ($sermons->have_posts()) {
                $sermons->the_post();
                // Display sermon information
            }
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        // Widget form fields
    }
    
    public function update($new_instance, $old_instance) {
        // Update widget settings
    }
}

// Register widget
add_action('widgets_init', function() {
    register_widget('Custom_Sermon_Widget');
});
```

## Hooks and Filters

### Available Actions
```php
// Fires when plugin is initialized
do_action('sm_init');

// Fires when scripts and styles are enqueued
do_action('sm_enqueue_scripts');

// Fires when a sermon is saved
do_action('sm_sermon_save', $post_id, $post);

// Fires when a sermon is deleted
do_action('sm_sermon_delete', $post_id);
```

### Available Filters
```php
// Filter sermon content
apply_filters('sm_sermon_content', $content);

// Filter sermon excerpt
apply_filters('sm_sermon_excerpt', $excerpt);

// Filter sermon meta data
apply_filters('sm_sermon_meta', $meta);

// Filter sermon image size
apply_filters('sm_sermon_image_size', $size);

// Filter shortcode attributes
apply_filters('sm_sermon_shortcode_atts', $atts);
```

## Creating Add-ons

### Basic Add-on Structure
```
your-addon/
├── your-addon.php
├── includes/
│   ├── class-your-addon.php
│   └── functions.php
├── assets/
│   ├── css/
│   └── js/
└── templates/
```

### Add-on Main File
```php
<?php
/**
 * Plugin Name: Your Add-on Name
 * Description: Description of your add-on
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your-website.com
 * Text Domain: your-addon
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if Sermon Manager is active
if (!class_exists('SM\Core\Plugin')) {
    return;
}

// Define constants
define('YOUR_ADDON_VERSION', '1.0.0');
define('YOUR_ADDON_PATH', plugin_dir_path(__FILE__));
define('YOUR_ADDON_URL', plugin_dir_url(__FILE__));

// Load main class
require_once YOUR_ADDON_PATH . 'includes/class-your-addon.php';

// Initialize add-on
add_action('plugins_loaded', function() {
    new Your_Addon();
});
```

### Add-on Class
```php
<?php
namespace Your_Addon;

class Your_Addon {
    public function __construct() {
        $this->init_hooks();
    }
    
    protected function init_hooks() {
        // Add hooks and filters
        add_action('sm_init', [$this, 'init']);
        add_filter('sm_sermon_meta', [$this, 'add_custom_meta']);
    }
    
    public function init() {
        // Initialize add-on
    }
    
    public function add_custom_meta($meta) {
        // Add custom meta data
        return $meta;
    }
}
```

## Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

### Pull Request Guidelines
- Follow coding standards
- Include tests for new features
- Update documentation
- Provide clear commit messages
- Reference related issues

### Testing
- Write unit tests for new features
- Test in different environments
- Verify backward compatibility
- Check performance impact

## Security

### Best Practices
- Validate and sanitize all input
- Escape all output
- Use nonces for forms
- Follow WordPress security guidelines
- Keep dependencies updated

### Common Security Issues
- SQL injection
- XSS attacks
- CSRF attacks
- File inclusion vulnerabilities
- Privilege escalation

## Performance

### Optimization Tips
- Use proper indexing
- Implement caching
- Optimize database queries
- Minify assets
- Use lazy loading

### Monitoring
- Use query monitoring
- Check server resources
- Monitor page load times
- Track memory usage
- Profile code execution

## Support

### Getting Help
- Check documentation
- Search existing issues
- Ask in support forum
- Contact developers
- Report bugs

### Reporting Issues
1. Check if issue is already reported
2. Provide detailed information
3. Include steps to reproduce
4. Add error messages
5. Share environment details 