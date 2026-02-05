# Smart FAQ Manager

<p align="center">
  <img src="https://img.shields.io/badge/WordPress-5.8%2B-blue?style=flat-square&logo=wordpress" alt="WordPress 5.8+">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-purple?style=flat-square&logo=php" alt="PHP 7.4+">
  <img src="https://img.shields.io/badge/License-GPL%20v2-green?style=flat-square" alt="GPL v2">
</p>

> A production-ready WordPress plugin that intelligently displays contextually relevant FAQs using local content analysis. No external AI services required.

## Features

- **Smart Content Analysis** — Automatically analyzes page content to find the most relevant FAQs
- **Rich HTML Support** — Create FAQs with full WordPress editor formatting
- **Multiple Display Styles** — Accordion, List, and Grid layouts  
- **Intelligent Caching** — Multi-level caching for optimal performance
- **Gutenberg Block** — Native WordPress block editor support
- **Shortcode Support** — `[smart_faq]` for easy integration
- **WordPress Widget** — Sidebar widget support
- **Analytics Dashboard** — Track FAQ performance
- **SEO Optimized** — Schema.org structured markup
- **Fully Accessible** — WCAG 2.1 AA compliant
- **Translation Ready** — Full i18n support
- **Multisite Compatible** — Works with WordPress multisite

## Quick Start

### Download & Install

**Option 1: Download Release**
```bash
# Download from GitHub releases
wget https://github.com/rmirque/smart-faq-manager/releases/download/v1.0/smart-faq-manager-v1.0-FINAL.zip
```

**Option 2: Clone Repository**
```bash
cd wp-content/plugins/
git clone https://github.com/rmirque/smart-faq-manager.git
```

**Activate in WordPress:**
1. Go to **Plugins** → Installed Plugins
2. Find "Smart FAQ Manager"
3. Click **Activate**

## Usage

### Shortcode
```php
[smart_faq]                              <!-- Default: 5 FAQs, accordion style -->
[smart_faq limit="3" style="list"]       <!-- 3 FAQs in list format -->
[smart_faq category="shipping"]          <!-- Filter by category -->
```

### Gutenberg Block
Add the **Smart FAQ** block from the Widgets category.

### Widget
Go to **Appearance → Widgets** and add "Smart FAQ" to any widget area.

### PHP
```php
<?php
// Display FAQs
smart_faq_display([
    'limit' => 5,
    'style' => 'accordion',
    'category' => 'support'
]);

// Get FAQs programmatically
$faqs = smart_faq_get_faqs([
    'limit' => 10,
    'category' => 'billing'
]);
```

## Configuration

### FAQ Manager → Settings
- **Cache Duration** — How long to cache FAQ matches (default: 24 hours)
- **Max FAQs** — Maximum number to display (default: 5)
- **Display Style** — Default layout (accordion/list/grid)
- **Matching Threshold** — Minimum relevance score (0-1)

### FAQ Manager → Appearance
Customize colors, typography, spacing, and effects with live preview.

## Documentation

- [Quick Start Guide](QUICKSTART.md) — Get up and running in 5 minutes
- [Developer Guide](DEVELOPER.md) — Hooks, filters, and customization
- [Matching Algorithm](MATCHING-GUIDE.md) — How relevance scoring works
- [Selection Guide](MANUAL-SELECTION-GUIDE.md) — Manual FAQ placement
- [Changelog](CHANGELOG.md) — Version history

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher (with FULLTEXT search support)

## Performance

- **Caching**: Multi-level caching reduces database queries
- **Lazy Loading**: Assets only load when needed
- **Optimized Queries**: FULLTEXT search with proper indexing
- **Typical Impact**: < 100ms added to page load time

## Support

- 🐛 [Report Issues](../../issues)
- 💬 [Discussions](../../discussions)
- 📧 Contact: Visit [robfm.com/faq-manager](https://robfm.com/faq-manager)

## Contributing

Contributions welcome! Please follow WordPress coding standards.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing`)
5. Open a Pull Request

## License

GPLv2 or later. See [LICENSE.txt](LICENSE.txt) for details.

---

<p align="center">
  <a href="https://robfm.com/faq-manager">🌐 Landing Page</a> •
  <a href="../../releases">📦 Download</a> •
  <a href="../../issues">🐛 Report Bug</a>
</p>