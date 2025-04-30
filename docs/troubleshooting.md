# Troubleshooting Guide

## Common Issues

### Installation Issues

1. **Plugin Not Activating**
   - Check PHP version (requires PHP 7.4 or higher)
   - Verify WordPress version (requires 5.6 or higher)
   - Check for plugin conflicts
   - Review error logs

2. **Missing Dependencies**
   - Install required PHP extensions
   - Update WordPress core
   - Check server requirements
   - Verify file permissions

3. **Database Errors**
   - Check database user permissions
   - Verify database connection
   - Review database error logs
   - Check table structure

### Shortcode Issues

1. **Shortcode Not Displaying**
   ```php
   // Check shortcode registration
   global $shortcode_tags;
   print_r($shortcode_tags);
   
   // Verify shortcode output
   echo do_shortcode('[sermons]');
   ```

2. **Shortcode Parameters Not Working**
   - Check parameter names
   - Verify parameter values
   - Review shortcode documentation
   - Test with minimal parameters

3. **Shortcode Cache Issues**
   ```php
   // Clear shortcode cache
   wp_cache_flush();
   
   // Disable caching temporarily
   define('WP_CACHE', false);
   ```

### Template Issues

1. **Template Not Loading**
   ```php
   // Check template hierarchy
   global $template;
   error_log('Current template: ' . $template);
   
   // Verify template location
   locate_template('sermon/single.php', true);
   ```

2. **Template Overrides Not Working**
   - Check file permissions
   - Verify file location
   - Clear template cache
   - Check theme compatibility

3. **Template Functions Not Working**
   ```php
   // Check function availability
   if (function_exists('sm_the_sermon_meta')) {
       sm_the_sermon_meta();
   }
   
   // Debug function output
   ob_start();
   sm_the_sermon_meta();
   $output = ob_get_clean();
   error_log($output);
   ```

### Media Issues

1. **Audio/Video Not Playing**
   - Check file permissions
   - Verify file format
   - Test different browsers
   - Check media player settings

2. **Images Not Displaying**
   ```php
   // Check image URL
   $image_url = wp_get_attachment_url(get_post_thumbnail_id());
   error_log('Image URL: ' . $image_url);
   
   // Verify image size
   $image_size = get_post_thumbnail_id() ? wp_get_attachment_image_src(get_post_thumbnail_id(), 'full') : false;
   error_log('Image size: ' . print_r($image_size, true));
   ```

3. **Podcast Feed Issues**
   - Verify feed URL
   - Check feed format
   - Validate feed content
   - Test with podcast clients

## Debugging

### Enabling Debug Mode

1. **WordPress Debug**
   ```php
   // wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

2. **Plugin Debug**
   ```php
   // Add to theme's functions.php
   add_filter('sm_debug', '__return_true');
   ```

3. **Query Debug**
   ```php
   // Add to theme's functions.php
   add_action('pre_get_posts', function($query) {
       if ($query->is_main_query()) {
           error_log('Query: ' . $query->request);
       }
   });
   ```

### Common Error Messages

1. **PHP Errors**
   - Parse errors: Check syntax
   - Fatal errors: Review error logs
   - Notice/Warning: Check variable usage
   - Deprecated: Update code

2. **Database Errors**
   - Connection errors: Check credentials
   - Query errors: Review SQL
   - Table errors: Check structure
   - Permission errors: Verify access

3. **JavaScript Errors**
   - Console errors: Check browser console
   - AJAX errors: Review requests
   - Script loading: Verify paths
   - Dependency errors: Check order

## Performance Issues

### Slow Loading

1. **Database Optimization**
   ```php
   // Optimize sermon queries
   add_filter('sm_sermon_query_args', function($args) {
       $args['no_found_rows'] = true;
       $args['update_post_term_cache'] = false;
       $args['update_post_meta_cache'] = false;
       return $args;
   });
   ```

2. **Caching Implementation**
   ```php
   // Add caching to shortcodes
   add_filter('sm_shortcode_cache', '__return_true');
   
   // Set cache duration
   add_filter('sm_cache_duration', function() {
       return HOUR_IN_SECONDS;
   });
   ```

3. **Asset Optimization**
   - Minify CSS/JS
   - Combine files
   - Use CDN
   - Implement lazy loading

### Memory Issues

1. **Memory Limit**
   ```php
   // Increase memory limit
   define('WP_MEMORY_LIMIT', '256M');
   
   // Check memory usage
   error_log('Memory usage: ' . memory_get_usage());
   ```

2. **Query Optimization**
   ```php
   // Limit query results
   add_filter('sm_sermon_query_args', function($args) {
       $args['posts_per_page'] = 10;
       return $args;
   });
   ```

3. **Asset Loading**
   - Load scripts in footer
   - Defer non-critical JS
   - Optimize images
   - Use sprite sheets

## Security Issues

### Common Vulnerabilities

1. **Input Validation**
   ```php
   // Sanitize shortcode attributes
   add_filter('sm_shortcode_atts', function($atts) {
       return array_map('sanitize_text_field', $atts);
   });
   ```

2. **Output Escaping**
   ```php
   // Escape shortcode output
   add_filter('sm_shortcode_output', 'esc_html');
   ```

3. **Nonce Verification**
   ```php
   // Add nonce to forms
   wp_nonce_field('sm_action', 'sm_nonce');
   
   // Verify nonce
   if (!wp_verify_nonce($_POST['sm_nonce'], 'sm_action')) {
       wp_die('Invalid nonce');
   }
   ```

### File Permissions

1. **Directory Permissions**
   ```bash
   # Set correct permissions
   chmod 755 wp-content/plugins/sermon-manager
   chmod 644 wp-content/plugins/sermon-manager/*.php
   ```

2. **Upload Permissions**
   ```php
   // Check upload directory
   $upload_dir = wp_upload_dir();
   error_log('Upload path: ' . $upload_dir['path']);
   error_log('Upload URL: ' . $upload_dir['url']);
   ```

3. **File Access**
   - Restrict direct access
   - Use .htaccess rules
   - Implement file checks
   - Monitor file changes

## Getting Help

### Support Resources

1. **Documentation**
   - [Getting Started](getting-started.md)
   - [API Reference](api/README.md)
   - [Shortcodes](shortcodes.md)
   - [Templates](templates.md)

2. **Community Support**
   - WordPress.org forums
   - GitHub issues
   - Slack channel
   - Stack Overflow

3. **Professional Support**
   - Premium support
   - Custom development
   - Consulting services
   - Training sessions

### Reporting Issues

1. **Bug Reports**
   - Describe the issue
   - Provide steps to reproduce
   - Include error messages
   - Share environment details

2. **Feature Requests**
   - Explain the need
   - Provide use cases
   - Suggest implementation
   - Consider alternatives

3. **Security Reports**
   - Use secure channels
   - Provide details
   - Allow time for fix
   - Follow disclosure policy 