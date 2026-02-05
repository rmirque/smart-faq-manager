# FAQ Matching Optimization Guide

## Understanding the Improvements

We've made significant improvements to ensure consistent, high-quality FAQ matching:

### Key Changes:

1. **Lowered Default Threshold**: From 0.3 to 0.15
   - More lenient matching = more FAQs will display
   - Better for varied content topics

2. **Smart Fallback System**
   - Always shows top-scoring FAQs even if below threshold
   - Guarantees results on every page

3. **Improved Content Analysis**
   - Extracts and weights headings (H1-H6)
   - Better title weighting (5x instead of 3x)
   - Improved category/tag weighting
   - SEO meta description support (Yoast & Rank Math)

4. **Larger Candidate Pool**
   - Searches 50 FAQs instead of 30
   - Uses 15 keywords instead of 10
   - Better coverage of your FAQ database

## Best Practices for Consistent Matching

### 1. Write Descriptive FAQ Titles/Questions

**❌ Bad:**
- "What about shipping?"
- "Returns?"
- "Help with account"

**✅ Good:**
- "What are your shipping times and costs?"
- "What is your return policy and how do I return items?"
- "How do I reset my account password or update my profile?"

**Why?** More descriptive questions = better keyword matching.

### 2. Use Strategic Keywords

For each FAQ, add 5-10 relevant keywords covering:
- Main topic
- Related terms
- Common misspellings
- Synonyms

**Example FAQ about Shipping:**
```
Question: What are your shipping times?
Answer: We ship within 1-3 business days...
Keywords: shipping, delivery, how long, package, tracking, carrier, transit, mail, postal
Category: Shipping
```

### 3. Match Categories to Content

If your articles have categories, create matching FAQ categories:

**Article Categories:**
- Products
- Support
- Billing

**FAQ Categories:**
- Products
- Support  
- Billing

This gives matching FAQs a 20% relevance boost!

### 4. Set Smart Priorities

Use priorities strategically (0-100):

- **80-100**: Must-show FAQs (critical info)
- **60-79**: High importance FAQs
- **40-59**: Standard FAQs (default: 50)
- **20-39**: Supplementary FAQs
- **0-19**: Rarely relevant FAQs

**Tip:** Set higher priorities for FAQs that apply to multiple topics.

### 5. Create Topic-Specific FAQs

Instead of one generic FAQ for everything, create specific FAQs:

**❌ One Generic FAQ:**
- "Product Information" (covers everything)

**✅ Multiple Specific FAQs:**
- "What materials are your products made from?"
- "What sizes do your products come in?"
- "How do I care for and clean my products?"
- "Are your products eco-friendly?"

### 6. Use Your Article Titles in FAQs

If you have an article titled "Complete Guide to Product Returns", create FAQs like:
- "How do I start a product return?"
- "What is your return policy?"
- "Can I return opened products?"

This ensures the FAQ matches articles about returns.

## Recommended Settings

Go to **FAQ Manager → Settings** and use these values:

### For Most Sites:
```
Matching Threshold: 0.15
Max FAQs Display: 5
Keyword Weight: 0.4
Content Weight: 0.3
Phrase Weight: 0.2
Priority Weight: 0.1
Category Boost: 1.2
```

### For Stricter Matching (Only highly relevant FAQs):
```
Matching Threshold: 0.25
Max FAQs Display: 3
```

### For More Lenient Matching (Always show something):
```
Matching Threshold: 0.10
Max FAQs Display: 7
```

## Troubleshooting Common Issues

### Issue: "Some pages show no FAQs"

**Solutions:**
1. Lower the threshold: Try 0.10 or 0.15
2. Add more keywords to your FAQs
3. Check if FAQs are marked as "Active"
4. Clear the cache (FAQ Manager → Clear All Cache)

### Issue: "FAQs don't seem relevant"

**Solutions:**
1. Add more specific keywords to FAQs
2. Match FAQ categories to page categories
3. Increase priority on important FAQs
4. Write more descriptive FAQ questions

### Issue: "Same FAQs show on all pages"

**Solutions:**
1. Create more topic-specific FAQs
2. Increase the threshold: Try 0.25 or 0.30
3. Reduce priority on generic FAQs
4. Use categories to organize FAQs

### Issue: "Too many FAQs showing"

**Solutions:**
1. Reduce "Max FAQs Display" (try 3-4)
2. Increase the threshold slightly
3. Deactivate less important FAQs

## Monitoring & Optimization

### Week 1: Setup Phase
1. Add 10-20 FAQs covering main topics
2. Use threshold 0.15
3. Test on different pages
4. Adjust priorities as needed

### Week 2: Analysis Phase
1. Check **FAQ Manager → Analytics**
2. Look for "Underperforming FAQs"
3. Add keywords to FAQs that rarely show
4. Increase priority on important FAQs

### Week 3: Optimization Phase
1. Review which FAQs show most often
2. Create more specific versions of generic FAQs
3. Fine-tune categories
4. Adjust threshold if needed

### Ongoing:
- Monitor analytics monthly
- Add new FAQs as questions arise
- Update keywords based on performance
- Keep FAQs current and accurate

## Advanced Tips

### Tip 1: Use Page Categories

If your pages use WordPress categories, assign matching categories to FAQs for automatic relevance boosting.

### Tip 2: Test with Debug Mode

Enable WordPress debug mode to see relevance scores in the log:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check `wp-content/debug.log` to see which FAQs scored what.

### Tip 3: Create FAQ "Families"

Group related FAQs with similar keywords:

**Shipping Family:**
- All have "shipping" keyword
- All in "Shipping" category
- Varied priorities based on importance

### Tip 4: Use Synonyms

Add synonyms to keywords:
- shipping, delivery, courier, postal
- return, refund, exchange, send back
- account, profile, login, credentials

### Tip 5: Update Regularly

- Review analytics every 2 weeks
- Update underperforming FAQs
- Add seasonal FAQs as needed
- Archive outdated FAQs

## Content Strategy for Maximum Coverage

To ensure every page gets relevant FAQs:

1. **Core FAQs** (Priority 70-90)
   - 5-10 FAQs covering main business topics
   - Generic enough for multiple pages
   - Always relevant

2. **Topic-Specific FAQs** (Priority 50-70)
   - 20-30 FAQs for specific topics
   - Matched to article categories
   - Most of your FAQ database

3. **Supplementary FAQs** (Priority 30-50)
   - 10-20 FAQs for edge cases
   - Specific questions
   - Less frequently shown

## Measuring Success

Good FAQ matching means:
- ✅ 90%+ of pages show FAQs
- ✅ FAQs are topically relevant
- ✅ Top 3 FAQs score above 0.20
- ✅ Users don't see same FAQs everywhere
- ✅ Analytics show even distribution

Check your analytics dashboard to verify!

## Getting Help

Still having issues? Check:
1. This guide
2. DEVELOPER.md for advanced customization
3. Analytics dashboard for insights
4. WordPress.org support forum

## Quick Checklist

Before asking for help, verify:
- [ ] Threshold is between 0.10-0.20
- [ ] FAQs have keywords added
- [ ] FAQs are marked "Active"
- [ ] Cache has been cleared
- [ ] You have 10+ FAQs in database
- [ ] FAQ questions are descriptive
- [ ] Categories match content (if using)
- [ ] Checked analytics for insights

---

**Pro Tip:** The fallback system ensures pages always get FAQs now, but optimizing keywords and priorities ensures they're the RIGHT FAQs! 🎯




