# Changelog

All notable changes to Smart FAQ Manager will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2024-01-XX

### Added
- **Enhanced Visual FAQ Designer** - Complete visual design interface
  - **Template Gallery**: 8 professional pre-built templates (Modern Gradient, Classic Professional, Minimal Clean, Bold & Vibrant, Dark Mode, Corporate Blue, Soft Pastel, Neumorphic)
  - **Tabbed Interface**: Organized settings into Templates, Colors, Typography, Layout, Effects, and Advanced tabs
  - **Device Preview**: Desktop, tablet, and mobile preview modes with responsive testing
  - **Advanced Controls**: Animation settings, gradient backgrounds, icon customization, enhanced borders
  - **Custom CSS Editor**: CodeMirror-integrated CSS editor with real-time preview
  - **Live Preview**: Real-time updates with interactive FAQ elements
  - **Template Management**: Apply, save, and export custom template designs

### Enhanced
- **Appearance Settings**: Complete redesign with visual controls and live preview
- **Style Generation**: Support for gradients, animations, custom icons, and advanced CSS
- **Preview System**: Interactive preview with device switching and animation testing

## [1.0.1] - 2024-01-XX

### Improved
- **Enhanced FAQ Schema Markup for SEO**
  - Improved text sanitization and validation for FAQ schema markup
  - Added support for both plain text and HTML answers in schema
  - Enhanced JSON-LD formatting with proper Unicode and slash handling
  - Added FAQ ID-based identifiers to each question item for better tracking
  - Improved input validation to prevent invalid schema markup
  - Added global setting `smart_faq_enable_schema` to control schema generation

### Added
- Global schema markup control setting in plugin options

### Fixed
- Schema markup now properly handles HTML content in FAQ answers
- Better error handling for empty or invalid FAQ content in schema generation

## [1.0.0] - 2024-01-01

### Added
- Initial release of Smart FAQ Manager
- Dynamic FAQ matching based on page content analysis
- Local content analysis engine (no external API calls)
- Rich HTML support for questions and answers
- Three display styles: Accordion, List, and Grid
- WordPress Gutenberg block support
- Shortcode support: `[smart_faq]`
- WordPress widget for sidebars
- Comprehensive admin interface
  - FAQ list with sorting and filtering
  - Rich text editor for questions and answers
  - Category management
  - Priority settings per FAQ
  - Status management (Active, Inactive, Draft)
- Advanced settings panel
  - Cache configuration
  - Display options
  - Algorithm tuning
  - Analytics settings
- Multi-level caching system
  - Database cache for matched FAQs
  - WordPress transients for queries
  - Object cache support (Redis/Memcached)
- Analytics dashboard
  - Top performing FAQs
  - Underperforming FAQ detection
  - Category performance stats
  - Display count tracking
- SEO optimization
  - Automatic Schema.org FAQPage markup
  - JSON-LD structured data
- Accessibility features
  - WCAG 2.1 AA compliant
  - Keyboard navigation support
  - ARIA attributes
  - Screen reader optimized
- Security features
  - Nonce verification on all forms
  - Input sanitization
  - Output escaping
  - Capability checks
  - SQL injection prevention
- Import/Export functionality
  - Export FAQs to JSON
  - Import FAQs from JSON
  - Bulk operations support
- Internationalization
  - Translation ready
  - POT file included
  - RTL language support
- Developer features
  - 15+ action hooks
  - 20+ filter hooks
  - Template override support
  - Extensive documentation
- Performance optimizations
  - FULLTEXT search indexing
  - Query optimization
  - Lazy loading of assets
  - Conditional asset loading
- WordPress Multisite support
- Custom CSS support
- Responsive design (mobile-first)
- Dark mode support

### Technical Features
- Content analysis engine
  - Keyword extraction (TF-IDF style)
  - Stop words filtering
  - Bigram and trigram analysis
  - Content signature hashing
- Matching algorithm
  - Keyword similarity scoring
  - Content overlap calculation
  - Phrase matching
  - Manual priority weighting
  - Category boosting
- Database schema
  - Optimized indexes
  - FULLTEXT search support
  - Proper foreign keys
  - Auto-cleanup for old data

### Security
- All user inputs sanitized
- All outputs escaped
- SQL prepared statements
- CSRF protection via nonces
- Capability checks throughout
- Direct file access prevention

### Documentation
- Complete README.md
- Installation guide
- Developer documentation
- Inline code documentation (PHPDoc)
- Template examples

## [1.0.1] - 2024-01-XX

### Improved
- **Enhanced FAQ Schema Markup for SEO**
  - Improved text sanitization and validation for FAQ schema markup
  - Added support for both plain text and HTML answers in schema
  - Enhanced JSON-LD formatting with proper Unicode and slash handling
  - Added FAQ ID-based identifiers to each question item for better tracking
  - Improved input validation to prevent invalid schema markup
  - Added global setting `smart_faq_enable_schema` to control schema generation

### Added
- Global schema markup control setting in plugin options

### Fixed
- Schema markup now properly handles HTML content in FAQ answers
- Better error handling for empty or invalid FAQ content in schema generation

## [Unreleased]

### Planned Features
- AI-powered FAQ generation (optional)
- A/B testing for FAQ displays
- User voting on FAQ helpfulness
- Advanced analytics charts
- FAQ search widget
- Related FAQs suggestions
- FAQ URL parameters for deep linking
- REST API endpoints
- FAQ duplicator
- FAQ revision history

### Planned Improvements
- Additional display templates
- More language translations
- Enhanced mobile experience
- Video content support in answers
- FAQ groups/collections
- Conditional display rules
- Time-based FAQ scheduling
- User role restrictions per FAQ

## Version History

### Version Numbering

- **Major.Minor.Patch** (e.g., 1.0.0)
- **Major**: Breaking changes, major new features
- **Minor**: New features, backward compatible
- **Patch**: Bug fixes, minor improvements

### Support Policy

- **Latest version**: Full support and updates
- **Previous major version**: Security updates only
- **Older versions**: No support (please upgrade)

## Upgrade Notes

### From Pre-release to 1.0.0
- First public release, no upgrade path needed

### Future Upgrades
- Always backup before upgrading
- Review changelog for breaking changes
- Test in staging environment first
- Clear cache after upgrade

## Contributors

Special thanks to all contributors who helped make this plugin possible.

---

For more information, visit the [plugin homepage](https://example.com/smart-faq-manager).



