=== Smart FAQ Manager ===
Contributors: smartfaqmanager
Tags: faq, frequently asked questions, dynamic content, contextual, widget
Requires at least: 6.2
Tested up to: 6.9.1
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dynamically display contextually relevant FAQs on each page by analyzing page content using local content analysis.

== Description ==

Smart FAQ Manager allows site administrators to create a centralized database of rich HTML FAQ questions and answers, then automatically displays the most contextually relevant FAQs on each page by analyzing page content.

**Key Features:**

* Create and manage FAQs with rich HTML content
* Automatic contextual FAQ matching based on page content
* Local content analysis (no external API calls required)
* Multiple display styles: Accordion, List, Grid
* Smart caching system for optimal performance
* Gutenberg block support
* Shortcode support
* Widget support
* SEO-friendly with Schema.org markup
* Fully accessible (WCAG 2.1 AA compliant)
* Analytics and reporting
* Extensible with hooks and filters
* Multisite compatible
* Translation ready

**How It Works:**

1. Create FAQs in your WordPress admin panel
2. Add the FAQ widget, shortcode, or Gutenberg block to your pages
3. The plugin analyzes page content and displays the most relevant FAQs
4. FAQs are cached for optimal performance

**Shortcode Usage:**

`[smart_faq limit="5" style="accordion"]`
`[smart_faq category="shipping" limit="3"]`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/smart-faq-manager` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Smart FAQ Manager screen to configure the plugin
4. Start adding FAQs from the FAQ Manager menu

== Frequently Asked Questions ==

= Does this plugin require external API calls? =

No! Smart FAQ Manager uses local content analysis to match FAQs with page content, ensuring fast performance and privacy.

= Can I use HTML in my FAQs? =

Yes! Both questions and answers support rich HTML content with the WordPress editor.

= Will this slow down my site? =

No. The plugin uses an intelligent caching system to ensure minimal performance impact. Typically adds less than 100ms to page load time.

= Can I customize the appearance? =

Yes! You can use the built-in display styles or create custom templates in your theme.

= Is it compatible with page builders? =

Yes! The plugin works with Gutenberg, and you can use shortcodes with any page builder.

== Screenshots ==

1. FAQ Manager admin interface
2. Add/Edit FAQ screen
3. Settings page
4. Accordion display style
5. List display style
6. Analytics dashboard

== Changelog ==

= 1.1.0 =
* Enhanced Visual FAQ Designer - Complete visual design interface
* Template Gallery with 8 professional pre-built templates
* Tabbed interface: Templates, Colors, Typography, Layout, Effects, Advanced
* Device preview system (Desktop, Tablet, Mobile)
* Advanced controls: Animations, gradients, icon customization, enhanced borders
* Custom CSS editor with CodeMirror integration and real-time preview
* Live interactive preview with device switching and animation testing
* Template management: Apply, save, and export custom designs

= 1.0.1 =
* Enhanced FAQ schema markup for better SEO compliance
* Improved text sanitization and validation for schema generation
* Added support for both plain text and HTML answers in schema
* Added global setting to control schema markup generation
* Better error handling for empty or invalid FAQ content

= 1.0.0 =
* Initial release
* Core FAQ management functionality
* Content analysis engine
* Multiple display styles
* Caching system
* Analytics tracking
* Gutenberg block
* Accessibility features

== Upgrade Notice ==

= 1.0.0 =
Initial release of Smart FAQ Manager.

== Developer Documentation ==

Smart FAQ Manager is extensible with numerous hooks and filters. Visit our documentation for detailed information on customization options.

**Available Filters:**

* `smart_faq_matched_faqs` - Modify matched FAQ list
* `smart_faq_relevance_score` - Adjust relevance scoring
* `smart_faq_template_path` - Override template location
* And many more...

**Available Actions:**

* `smart_faq_after_insert` - After FAQ is created
* `smart_faq_after_update` - After FAQ is updated
* `smart_faq_cache_cleared` - When cache is cleared
* And many more...

== Privacy Policy ==

Smart FAQ Manager does not collect or transmit any personal data. Optional analytics features only track FAQ display counts and do not identify individual users.

