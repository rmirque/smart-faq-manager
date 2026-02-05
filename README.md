# Smart FAQ Manager

A production-ready WordPress plugin that intelligently displays contextually relevant FAQs based on page content using local content analysis.

## Features

- **Smart Content Analysis**: Automatically analyzes page content to find the most relevant FAQs
- **Rich HTML Support**: Create FAQs with full HTML formatting using WordPress editor
- **Multiple Display Styles**: Accordion, List, and Grid layouts
- **Intelligent Caching**: Multi-level caching system for optimal performance
- **Gutenberg Block**: Native block editor support
- **Shortcode Support**: `[smart_faq]` shortcode for easy integration
- **WordPress Widget**: Sidebar widget support
- **Analytics Dashboard**: Track FAQ performance and identify improvements
- **SEO Optimized**: Automatic Schema.org markup for search engines
- **Fully Accessible**: WCAG 2.1 AA compliant
- **Translation Ready**: Full i18n support
- **Multisite Compatible**: Works with WordPress multisite

## Installation

1. Upload the `smart-faq-manager` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to FAQ Manager → Settings to configure
4. Start adding FAQs!

## Usage

### Using Shortcode

```php
[smart_faq limit="5" style="accordion"]
[smart_faq category="shipping" limit="3" style="list"]
```

### Using PHP

```php
<?php
// Display FAQs
smart_faq_display(array(
    'limit' => 5,
    'style' => 'accordion'
));

// Get FAQs programmatically
$faqs = smart_faq_get_faqs(array(
    'limit' => 5,
    'category' => 'shipping'
));
?>
```

### Using Gutenberg Block

Add the "Smart FAQ" block from the Widgets category in the block editor.

### Using Widget

Go to Appearance → Widgets and add the "Smart FAQ" widget to your sidebar.

## Configuration

### Settings

- **Cache Duration**: How long to cache FAQ matches (default: 24 hours)
- **Max FAQs**: Maximum number of FAQs to display (default: 5)
- **Display Style**: Default display style (accordion, list, or grid)
- **Matching Threshold**: Minimum relevance score for display (0-1)
- **Algorithm Weights**: Fine-tune keyword, content, phrase, and priority weights

### Appearance Settings (NEW!)

Customize the look and feel of your FAQs with user-friendly controls:

- **Colors**: Question/answer backgrounds, text colors, accent colors, borders, hover states
- **Typography**: Font family, question/answer font sizes, font weights
- **Spacing**: Border radius, padding, margins, border width
- **Effects**: Box shadows, hover effects, transition speeds
- **Live Preview**: See changes in real-time before saving
- **Theme Defaults**: Leave fields empty to inherit from your WordPress theme

Access via: **FAQ Manager → Appearance**

### FAQ Fields

- **Question**: The FAQ question (supports HTML)
- **Answer**: The FAQ answer (supports HTML, images, etc.)
- **Keywords**: Optional keywords to improve matching
- **Category**: Categorize FAQs for better organization
- **Priority**: Manual priority (0-100) to boost specific FAQs
- **Status**: Active, Inactive, or Draft

## How It Works

1. **Content Analysis**: The plugin analyzes page content, extracting keywords and phrases
2. **FAQ Matching**: FAQs are scored based on keyword overlap, content similarity, and manual priority
3. **Smart Display**: Top-scoring FAQs above the threshold are displayed
4. **Caching**: Results are cached to minimize processing on subsequent page loads

## Developer Hooks

### Filters

```php
// Modify matched FAQs
add_filter('smart_faq_matched_faqs', function($faqs, $page_id, $args) {
    return $faqs;
}, 10, 3);

// Adjust relevance score
add_filter('smart_faq_relevance_score', function($score, $faq, $page_content) {
    return $score;
}, 10, 3);

// Modify extracted keywords
add_filter('smart_faq_keywords', function($keywords, $content) {
    return $keywords;
}, 10, 2);
```

### Actions

```php
// After FAQ is created
add_action('smart_faq_after_insert', function($faq_id, $data) {
    // Your code here
}, 10, 2);

// After FAQ is updated
add_action('smart_faq_after_update', function($faq_id, $data) {
    // Your code here
}, 10, 2);

// When cache is cleared
add_action('smart_faq_cache_cleared', function($identifier) {
    // Your code here
});
```

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher (with FULLTEXT search support)

## Performance

The plugin is optimized for performance:

- **Caching**: Multi-level caching reduces database queries
- **Lazy Loading**: Assets only load when needed
- **Optimized Queries**: FULLTEXT search and proper indexing
- **Typical Impact**: < 100ms added to page load time

## Support

For support, please use the WordPress.org support forums or visit our documentation.

## Contributing

Contributions are welcome! Please follow WordPress coding standards.

## License

GPLv2 or later. See LICENSE.txt for details.

## Credits

Developed with ❤️ following WordPress best practices.


