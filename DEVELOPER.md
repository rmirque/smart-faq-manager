# Developer Documentation - Smart FAQ Manager

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Hooks & Filters](#hooks--filters)
3. [Template System](#template-system)
4. [Database Schema](#database-schema)
5. [API Functions](#api-functions)
6. [Extending the Plugin](#extending-the-plugin)
7. [Best Practices](#best-practices)

## Architecture Overview

### File Structure

```
smart-faq-manager/
├── includes/           # Core classes
│   ├── class-activator.php
│   ├── class-deactivator.php
│   ├── class-database.php
│   ├── class-cache-manager.php
│   ├── class-content-analyzer.php
│   ├── class-faq-matcher.php
│   ├── class-faq-manager.php
│   ├── class-widget-renderer.php
│   └── functions.php
├── admin/             # Admin interface
│   ├── class-admin-interface.php
│   ├── class-list-table.php
│   ├── class-settings.php
│   ├── class-analytics.php
│   ├── partials/      # Admin templates
│   ├── css/
│   └── js/
├── public/            # Frontend
│   ├── class-public-interface.php
│   ├── class-shortcode.php
│   ├── class-widget.php
│   ├── class-gutenberg-block.php
│   ├── css/
│   └── js/
└── templates/         # Display templates
```

### Core Classes

#### Smart_FAQ_Content_Analyzer

Handles content analysis and keyword extraction.

**Methods:**
- `extract_page_content($post_id)` - Extract content from a page
- `tokenize_content($content)` - Break content into words
- `extract_keywords($content, $limit)` - Extract top keywords
- `extract_bigrams_trigrams($content)` - Extract phrases
- `calculate_keyword_similarity($keywords1, $keywords2)` - Compare keyword sets

#### Smart_FAQ_Matcher

Handles FAQ matching algorithm.

**Methods:**
- `find_matching_faqs($page_id, $args)` - Find relevant FAQs for a page

**Algorithm:**
```php
final_score = (keyword_score * 0.4) + 
              (content_score * 0.3) + 
              (phrase_score * 0.2) + 
              (priority_score * 0.1)
```

#### Smart_FAQ_Database

All database operations.

**Methods:**
- `get_faq($faq_id)` - Get single FAQ
- `get_active_faqs($args)` - Get active FAQs
- `search_faqs($terms, $limit)` - Fulltext search
- `insert_faq($data)` - Create FAQ
- `update_faq($faq_id, $data)` - Update FAQ
- `delete_faq($faq_id)` - Delete FAQ

## Hooks & Filters

### Action Hooks

#### Database Operations

```php
// Before FAQ insert
do_action('smart_faq_before_insert', $data);

// After FAQ insert
do_action('smart_faq_after_insert', $faq_id, $data);

// Before FAQ update
do_action('smart_faq_before_update', $faq_id, $data);

// After FAQ update
do_action('smart_faq_after_update', $faq_id, $data);

// Before FAQ delete
do_action('smart_faq_before_delete', $faq_id);

// After FAQ delete
do_action('smart_faq_after_delete', $faq_id);
```

#### Display Hooks

```php
// Before entire widget
do_action('smart_faq_before_widget', $faqs, $args);

// After entire widget
do_action('smart_faq_after_widget', $faqs, $args);

// Before each FAQ item
do_action('smart_faq_before_faq_item', $faq, $args);

// After each FAQ item
do_action('smart_faq_after_faq_item', $faq, $args);
```

#### Cache Hooks

```php
// When cache is cleared
do_action('smart_faq_cache_cleared', $identifier);

// When cache is created
do_action('smart_faq_cache_created', $page_id, $faq_data);
```

### Filter Hooks

#### Content Analysis

```php
// Modify stop words list
$stop_words = apply_filters('smart_faq_stop_words', $stop_words);

// Modify extracted content
$content = apply_filters('smart_faq_content_extract', $content, $post_id);

// Modify extracted keywords
$keywords = apply_filters('smart_faq_keywords', $keywords, $content);
```

#### Matching Algorithm

```php
// Modify relevance score
$score = apply_filters('smart_faq_relevance_score', $score, $faq, $page_content);

// Modify matched FAQs
$faqs = apply_filters('smart_faq_matched_faqs', $faqs, $page_id, $args);

// Modify score threshold
$threshold = apply_filters('smart_faq_score_threshold', $threshold);

// Modify query arguments
$query_args = apply_filters('smart_faq_query_args', $query_args);
```

#### Display

```php
// Modify template path
$template = apply_filters('smart_faq_template_path', $template, $style);

// Modify output HTML
$html = apply_filters('smart_faq_widget_html', $html, $faqs, $args);

// Modify shortcode attributes
$atts = apply_filters('smart_faq_shortcode_atts', $atts);

// Modify display limit
$limit = apply_filters('smart_faq_display_limit', $limit);
```

### Hook Usage Examples

#### Example 1: Boost FAQs with Specific Keyword

```php
add_filter('smart_faq_relevance_score', function($score, $faq, $page_content) {
    if (strpos($faq->keywords, 'urgent') !== false) {
        return $score * 1.5; // Boost by 50%
    }
    return $score;
}, 10, 3);
```

#### Example 2: Add Custom Content to Analysis

```php
add_filter('smart_faq_content_extract', function($content, $post_id) {
    // Add custom field content
    $custom_content = get_post_meta($post_id, 'custom_field', true);
    return $content . ' ' . $custom_content;
}, 10, 2);
```

#### Example 3: Track FAQ Displays

```php
add_action('smart_faq_after_widget', function($faqs, $args) {
    foreach ($faqs as $faq) {
        // Log to custom analytics
        custom_log_faq_display($faq->id);
    }
}, 10, 2);
```

#### Example 4: Exclude FAQs Based on Conditions

```php
add_filter('smart_faq_matched_faqs', function($faqs, $page_id, $args) {
    return array_filter($faqs, function($faq) use ($page_id) {
        // Exclude certain FAQs on specific pages
        if ($page_id === 42 && $faq->category === 'Technical') {
            return false;
        }
        return true;
    });
}, 10, 3);
```

## Template System

### Template Hierarchy

The plugin looks for templates in this order:

1. `{theme}/smart-faq/faq-{style}.php`
2. `{plugin}/templates/faq-{style}.php`

### Creating Custom Templates

**Example: Custom Accordion Template**

Create `your-theme/smart-faq/faq-accordion.php`:

```php
<?php
if (!defined('ABSPATH')) exit;
$counter = 1;
?>
<div class="my-custom-faq <?php echo esc_attr($args['custom_class']); ?>">
    <?php foreach ($faqs as $faq) : ?>
        <div class="faq-item">
            <h3 class="faq-question">
                <?php if ($args['show_numbers']) : ?>
                    <span><?php echo $counter; ?>.</span>
                <?php endif; ?>
                <?php echo wp_kses_post($faq->question_html); ?>
            </h3>
            <div class="faq-answer">
                <?php echo wp_kses_post($faq->answer_html); ?>
            </div>
        </div>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>
```

### Available Template Variables

- `$faqs` - Array of FAQ objects
- `$args` - Array of arguments
  - `$args['style']` - Display style
  - `$args['show_numbers']` - Boolean
  - `$args['custom_class']` - Additional CSS class
  - `$args['show_schema']` - Boolean

### FAQ Object Properties

```php
$faq->id              // FAQ ID
$faq->question        // Plain text question
$faq->question_html   // HTML question
$faq->answer          // Plain text answer
$faq->answer_html     // HTML answer
$faq->keywords        // Keywords string
$faq->category        // Category name
$faq->priority        // Priority (0-100)
$faq->status          // Status (active/inactive/draft)
$faq->display_count   // Times displayed
$faq->created_at      // Creation timestamp
$faq->updated_at      // Update timestamp
$faq->relevance_score // Calculated relevance (0-1)
```

## Database Schema

### Table: wp_smart_faq_items

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| question | TEXT | Plain text question |
| question_html | LONGTEXT | HTML question |
| answer | TEXT | Plain text answer |
| answer_html | LONGTEXT | HTML answer |
| keywords | TEXT | Keywords |
| category | VARCHAR(255) | Category |
| priority | INT | Priority (0-100) |
| status | VARCHAR(20) | Status |
| created_by | BIGINT | Creator user ID |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Update timestamp |
| view_count | BIGINT | View count |
| display_count | BIGINT | Display count |

**Indexes:**
- PRIMARY KEY on `id`
- INDEX on `status`
- INDEX on `category`
- INDEX on `priority`
- FULLTEXT on `question`, `answer`, `keywords`

### Table: wp_smart_faq_cache

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| page_id | BIGINT | Page ID |
| content_hash | VARCHAR(64) | Content MD5 hash |
| matched_faq_ids | TEXT | JSON FAQ data |
| created_at | DATETIME | Creation timestamp |
| expires_at | DATETIME | Expiration timestamp |

**Indexes:**
- PRIMARY KEY on `id`
- UNIQUE on `page_id`
- INDEX on `expires_at`

### Table: wp_smart_faq_analytics

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| faq_id | BIGINT | FAQ ID |
| page_id | BIGINT | Page ID |
| displayed_at | DATETIME | Display timestamp |
| user_id | BIGINT | User ID |

**Indexes:**
- PRIMARY KEY on `id`
- INDEX on `faq_id`
- INDEX on `page_id`
- INDEX on `displayed_at`

## API Functions

### Public Functions

```php
// Get FAQs for current page
$faqs = smart_faq_get_faqs(array(
    'limit' => 5,
    'category' => 'General',
    'threshold' => 0.3
));

// Display FAQs
smart_faq_display(array(
    'limit' => 5,
    'style' => 'accordion'
));

// Get single FAQ
$faq = smart_faq_get_faq($faq_id);

// Get plugin option
$value = smart_faq_get_option('cache_duration', 24);

// Update plugin option
smart_faq_update_option('cache_duration', 48);

// Clear all cache
smart_faq_clear_cache();

// Clear page cache
smart_faq_clear_page_cache($page_id);

// Log message
smart_faq_log('Error message', 'ERROR');

// Get categories
$categories = smart_faq_get_categories();

// Enqueue styles
smart_faq_enqueue_styles();

// Enqueue scripts
smart_faq_enqueue_scripts();
```

### Class Methods

```php
// Content Analysis
$content = Smart_FAQ_Content_Analyzer::extract_page_content($post_id);
$keywords = Smart_FAQ_Content_Analyzer::extract_keywords($content, 50);
$hash = Smart_FAQ_Content_Analyzer::calculate_content_signature($content);

// FAQ Matching
$faqs = Smart_FAQ_Matcher::find_matching_faqs($page_id, $args);

// Database Operations
$faq = Smart_FAQ_Database::get_faq($id);
$faqs = Smart_FAQ_Database::get_active_faqs($args);
$faq_id = Smart_FAQ_Database::insert_faq($data);
Smart_FAQ_Database::update_faq($id, $data);
Smart_FAQ_Database::delete_faq($id);

// Cache Management
$cached = Smart_FAQ_Cache_Manager::get_cached_faqs($page_id, $hash);
Smart_FAQ_Cache_Manager::set_cached_faqs($page_id, $hash, $data);
Smart_FAQ_Cache_Manager::clear_all_cache();
Smart_FAQ_Cache_Manager::cleanup_expired_cache();

// FAQ Management
$faq_id = Smart_FAQ_Manager::create_faq($data);
Smart_FAQ_Manager::update_faq($faq_id, $data);
Smart_FAQ_Manager::delete_faq($faq_id);
$json = Smart_FAQ_Manager::export_faqs();
$result = Smart_FAQ_Manager::import_faqs($json);
$stats = Smart_FAQ_Manager::get_statistics();

// Widget Rendering
$html = Smart_FAQ_Widget_Renderer::render($faqs, $args);
```

## Extending the Plugin

### Creating a Custom Matching Algorithm

```php
// Replace the default matcher
add_filter('smart_faq_matched_faqs', function($faqs, $page_id, $args) {
    // Your custom algorithm here
    $custom_faqs = my_custom_matching_algorithm($page_id);
    return $custom_faqs;
}, 1, 3); // Priority 1 to run first
```

### Adding Custom FAQ Fields

```php
// Add custom field to admin form
add_action('smart_faq_after_faq_item', function($faq, $args) {
    ?>
    <div class="custom-field">
        <label>Custom Field:</label>
        <input type="text" name="custom_field" value="<?php echo esc_attr(get_post_meta($faq->id, 'custom_field', true)); ?>" />
    </div>
    <?php
});

// Save custom field
add_action('smart_faq_after_insert', function($faq_id, $data) {
    if (isset($_POST['custom_field'])) {
        update_post_meta($faq_id, 'custom_field', sanitize_text_field($_POST['custom_field']));
    }
}, 10, 2);
```

### Creating a Custom Widget Style

```php
// Register custom style
add_filter('smart_faq_template_path', function($path, $style) {
    if ($style === 'my-custom-style') {
        return plugin_dir_path(__FILE__) . 'templates/my-custom-template.php';
    }
    return $path;
}, 10, 2);
```

## Best Practices

### Performance

1. **Use caching** - Always enable caching in production
2. **Limit FAQ count** - Don't display too many FAQs at once
3. **Optimize queries** - Use filters carefully to avoid N+1 queries
4. **Lazy load assets** - Only load CSS/JS when needed

### Security

1. **Sanitize inputs** - Use `sanitize_text_field()`, `wp_kses_post()`, etc.
2. **Escape outputs** - Use `esc_html()`, `esc_url()`, etc.
3. **Check capabilities** - Use `current_user_can()` before operations
4. **Verify nonces** - Always verify nonces in custom forms

### Code Quality

1. **Follow WordPress Coding Standards**
2. **Use meaningful function/variable names**
3. **Comment your code**
4. **Handle errors gracefully**
5. **Test thoroughly before deployment**

### Debugging

```php
// Enable WordPress debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Use smart_faq_log() for logging
smart_faq_log('Debug message', 'DEBUG');

// Check error log
// wp-content/debug.log
```

## Support

For questions or issues:
1. Check this documentation
2. Review code comments
3. Check WordPress.org support forum
4. Review source code

## Contributing

Contributions welcome! Please:
1. Follow WordPress Coding Standards
2. Add PHPDoc comments
3. Test thoroughly
4. Submit pull requests

---

Happy coding! 🚀





