# Shortcodes Documentation

## Overview
Sermonator provides several shortcodes to display sermons and related content throughout your WordPress site. These shortcodes can be used in posts, pages, widgets, or theme templates.

## Available Shortcodes

### [sermons]
Displays a list of sermons with customizable parameters.

#### Parameters
- `per_page` (int) - Number of sermons to display per page (default: 10)
- `order` (string) - Sort order: 'ASC' or 'DESC' (default: 'DESC')
- `orderby` (string) - Sort by: 'date', 'title', 'series', 'preacher' (default: 'date')
- `series` (string) - Filter by series slug
- `preacher` (string) - Filter by preacher slug
- `topic` (string) - Filter by topic slug
- `book` (string) - Filter by book slug
- `pagination` (bool) - Enable/disable pagination (default: true)
- `show_meta` (bool) - Show sermon meta information (default: true)
- `show_excerpt` (bool) - Show sermon excerpt (default: true)
- `show_image` (bool) - Show sermon image (default: true)

#### Examples
```php
// Display latest 5 sermons
[sermons per_page="5"]

// Display sermons from a specific series
[sermons series="gospel-of-john"]

// Display sermons by a specific preacher
[sermons preacher="john-doe"]

// Display sermons with custom sorting
[sermons orderby="title" order="ASC"]

// Display sermons without pagination
[sermons pagination="false"]
```

### [sermon_images]
Displays a grid of images for series, preachers, topics, or books.

#### Parameters
- `display` (string) - What to display: 'series', 'preacher', 'topic', 'book' (required)
- `columns` (int) - Number of columns in the grid (default: 3)
- `size` (string) - Image size: 'thumbnail', 'medium', 'large', 'full' (default: 'medium')
- `link` (bool) - Link images to their respective archives (default: true)
- `show_title` (bool) - Show title below images (default: true)
- `show_count` (bool) - Show sermon count (default: true)
- `order` (string) - Sort order: 'ASC' or 'DESC' (default: 'ASC')
- `orderby` (string) - Sort by: 'name', 'count', 'id' (default: 'name')

#### Examples
```php
// Display series images in 4 columns
[sermon_images display="series" columns="4"]

// Display preacher images with custom size
[sermon_images display="preacher" size="large"]

// Display topics without titles
[sermon_images display="topic" show_title="false"]

// Display books sorted by count
[sermon_images display="book" orderby="count" order="DESC"]
```

### [list_podcasts]
Displays a list of podcast subscription links.

#### Parameters
- `include` (string) - Comma-separated list of services to include
- `exclude` (string) - Comma-separated list of services to exclude
- `style` (string) - Display style: 'list', 'buttons', 'icons' (default: 'list')
- `show_labels` (bool) - Show service labels (default: true)
- `show_icons` (bool) - Show service icons (default: true)

Available services: itunes, android, spotify, google, stitcher, rss

#### Examples
```php
// Display all podcast links
[list_podcasts]

// Display specific podcast services
[list_podcasts include="itunes,spotify"]

// Display as buttons
[list_podcasts style="buttons"]

// Display without labels
[list_podcasts show_labels="false"]
```

## Advanced Usage

### Combining Shortcodes
Shortcodes can be combined with other WordPress shortcodes or HTML:

```php
<div class="sermon-section">
    <h2>Latest Sermons</h2>
    [sermons per_page="3"]
    
    <h2>Browse by Series</h2>
    [sermon_images display="series"]
    
    <h2>Subscribe to Podcast</h2>
    [list_podcasts style="buttons"]
</div>
```

### Using in Theme Templates
Shortcodes can be used in theme templates using the `do_shortcode()` function:

```php
<?php
// In your theme template
echo do_shortcode('[sermons per_page="5" show_excerpt="false"]');
?>
```

### Custom Styling
All shortcodes output HTML with semantic classes that can be styled using CSS:

```css
/* Style sermon list */
.sermon-list {
    margin: 20px 0;
}

.sermon-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

/* Style image grid */
.sermon-images-grid {
    display: grid;
    gap: 20px;
}

.sermon-image-item {
    text-align: center;
}

/* Style podcast links */
.podcast-links {
    display: flex;
    gap: 10px;
}

.podcast-link {
    padding: 8px 16px;
    border-radius: 4px;
}
```

## Troubleshooting

### Common Issues

1. **Shortcode not displaying**
   - Check if the shortcode is properly registered
   - Verify there are no syntax errors in the shortcode parameters
   - Ensure the content exists (e.g., sermons are published)

2. **Images not showing**
   - Verify image sizes are properly registered
   - Check if featured images are set for sermons
   - Ensure the image directory has proper permissions

3. **Pagination not working**
   - Verify the `pagination` parameter is set to "true"
   - Check if there are enough items to paginate
   - Ensure the theme supports WordPress pagination

### Debugging Tips

1. Enable WordPress debug mode:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

2. Check the shortcode output:
```php
<?php
$shortcode = '[sermons per_page="5"]';
echo '<pre>';
print_r(do_shortcode($shortcode));
echo '</pre>';
?>
```

3. Verify shortcode registration:
```php
<?php
global $shortcode_tags;
print_r($shortcode_tags);
?>
``` 