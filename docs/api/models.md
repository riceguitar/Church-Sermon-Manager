# Models Documentation

## SM\Models\Sermon

The Sermon model represents a sermon post type with additional meta data and functionality.

### Properties

```php
/**
 * @var int $id The sermon post ID
 */
protected $id;

/**
 * @var array $meta Cache for sermon meta data
 */
protected $meta = [];

/**
 * @var array $taxonomies Cache for sermon taxonomies
 */
protected $taxonomies = [];
```

### Methods

#### Constructor
```php
/**
 * Initialize the sermon model.
 *
 * @param int $post_id The sermon post ID
 */
public function __construct(int $post_id);
```

#### Getters

```php
/**
 * Get the sermon ID.
 *
 * @return int
 */
public function get_id(): int;

/**
 * Get the sermon title.
 *
 * @return string
 */
public function get_title(): string;

/**
 * Get the sermon content.
 *
 * @return string
 */
public function get_content(): string;

/**
 * Get the sermon excerpt.
 *
 * @return string
 */
public function get_excerpt(): string;

/**
 * Get the sermon date.
 *
 * @return string
 */
public function get_date(): string;

/**
 * Get the sermon audio URL.
 *
 * @return string|null
 */
public function get_audio_url(): ?string;

/**
 * Get the sermon video URL.
 *
 * @return string|null
 */
public function get_video_url(): ?string;

/**
 * Get the sermon duration.
 *
 * @return string|null
 */
public function get_duration(): ?string;

/**
 * Get the sermon views count.
 *
 * @return int
 */
public function get_views(): int;

/**
 * Get the sermon download count.
 *
 * @return int
 */
public function get_downloads(): int;
```

#### Taxonomy Methods

```php
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

/**
 * Get sermon topics.
 *
 * @return array<\WP_Term>
 */
public function get_topics(): array;

/**
 * Get sermon books.
 *
 * @return array<\WP_Term>
 */
public function get_books(): array;
```

#### Meta Methods

```php
/**
 * Get sermon meta value.
 *
 * @param string $key Meta key
 * @param mixed $default Default value
 * @return mixed
 */
public function get_meta(string $key, $default = null);

/**
 * Update sermon meta value.
 *
 * @param string $key Meta key
 * @param mixed $value Meta value
 * @return bool
 */
public function update_meta(string $key, $value): bool;

/**
 * Delete sermon meta value.
 *
 * @param string $key Meta key
 * @return bool
 */
public function delete_meta(string $key): bool;
```

#### Utility Methods

```php
/**
 * Check if sermon has audio.
 *
 * @return bool
 */
public function has_audio(): bool;

/**
 * Check if sermon has video.
 *
 * @return bool
 */
public function has_video(): bool;

/**
 * Get sermon permalink.
 *
 * @return string
 */
public function get_permalink(): string;

/**
 * Get sermon edit link.
 *
 * @return string
 */
public function get_edit_link(): string;
```

## SM\Models\Series

The Series model represents a sermon series taxonomy term.

### Properties

```php
/**
 * @var int $id The series term ID
 */
protected $id;

/**
 * @var array $meta Cache for series meta data
 */
protected $meta = [];
```

### Methods

```php
/**
 * Get series ID.
 *
 * @return int
 */
public function get_id(): int;

/**
 * Get series name.
 *
 * @return string
 */
public function get_name(): string;

/**
 * Get series description.
 *
 * @return string
 */
public function get_description(): string;

/**
 * Get series image URL.
 *
 * @return string|null
 */
public function get_image_url(): ?string;

/**
 * Get series sermons count.
 *
 * @return int
 */
public function get_sermons_count(): int;

/**
 * Get series sermons.
 *
 * @param array $args Query arguments
 * @return array<\SM\Models\Sermon>
 */
public function get_sermons(array $args = []): array;
```

## SM\Models\Preacher

The Preacher model represents a sermon preacher taxonomy term.

### Properties

```php
/**
 * @var int $id The preacher term ID
 */
protected $id;

/**
 * @var array $meta Cache for preacher meta data
 */
protected $meta = [];
```

### Methods

```php
/**
 * Get preacher ID.
 *
 * @return int
 */
public function get_id(): int;

/**
 * Get preacher name.
 *
 * @return string
 */
public function get_name(): string;

/**
 * Get preacher description.
 *
 * @return string
 */
public function get_description(): string;

/**
 * Get preacher image URL.
 *
 * @return string|null
 */
public function get_image_url(): ?string;

/**
 * Get preacher sermons count.
 *
 * @return int
 */
public function get_sermons_count(): int;

/**
 * Get preacher sermons.
 *
 * @param array $args Query arguments
 * @return array<\SM\Models\Sermon>
 */
public function get_sermons(array $args = []): array;
```

## Usage Examples

### Basic Usage
```php
// Get sermon by ID
$sermon = new SM\Models\Sermon(123);

// Get sermon details
$title = $sermon->get_title();
$audio_url = $sermon->get_audio_url();
$series = $sermon->get_series();

// Get series details
$series = new SM\Models\Series(456);
$series_name = $series->get_name();
$sermons = $series->get_sermons();
```

### Advanced Usage
```php
// Get sermons with custom query
$series = new SM\Models\Series(456);
$sermons = $series->get_sermons([
    'posts_per_page' => 10,
    'orderby' => 'date',
    'order' => 'DESC'
]);

// Update sermon meta
$sermon = new SM\Models\Sermon(123);
$sermon->update_meta('custom_field', 'value');

// Get preacher sermons
$preacher = new SM\Models\Preacher(789);
$sermons = $preacher->get_sermons([
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'ASC'
]);
```

## Best Practices

1. **Caching**
   - Use object caching for frequently accessed data
   - Implement transient caching for expensive queries
   - Cache taxonomy relationships

2. **Performance**
   - Limit the number of queries
   - Use proper indexing
   - Implement lazy loading
   - Cache meta data

3. **Security**
   - Validate input data
   - Sanitize output data
   - Use nonces for forms
   - Check user capabilities

4. **Error Handling**
   - Use try-catch blocks
   - Log errors properly
   - Provide fallback values
   - Handle edge cases 