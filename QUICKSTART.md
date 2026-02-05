# Quick Start Guide - Smart FAQ Manager

Get up and running with Smart FAQ Manager in 5 minutes!

## Step 1: Install & Activate (1 minute)

1. Go to **Plugins → Add New** in WordPress admin
2. Click **Upload Plugin** and select the ZIP file
3. Click **Install Now** then **Activate Plugin**

✅ Done! The plugin is now active.

## Step 2: Add Your First FAQ (2 minutes)

1. Go to **FAQ Manager → Add New** in admin menu
2. Enter your first FAQ:
   - **Question**: "What is your return policy?"
   - **Answer**: "We accept returns within 30 days of purchase."
   - **Category**: "Shipping"
   - **Priority**: 50 (default)
3. Click **Publish**

✅ First FAQ created!

## Step 3: Display FAQs (1 minute)

Choose one method:

### Method A: Shortcode (Easiest)

1. Edit any page or post
2. Add this shortcode: `[smart_faq]`
3. Publish/Update

### Method B: Gutenberg Block

1. Edit any page with Gutenberg
2. Add block → Search for "Smart FAQ"
3. Configure block settings
4. Publish

### Method C: Widget

1. Go to **Appearance → Widgets**
2. Add "Smart FAQ" widget to a sidebar
3. Configure and save

✅ FAQs are now displaying!

## Step 4: Add More FAQs (Optional)

The more FAQs you add, the better the plugin works:

1. Go to **FAQ Manager → Add New**
2. Add 5-10 FAQs on different topics
3. Use different categories to organize them
4. Vary priorities based on importance

**Pro Tip**: Add keywords to improve matching accuracy!

## Step 5: Check Analytics (Optional)

After a few days:

1. Go to **FAQ Manager → Analytics**
2. See which FAQs are most displayed
3. Identify underperforming FAQs
4. Adjust priorities and keywords as needed

## Common Use Cases

### E-commerce Site

Create FAQs for:
- Shipping & Delivery
- Returns & Refunds
- Payment Methods
- Product Information
- Order Tracking

Example:
```
Question: How long does shipping take?
Answer: Standard shipping takes 5-7 business days. Express shipping is 2-3 days.
Category: Shipping
Keywords: delivery, shipping time, how long
Priority: 80
```

### Service Business

Create FAQs for:
- Pricing
- Booking Process
- Cancellation Policy
- Service Details
- Contact Information

### Blog/Content Site

Create FAQs for:
- Site Navigation
- Subscription
- Content Usage
- Technical Support
- Account Management

## Shortcode Examples

### Basic Usage
```
[smart_faq]
```

### Show 3 FAQs in List Style
```
[smart_faq limit="3" style="list"]
```

### Filter by Category
```
[smart_faq category="Shipping" limit="5"]
```

### Accordion with Numbers
```
[smart_faq style="accordion" show_numbers="true"]
```

### Grid Layout
```
[smart_faq style="grid" limit="6"]
```

## Customization Tips

### Change Display Style

1. Go to **FAQ Manager → Settings**
2. Under **Display Settings**
3. Change **Default Display Style**
4. Save changes

### Adjust Number of FAQs

1. Go to **FAQ Manager → Settings**
2. Change **Maximum FAQs to Display**
3. Try 3-7 for best results
4. Save changes

### Customize Appearance (NEW!)

**Easy way** - No CSS knowledge required!

1. Go to **FAQ Manager → Appearance**
2. Use color pickers to customize:
   - Question/answer colors
   - Border colors
   - Hover effects
3. Adjust font sizes and spacing
4. See live preview as you change
5. Click **Save Appearance Settings**

**Example customization:**
- Question Background: #2271b1 (blue)
- Question Text: #ffffff (white)
- Accent Color: #00a0d2 (light blue)
- Border Radius: 8px (rounded)

**Advanced way** - Custom CSS:

1. Go to **FAQ Manager → Settings**
2. Scroll to **Advanced Settings**
3. Add your custom CSS
4. Example:
```css
.smart-faq-widget {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
}

.smart-faq-question {
    color: #0066cc;
    font-weight: bold;
}
```

## Troubleshooting

### "No FAQs Showing"

**Check:**
- ✓ Are FAQs marked as "Active"?
- ✓ Is the shortcode correct?
- ✓ Are there FAQs relevant to the page content?
- ✓ Try lowering the matching threshold in settings

### "FAQs Not Relevant"

**Try:**
- ✓ Add more keywords to FAQs
- ✓ Increase priority of important FAQs
- ✓ Use categories matching page categories
- ✓ Add more FAQs covering different topics

### "FAQs Not Updating"

**Solution:**
1. Go to **FAQ Manager → All FAQs**
2. Click **Clear All Cache** button
3. Refresh your page

## Pro Tips

### 🎯 Tip 1: Use Descriptive Keywords
Instead of: "shipping"
Use: "shipping, delivery, how long, tracking, package"

### 🎯 Tip 2: Set Smart Priorities
- 90-100: Critical FAQs (must show)
- 70-89: Important FAQs
- 50-69: Standard FAQs (default)
- 30-49: Nice-to-have FAQs
- 0-29: Rare/specific FAQs

### 🎯 Tip 3: Use Categories Wisely
Match FAQ categories to your page/post categories for better relevance.

### 🎯 Tip 4: Test on Different Pages
Add the shortcode to different pages and see which FAQs appear. Adjust as needed.

### 🎯 Tip 5: Review Analytics Weekly
Check which FAQs perform well and which don't. Adjust accordingly.

## Next Steps

1. **Week 1**: Add 10-15 FAQs
2. **Week 2**: Review analytics, adjust priorities
3. **Week 3**: Fine-tune settings based on performance
4. **Ongoing**: Add new FAQs as questions arise

## Need Help?

- 📖 Read the full [README.md](README.md)
- 💻 Check [DEVELOPER.md](DEVELOPER.md) for advanced usage
- 🔧 See [INSTALLATION.md](INSTALLATION.md) for detailed setup
- 💬 Ask in WordPress.org support forum

## Settings Reference

### Recommended Settings for Most Sites

- **Cache Enabled**: ✓ Yes
- **Cache Duration**: 24 hours
- **Max FAQs**: 5
- **Display Style**: Accordion
- **Show Numbers**: ✓ Yes
- **Matching Threshold**: 0.3
- **Analytics Enabled**: ✓ Yes

### Settings for High-Traffic Sites

- **Cache Enabled**: ✓ Yes
- **Cache Duration**: 48 hours
- **Max FAQs**: 3
- **Matching Threshold**: 0.4 (stricter)

### Settings for Content-Heavy Sites

- **Max FAQs**: 7-10
- **Matching Threshold**: 0.2 (more lenient)
- **Display Style**: List

---

**You're all set!** 🎉

Start with these basics and explore more features as you get comfortable with the plugin.

Happy FAQ-ing! 😊


