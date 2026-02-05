# Manual FAQ Selection Guide

## Overview

The **Manual FAQ Selection** feature gives you complete control over which FAQs appear on specific pages. This is perfect for:
- Landing pages where you want precise FAQ control
- Product pages with specific FAQs
- Pages where automatic matching isn't quite right
- Important pages that need hand-picked FAQs

## How to Use

### Step 1: Edit Any Page or Post

1. Go to **Pages** or **Posts** in WordPress admin
2. Click **Edit** on any page/post
3. Look for the **"Smart FAQ - Manual Selection"** box in the right sidebar

### Step 2: Choose Your Mode

You have **3 modes** to choose from:

#### 🤖 **Automatic Matching** (Default)
- The plugin automatically selects FAQs based on page content
- No manual work required
- Best for most pages

#### 🎯 **Manual + Automatic** (Recommended for Important Pages)
- Show your hand-picked FAQs FIRST
- Then fill remaining slots with automatic matches
- Perfect balance of control and automation
- **Example:** Show 2 specific FAQs, let the plugin add 3 more

#### ✋ **Manual Only** (Maximum Control)
- Show ONLY the FAQs you select
- No automatic matching
- Perfect for landing pages and product pages

### Step 3: Select Your FAQs

1. Choose **Manual + Automatic** or **Manual Only** mode
2. Checkboxes will appear showing all your active FAQs
3. Check the FAQs you want to show
4. FAQs will appear in the order you select them
5. A counter shows how many you've selected

### Step 4: Save

Click **Update** or **Publish** to save your selections!

## Use Cases

### Landing Page Example

**Scenario:** Product landing page for "Premium Widgets"

**Setup:**
- Mode: **Manual Only**
- Select 5 specific FAQs:
  - "What makes Premium Widgets special?"
  - "What's included with Premium Widgets?"
  - "How do Premium Widgets work?"
  - "Premium Widget pricing and plans?"
  - "Premium Widget guarantee?"

**Result:** Visitors see exactly these 5 FAQs, perfectly tailored to the product.

### Blog Post Example

**Scenario:** Blog post about "10 Tips for Better Productivity"

**Setup:**
- Mode: **Manual + Automatic**
- Select 2 must-show FAQs:
  - "What are your top productivity tips?"
  - "How do I get started with productivity improvements?"
- Let the plugin add 3 more related FAQs automatically

**Result:** Your 2 important FAQs always show, plus 3 contextual ones based on the post content.

### General Content Example

**Scenario:** Regular blog post or article

**Setup:**
- Mode: **Automatic Matching**
- No manual selections needed

**Result:** Plugin automatically shows the most relevant FAQs based on content analysis.

## Best Practices

### When to Use Manual Only

✅ **Use Manual Only for:**
- Landing pages
- Product pages
- Service pages
- Pricing pages
- About Us pages
- Contact pages
- High-traffic pages
- Pages with specific conversion goals

### When to Use Manual + Automatic

✅ **Use Manual + Automatic for:**
- Blog posts where you want 1-2 specific FAQs always shown
- Category pages with core FAQs plus automatic matches
- Tutorial pages with foundational FAQs
- Pages that are important but still benefit from contextual FAQs

### When to Use Automatic

✅ **Use Automatic for:**
- Regular blog posts
- News articles
- General content pages
- Pages where content is the main focus
- When you have many pages (don't have time to manually select for each)

## Tips for Success

### Tip 1: Mix Modes Across Your Site

Don't use the same mode everywhere:
- 5-10 key pages: **Manual Only**
- 20-30 important pages: **Manual + Automatic**
- All other pages: **Automatic**

### Tip 2: Update Periodically

Review your manual selections:
- Monthly: Check top 10 pages
- Quarterly: Review all manual selections
- When content updates: Update FAQ selections

### Tip 3: Use Analytics

Check **FAQ Manager → Analytics** to see:
- Which manual FAQs perform well
- If automatic matching works better than manual
- If you should adjust your selections

### Tip 4: Start Conservative

**Week 1:** Use automatic matching on all pages
**Week 2:** Manually select for top 5 pages
**Week 3:** Expand to more pages based on analytics
**Week 4:** Fine-tune based on results

### Tip 5: Don't Over-Select

**❌ Don't:**
- Select 10 FAQs when you only show 5 (wastes time)
- Manually select for every single page
- Never update selections

**✅ Do:**
- Select just enough for your display limit
- Focus on high-impact pages
- Review and update quarterly

## Troubleshooting

### "I don't see the meta box"

**Solutions:**
1. Make sure you're editing a Page or Post (not a FAQ)
2. Check if the meta box is collapsed - click "Smart FAQ - Manual Selection" to expand
3. Try refreshing the page

### "My selections don't show on the page"

**Solutions:**
1. Make sure you clicked **Update** or **Publish**
2. Clear the plugin cache: **FAQ Manager → Clear All Cache**
3. Check that FAQs are marked as "Active" in FAQ Manager
4. Verify the shortcode or widget is on the page

### "Manual + Automatic only shows manual FAQs"

**Explanation:** If your manual selections fill all available slots (e.g., you select 5 FAQs and max display is 5), no automatic FAQs will be added.

**Solutions:**
1. Reduce number of manual selections
2. Increase max display in **Settings** (e.g., from 5 to 7)
3. Use **Manual Only** mode if you want complete control

### "Changes not appearing on live site"

**Solutions:**
1. Clear plugin cache
2. Clear browser cache (Ctrl+Shift+Delete)
3. Clear site cache if using a caching plugin
4. Try viewing in incognito/private mode

## Combining with Other Features

### Manual Selection + Categories

You can still use categories with manual selection:
- FAQs are grouped by category
- Manual selection works across categories
- Category boost doesn't apply to manual selections

### Manual Selection + Shortcodes

Use shortcode attributes with manual pages:

```php
// This will be ignored on pages with manual selections
[smart_faq limit="10" threshold="0.1"]

// Manual selections override shortcode settings
```

### Manual Selection + Widgets

Widgets respect manual selections:
- Sidebar widgets show manual selections if set
- Widget settings are secondary to manual selections

## Advanced: Bulk Management

### For Multiple Pages

If you need to set the same FAQs on multiple pages:

1. Set up one page manually
2. Note which FAQs you selected
3. Repeat on other similar pages
4. Or use a custom function (see DEVELOPER.md)

### For Product Categories

If you have many product pages with similar FAQs:

1. Create a FAQ category for that product type
2. Use **Automatic Matching** with category filter
3. Or manually select on template pages

## Quick Reference

| Mode | Control Level | Best For | Time Required |
|------|---------------|----------|---------------|
| **Automatic** | Low | General content | 0 minutes |
| **Manual + Auto** | Medium | Important pages | 2-3 minutes |
| **Manual Only** | High | Landing pages | 3-5 minutes |

## Summary

Manual FAQ Selection gives you:
- ✅ Complete control when you need it
- ✅ Flexibility to mix automatic and manual
- ✅ Time savings on less important pages
- ✅ Guaranteed quality on key pages
- ✅ Easy to set up and maintain

**Pro Tip:** Start with automatic everywhere, then manually select for your top 5-10 pages. This gives you 90% of the benefit with 10% of the work! 🎯

---

Questions? See MATCHING-GUIDE.md for automatic matching tips or DEVELOPER.md for advanced customization.




