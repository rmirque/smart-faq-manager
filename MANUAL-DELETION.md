# Manual Plugin Deletion Guide

## Issue: Plugin Won't Delete from WordPress Admin

If clicking "Delete" doesn't remove the plugin from the list, follow these steps:

---

## Quick Fix (2 minutes)

### Step 1: Deactivate (if active)
- Go to **Plugins** in WordPress
- Click **Deactivate** under Smart FAQ Manager
- Wait for confirmation

### Step 2: Delete Files via FTP/File Manager

**Option A: FTP**
1. Connect to your site via FTP
2. Navigate to: `/wp-content/plugins/`
3. Delete the folder: `smart-faq-manager`
4. Disconnect

**Option B: cPanel File Manager**
1. Log into cPanel
2. Open **File Manager**
3. Navigate to: `public_html/wp-content/plugins/`
4. Right-click `smart-faq-manager` folder
5. Click **Delete**
6. Confirm deletion

**Option C: Local by Flywheel**
1. Right-click your site → **Open site shell**
2. Run: `rm -rf app/public/wp-content/plugins/smart-faq-manager`
3. Done!

### Step 3: Clean Database (Optional but Recommended)

Go to **phpMyAdmin** and run:

```sql
-- Delete tables
DROP TABLE IF EXISTS wp_smart_faq_items;
DROP TABLE IF EXISTS wp_smart_faq_cache;
DROP TABLE IF EXISTS wp_smart_faq_analytics;

-- Delete options
DELETE FROM wp_options WHERE option_name LIKE 'smart_faq_%';

-- Delete transients
DELETE FROM wp_options 
WHERE option_name LIKE '_transient_smart_faq_%' 
OR option_name LIKE '_transient_timeout_smart_faq_%';

-- Delete post meta
DELETE FROM wp_postmeta WHERE meta_key LIKE '_smart_faq_%';

-- Delete widget data
DELETE FROM wp_options WHERE option_name LIKE '%smart_faq_widget%';
```

### Step 4: Refresh WordPress

1. Go back to **Plugins** page in WordPress
2. Refresh the page (F5)
3. Plugin should now be GONE from the list!

---

## Why This Happens

**Common Causes:**

1. **File Permissions** - WordPress can't delete files (most common)
   - Fix: Delete via FTP as shown above

2. **Open File Handles** - Server has file locked
   - Fix: Restart web server or wait a few minutes

3. **Safe Mode** - Hosting has file deletion restricted
   - Fix: Contact hosting support or use FTP

4. **JavaScript Error** - Plugins page has JS error
   - Fix: Disable other plugins temporarily

---

## Prevention

To avoid this in future:

1. **Always deactivate first** before deleting
2. **Check file permissions**: 755 for folders, 644 for files
3. **Use FTP** if admin delete doesn't work
4. **Contact host** if permissions issues persist

---

## Verification

After manual deletion:

✅ Plugin folder gone: `/wp-content/plugins/smart-faq-manager/`
✅ Plugin not in WordPress Plugins list
✅ No errors on site
✅ Run SQL cleanup to remove database traces

---

## Need to Reinstall?

Just upload and activate the ZIP again:
- Fresh install
- Clean database tables created
- No old data conflicts

---

**Quick Command Reference:**

**Via SSH/Terminal:**
```bash
# Delete plugin folder
rm -rf wp-content/plugins/smart-faq-manager

# Or via wp-cli
wp plugin delete smart-faq-manager --deactivate
```

**That's it!** Manual deletion always works when WordPress admin doesn't. 🗑️✅




