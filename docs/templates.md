# Templates Documentation

## Theme Integration

### Template Hierarchy
Sermon Manager follows WordPress template hierarchy. You can override any template by placing it in your theme directory:

```
your-theme/
├── sermon/
│   ├── archive.php
│   ├── single.php
│   ├── taxonomy-series.php
│   ├── taxonomy-preacher.php
│   ├── taxonomy-topic.php
│   └── taxonomy-book.php
└── sermon-manager/
    ├── content-sermon.php
    ├── loop-sermon.php
    ├── player-audio.php
    └── player-video.php
```

### Default Templates
The plugin includes these default templates:
- `archive.php` - Sermon archive page
- `single.php` - Single sermon page
- `content-sermon.php` - Sermon content template
- `loop-sermon.php` - Sermon loop template
- `player-audio.php` - Audio player template
- `player-video.php` - Video player template

## Custom Templates

### Creating Custom Templates
1. Copy the template from the plugin's `templates` directory
2. Place it in your theme's `sermon-manager` directory
3. Modify the template as needed

Example:
```php
// your-theme/sermon-manager/content-sermon.php
<?php
/**
 * The template for displaying sermon content
 */
?>

<article id="sermon-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        
        <div class="entry-meta">
            <?php
            // Display sermon meta
            sm_the_sermon_meta();
            ?>
        </div>
    </header>

    <div class="entry-content">
        <?php
        // Display sermon media
        sm_the_sermon_media();
        
        // Display sermon content
        the_content();
        ?>
    </div>
</article>
```

### Template Functions

#### Display Functions
```php
// Display sermon media (audio/video)
sm_the_sermon_media();

// Display sermon meta information
sm_the_sermon_meta();

// Display sermon series
sm_the_sermon_series();

// Display sermon preacher
sm_the_sermon_preacher();

// Display sermon topics
sm_the_sermon_topics();

// Display sermon books
sm_the_sermon_books();
```

#### Get Functions
```php
// Get sermon audio URL
sm_get_sermon_audio_url();

// Get sermon video URL
sm_get_sermon_video_url();

// Get sermon series
sm_get_sermon_series();

// Get sermon preacher
sm_get_sermon_preacher();

// Get sermon topics
sm_get_sermon_topics();

// Get sermon books
sm_get_sermon_books();
```

## Customizing Display

### CSS Classes
The plugin adds these classes to sermon elements:
- `.sermon` - Main sermon container
- `.sermon-media` - Media container
- `.sermon-meta` - Meta information container
- `.sermon-series` - Series container
- `.sermon-preacher` - Preacher container
- `.sermon-topics` - Topics container
- `.sermon-books` - Books container

### Example CSS
```css
/* Style sermon container */
.sermon {
    margin-bottom: 2em;
    padding: 1em;
    border: 1px solid #eee;
}

/* Style media player */
.sermon-media {
    margin: 1em 0;
}

/* Style meta information */
.sermon-meta {
    font-size: 0.9em;
    color: #666;
}

/* Style taxonomy links */
.sermon-series a,
.sermon-preacher a,
.sermon-topics a,
.sermon-books a {
    text-decoration: none;
    color: #0073aa;
}
```

## Advanced Customization

### Custom Template Parts
You can create custom template parts for specific elements:

```php
// your-theme/sermon-manager/sermon-meta.php
<?php
/**
 * Custom sermon meta template
 */
?>

<div class="sermon-meta">
    <span class="sermon-date">
        <?php echo get_the_date(); ?>
    </span>
    
    <span class="sermon-duration">
        <?php echo sm_get_sermon_duration(); ?>
    </span>
    
    <span class="sermon-views">
        <?php echo sm_get_sermon_views(); ?> views
    </span>
</div>
```

### Custom Archive Query
Modify the sermon archive query:

```php
// your-theme/functions.php
add_action('pre_get_posts', 'custom_sermon_archive_query');

function custom_sermon_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('sermon')) {
        $query->set('posts_per_page', 12);
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }
}
```

### Custom Taxonomy Templates
Create custom templates for sermon taxonomies:

```php
// your-theme/sermon/taxonomy-series.php
<?php
/**
 * Series archive template
 */

get_header(); ?>

<div class="series-archive">
    <header class="page-header">
        <h1 class="page-title">
            <?php single_term_title('Series: '); ?>
        </h1>
        
        <?php if (term_description()) : ?>
            <div class="taxonomy-description">
                <?php echo term_description(); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            get_template_part('sermon-manager/content', 'sermon');
        endwhile;
        
        the_posts_pagination();
    else :
        get_template_part('content', 'none');
    endif;
    ?>
</div>

<?php get_footer(); ?>
```

## Troubleshooting

### Common Issues

1. **Template Not Loading**
   - Verify template file is in correct location
   - Check file permissions
   - Clear WordPress cache

2. **Custom CSS Not Applying**
   - Check CSS specificity
   - Verify CSS file is enqueued
   - Clear browser cache

3. **Template Functions Not Working**
   - Verify function exists
   - Check function parameters
   - Ensure proper hook usage

### Debugging Tips

1. Enable template debugging:
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
```

2. Check template loading:
```php
// functions.php
add_action('template_include', function($template) {
    error_log('Loading template: ' . $template);
    return $template;
});
```

3. Verify template hierarchy:
```php
// functions.php
add_action('wp', function() {
    global $template;
    error_log('Current template: ' . $template);
});
``` 