<?php

declare(strict_types=1);

namespace SM\Frontend;

use SM\Core\Plugin;

/**
 * Handles the registration and rendering of shortcodes.
 *
 * @package SM\Frontend
 */
class Shortcodes {
    /**
     * The single instance of the class.
     *
     * @var Shortcodes
     */
    protected static $instance = null;

    /**
     * Main Shortcodes Instance.
     *
     * Ensures only one instance of Shortcodes is loaded or can be loaded.
     *
     * @return Shortcodes - Main instance.
     */
    public static function get_instance(): Shortcodes {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Shortcodes constructor.
     */
    protected function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    protected function init_hooks(): void {
        add_action('init', [$this, 'register_shortcodes']);
    }

    /**
     * Register all shortcodes.
     */
    public function register_shortcodes(): void {
        // List podcast buttons.
        add_shortcode('list_podcasts', [$this, 'display_podcasts_list']);
        // List all series or speakers in a simple unordered list.
        add_shortcode('list_sermons', [$this, 'display_sermons_list']);
        // Display all series or speakers in a grid of images.
        add_shortcode('sermon_images', [$this, 'display_images']);
        // Display the latest sermon series image (optional - by service type).
        add_shortcode('latest_series', [$this, 'display_latest_series_image']);
        // Main shortcode.
        add_shortcode('sermons', [$this, 'display_sermons']);
        // Add alternative shortcode for case when Sermon Browser is used at the same time.
        add_shortcode('sermons_sm', [$this, 'display_sermons']);
        // Filtering shortcode.
        add_shortcode('sermon_sort_fields', [$this, 'display_sermon_sorting']);
        // Display latest sermons shortcode.
        add_shortcode('latest_sermon', [$this, 'display_latest_sermon']);

        // Load deprecated shortcode aliasing.
        $this->legacy_shortcodes();
    }

    /**
     * Register legacy shortcodes for compatibility.
     */
    public function legacy_shortcodes(): void {
        add_shortcode('list-sermons', [$this, 'display_sermons_list']);
        add_shortcode('sermon-images', [$this, 'display_images']);
        add_shortcode('latest_sermon', [$this, 'display_latest_sermon']);
    }

    /**
     * Display a list of podcast URLs specified on the podcast settings page.
     *
     * @param array<string, string|null> $atts Shortcode parameters.
     * @return string List or error message.
     */
    public function display_podcasts_list(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Default options.
        $args = [
            'include' => 'itunes, android, overcast',
            'exclude' => null,
        ];

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'list_podcasts');

        // Remove spaces so we can get clean array values.
        if ($args['include']) {
            $args['include'] = str_replace(' ', '', $args['include']);
        }
        
        if ($args['exclude']) {
            $args['exclude'] = str_replace(' ', '', $args['exclude']);
        }

        // Convert comma-separated shortcode attributes to array.
        $services_to_include = $args['include'] ? explode(',', $args['include']) : [];
        $services_to_exclude = $args['exclude'] ? explode(',', $args['exclude']) : [];

        // Remove excluded services.
        $services = array_diff($services_to_include, $services_to_exclude);

        if (!SM_OB_ENABLED) {
            return '';
        }

        // Start output.
        ob_start();

        if (count($services) > 0) {
            echo '<ul class="subscribe">';
            foreach ($services as $key) {
                // Get URL.
                $url = get_option('sermonmanager_podcast_url_' . esc_attr($key), true);

                // Ensure URL isn't empty.
                if (!empty($url)) {
                    // Set default labels.
                    $label = 'itunes' === $key ? 'Subscribe using iTunes' : 'Subscribe using ' . ucwords($key);

                    // Allow custom labels.
                    $label = apply_filters('wpfc_podcast_label_' . esc_attr($key), $label);

                    // Print link.
                    echo '<li><a class="' . esc_attr($key) . '" title="' . esc_attr($label) . '" href="' . esc_url($url) . '" target="_blank" rel="noopener">' . $label . '</a></li>';
                }
            }
            echo '</ul>';
        } else {
            echo 'No podcast services have been specified. Please check your include/exclude settings.';
        }

        // Return output.
        return ob_get_clean();
    }

    /**
     * Display an unordered list of series, preachers, topics or books.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string List or error message.
     */
    public function display_sermons_list(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Unquote.
        foreach ($atts as &$att) {
            $att = $this->unquote($att);
        }

        // Default options.
        $args = [
            'display' => 'series',
            'order'   => 'ASC',
            'orderby' => 'name',
        ];

        // For compatibility.
        if (!empty($atts['tax'])) {
            $atts['display'] = $atts['tax'];
            unset($atts['tax']);
        }

        // For compatibility.
        if (!empty($atts['taxonomy'])) {
            $atts['display'] = $atts['taxonomy'];
            unset($atts['taxonomy']);
        }

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'list_sermons');

        // Check if we are using a SM taxonomy, and if we are, convert to valid taxonomy name.
        if ($this->convert_taxonomy_name($args['display'], true)) {
            $args['display'] = $this->convert_taxonomy_name($args['display'], false);
        } elseif (!$this->convert_taxonomy_name($args['display'], false)) {
            return '<strong>Error: Invalid "list" parameter.</strong><br> Possible values are: "series", "preachers", "topics" and "books".<br> You entered: "<em>' . $args['display'] . '</em>"';
        }

        $query_args = [
            'taxonomy' => $args['display'],
            'orderby'  => $args['orderby'],
            'order'    => $args['order'],
        ];

        if ('date' === $query_args['orderby']) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'sermon_date';
            $query_args['meta_compare'] = '<=';
            $query_args['meta_value_num'] = time();
        }

        // Get items.
        $terms = get_terms($query_args);

        if (count($terms) > 0) {
            // Sort books by order.
            if ('wpfc_bible_book' === $args['display'] && 'book' === $args['orderby']) {
                // Book order.
                $books = [
                    'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy',
                    'Joshua', 'Judges', 'Ruth', '1 Samuel', '2 Samuel',
                    '1 Kings', '2 Kings', '1 Chronicles', '2 Chronicles',
                    'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs',
                    'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah',
                    'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel',
                    'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk',
                    'Zephaniah', 'Haggai', 'Zechariah', 'Malachi', 'Matthew',
                    'Mark', 'Luke', 'John', 'Acts', 'Romans', '1 Corinthians',
                    '2 Corinthians', 'Galatians', 'Ephesians', 'Philippians',
                    'Colossians', '1 Thessalonians', '2 Thessalonians',
                    '1 Timothy', '2 Timothy', 'Titus', 'Philemon', 'Hebrews',
                    'James', '1 Peter', '2 Peter', '1 John', '2 John',
                    '3 John', 'Jude', 'Revelation'
                ];

                usort($terms, function($a, $b) use ($books) {
                    return array_search($a->name, $books) - array_search($b->name, $books);
                });
            }

            // Start output.
            ob_start();
            echo '<ul class="sermon-taxonomy-list">';
            foreach ($terms as $term) {
                echo '<li><a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a> (' . esc_html($term->count) . ')</li>';
            }
            echo '</ul>';
            return ob_get_clean();
        }

        return 'No items found.';
    }

    /**
     * Remove quotes from a string.
     *
     * @param string $string The string to unquote.
     * @return string The unquoted string.
     */
    private function unquote(string $string): string {
        return str_replace(['"', "'"], '', $string);
    }

    /**
     * Convert taxonomy name to valid WordPress taxonomy name.
     *
     * @param string $name The name to convert.
     * @param bool $new_name Whether to convert to new name.
     * @return bool|string The converted name or false if invalid.
     */
    private function convert_taxonomy_name(string $name, bool $new_name) {
        $taxonomies = [
            'series'    => 'wpfc_sermon_series',
            'preachers' => 'wpfc_preacher',
            'topics'    => 'wpfc_sermon_topics',
            'books'     => 'wpfc_bible_book',
        ];

        if ($new_name) {
            return isset($taxonomies[$name]);
        }

        return $taxonomies[$name] ?? false;
    }

    /**
     * Render a shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @param string $tag Shortcode tag.
     * @return string Rendered shortcode output.
     */
    public function render_shortcode(array $atts, string $content, string $tag): string {
        // Implementation will be moved from the old class
        return '';
    }

    /**
     * Display a grid of images for series, preachers, topics or books.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Grid of images or error message.
     */
    public function display_images(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Unquote.
        foreach ($atts as &$att) {
            $att = $this->unquote($att);
        }

        // Default options.
        $args = [
            'display' => 'series',
            'order'   => 'ASC',
            'orderby' => 'name',
            'size'    => 'sermon_medium',
            'columns' => '3',
        ];

        // For compatibility.
        if (!empty($atts['tax'])) {
            $atts['display'] = $atts['tax'];
            unset($atts['tax']);
        }

        // For compatibility.
        if (!empty($atts['taxonomy'])) {
            $atts['display'] = $atts['taxonomy'];
            unset($atts['taxonomy']);
        }

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'sermon_images');

        // Check if we are using a SM taxonomy, and if we are, convert to valid taxonomy name.
        if ($this->convert_taxonomy_name($args['display'], true)) {
            $args['display'] = $this->convert_taxonomy_name($args['display'], false);
        } elseif (!$this->convert_taxonomy_name($args['display'], false)) {
            return '<strong>Error: Invalid "display" parameter.</strong><br> Possible values are: "series", "preachers", "topics" and "books".<br> You entered: "<em>' . $args['display'] . '</em>"';
        }

        $query_args = [
            'taxonomy' => $args['display'],
            'orderby'  => $args['orderby'],
            'order'    => $args['order'],
        ];

        if ('date' === $query_args['orderby']) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'sermon_date';
            $query_args['meta_compare'] = '<=';
            $query_args['meta_value_num'] = time();
        }

        // Get items.
        $terms = get_terms($query_args);

        if (count($terms) > 0) {
            // Sort books by order.
            if ('wpfc_bible_book' === $args['display'] && 'book' === $args['orderby']) {
                // Book order.
                $books = [
                    'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy',
                    'Joshua', 'Judges', 'Ruth', '1 Samuel', '2 Samuel',
                    '1 Kings', '2 Kings', '1 Chronicles', '2 Chronicles',
                    'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs',
                    'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah',
                    'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel',
                    'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk',
                    'Zephaniah', 'Haggai', 'Zechariah', 'Malachi', 'Matthew',
                    'Mark', 'Luke', 'John', 'Acts', 'Romans', '1 Corinthians',
                    '2 Corinthians', 'Galatians', 'Ephesians', 'Philippians',
                    'Colossians', '1 Thessalonians', '2 Thessalonians',
                    '1 Timothy', '2 Timothy', 'Titus', 'Philemon', 'Hebrews',
                    'James', '1 Peter', '2 Peter', '1 John', '2 John',
                    '3 John', 'Jude', 'Revelation'
                ];

                usort($terms, function($a, $b) use ($books) {
                    return array_search($a->name, $books) - array_search($b->name, $books);
                });
            }

            // Start output.
            ob_start();
            echo '<div class="sermon-taxonomy-grid columns-' . esc_attr($args['columns']) . '">';
            foreach ($terms as $term) {
                $image_id = get_term_meta($term->term_id, 'sermon_image', true);
                $image = wp_get_attachment_image($image_id, $args['size']);
                
                if (!$image) {
                    $image = '<img src="' . esc_url(SM_URL . 'assets/images/no-image.png') . '" alt="' . esc_attr($term->name) . '">';
                }

                echo '<div class="sermon-taxonomy-item">';
                echo '<a href="' . esc_url(get_term_link($term)) . '">';
                echo $image;
                echo '<span class="sermon-taxonomy-title">' . esc_html($term->name) . '</span>';
                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
            return ob_get_clean();
        }

        return 'No items found.';
    }

    /**
     * Display the latest sermon series image.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Image or error message.
     */
    public function display_latest_series_image(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Default options.
        $args = [
            'size'    => 'sermon_medium',
            'service' => null,
        ];

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'latest_series');

        // Get latest sermon.
        $query_args = [
            'post_type'      => 'wpfc_sermon',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($args['service']) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'wpfc_service_type',
                    'field'    => 'slug',
                    'terms'    => $args['service'],
                ],
            ];
        }

        $latest_sermon = get_posts($query_args);

        if (empty($latest_sermon)) {
            return 'No sermons found.';
        }

        $sermon = $latest_sermon[0];
        $series = wp_get_post_terms($sermon->ID, 'wpfc_sermon_series');

        if (empty($series)) {
            return 'No series found.';
        }

        $series = $series[0];
        $image_id = get_term_meta($series->term_id, 'sermon_image', true);
        $image = wp_get_attachment_image($image_id, $args['size']);

        if (!$image) {
            $image = '<img src="' . esc_url(SM_URL . 'assets/images/no-image.png') . '" alt="' . esc_attr($series->name) . '">';
        }

        ob_start();
        echo '<div class="latest-series-image">';
        echo '<a href="' . esc_url(get_term_link($series)) . '">';
        echo $image;
        echo '<span class="series-title">' . esc_html($series->name) . '</span>';
        echo '</a>';
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Display sermons based on the provided attributes.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Sermons list or error message.
     */
    public function display_sermons(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Default options.
        $args = [
            'order'      => 'DESC',
            'orderby'    => 'date',
            'per_page'   => '10',
            'paged'      => get_query_var('paged') ? get_query_var('paged') : 1,
            'series'     => null,
            'preacher'   => null,
            'topic'      => null,
            'book'       => null,
            'service'    => null,
            'year'       => null,
            'month'      => null,
            'day'        => null,
            'hide_pagination' => false,
        ];

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'sermons');

        // Build query args.
        $query_args = [
            'post_type'      => 'wpfc_sermon',
            'posts_per_page' => $args['per_page'],
            'paged'          => $args['paged'],
            'orderby'        => $args['orderby'],
            'order'          => $args['order'],
        ];

        // Add taxonomy queries if specified.
        $tax_queries = [];

        if ($args['series']) {
            $tax_queries[] = [
                'taxonomy' => 'wpfc_sermon_series',
                'field'    => 'slug',
                'terms'    => $args['series'],
            ];
        }

        if ($args['preacher']) {
            $tax_queries[] = [
                'taxonomy' => 'wpfc_preacher',
                'field'    => 'slug',
                'terms'    => $args['preacher'],
            ];
        }

        if ($args['topic']) {
            $tax_queries[] = [
                'taxonomy' => 'wpfc_sermon_topics',
                'field'    => 'slug',
                'terms'    => $args['topic'],
            ];
        }

        if ($args['book']) {
            $tax_queries[] = [
                'taxonomy' => 'wpfc_bible_book',
                'field'    => 'slug',
                'terms'    => $args['book'],
            ];
        }

        if ($args['service']) {
            $tax_queries[] = [
                'taxonomy' => 'wpfc_service_type',
                'field'    => 'slug',
                'terms'    => $args['service'],
            ];
        }

        if (!empty($tax_queries)) {
            $query_args['tax_query'] = $tax_queries;
        }

        // Add date queries if specified.
        if ($args['year'] || $args['month'] || $args['day']) {
            $date_query = [];

            if ($args['year']) {
                $date_query['year'] = $args['year'];
            }

            if ($args['month']) {
                $date_query['month'] = $args['month'];
            }

            if ($args['day']) {
                $date_query['day'] = $args['day'];
            }

            $query_args['date_query'] = [$date_query];
        }

        // Get sermons.
        $sermons = new \WP_Query($query_args);

        if (!$sermons->have_posts()) {
            return 'No sermons found.';
        }

        // Start output.
        ob_start();
        echo '<div class="sermons-list">';
        while ($sermons->have_posts()) {
            $sermons->the_post();
            get_template_part('content', 'sermon');
        }
        echo '</div>';

        // Add pagination if not hidden.
        if (!$args['hide_pagination']) {
            echo '<div class="sermon-pagination">';
            echo paginate_links([
                'base'    => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format'  => '?paged=%#%',
                'current' => max(1, $args['paged']),
                'total'   => $sermons->max_num_pages,
            ]);
            echo '</div>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Display sermon sorting fields.
     *
     * @param array<string, string> $atts Shortcode parameters.
     * @return string Sorting fields or error message.
     */
    public function display_sermon_sorting(array $atts): string {
        // Enqueue scripts and styles.
        if (!defined('SM_ENQUEUE_SCRIPTS_STYLES')) {
            define('SM_ENQUEUE_SCRIPTS_STYLES', true);
        }

        // Default options.
        $args = [
            'fields' => 'series,preacher,topic,book,date',
        ];

        // Join default and user options.
        $args = shortcode_atts($args, $atts, 'sermon_sort_fields');

        // Convert fields to array.
        $fields = array_map('trim', explode(',', $args['fields']));

        // Start output.
        ob_start();
        echo '<div class="sermon-sorting">';
        echo '<form method="get" action="' . esc_url(get_permalink()) . '">';
        
        foreach ($fields as $field) {
            switch ($field) {
                case 'series':
                    $this->display_taxonomy_dropdown('wpfc_sermon_series', 'Series');
                    break;
                case 'preacher':
                    $this->display_taxonomy_dropdown('wpfc_preacher', 'Preacher');
                    break;
                case 'topic':
                    $this->display_taxonomy_dropdown('wpfc_sermon_topics', 'Topic');
                    break;
                case 'book':
                    $this->display_taxonomy_dropdown('wpfc_bible_book', 'Book');
                    break;
                case 'date':
                    $this->display_date_dropdown();
                    break;
            }
        }

        echo '<input type="submit" value="Filter">';
        echo '</form>';
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Display a taxonomy dropdown.
     *
     * @param string $taxonomy The taxonomy name.
     * @param string $label The dropdown label.
     */
    private function display_taxonomy_dropdown(string $taxonomy, string $label): void {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ]);

        if (!empty($terms)) {
            echo '<select name="' . esc_attr($taxonomy) . '">';
            echo '<option value="">' . esc_html($label) . '</option>';
            foreach ($terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
    }

    /**
     * Display a date dropdown.
     */
    private function display_date_dropdown(): void {
        global $wpdb;

        $dates = $wpdb->get_results("
            SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
            FROM {$wpdb->posts}
            WHERE post_type = 'wpfc_sermon'
            AND post_status = 'publish'
            ORDER BY post_date DESC
        ");

        if (!empty($dates)) {
            echo '<select name="date">';
            echo '<option value="">Date</option>';
            foreach ($dates as $date) {
                $month_name = date('F', mktime(0, 0, 0, (int) $date->month, 1));
                echo '<option value="' . esc_attr($date->year . '-' . $date->month) . '">' . esc_html($month_name . ' ' . $date->year) . '</option>';
            }
            echo '</select>';
        }
    }
} 