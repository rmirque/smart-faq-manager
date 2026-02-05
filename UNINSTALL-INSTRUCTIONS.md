# Clean Uninstall Instructions

## How to Completely Remove Smart FAQ Manager

### Method 1: WordPress Admin (Recommended)

1. **Deactivate the Plugin**
   - Go to **Plugins** in WordPress admin
   - Find "Smart FAQ Manager"
   - Click **Deactivate**
   - Wait for confirmation

2. **Delete the Plugin**
   - Click **Delete** link under the deactivated plugin
   - Confirm deletion when prompted
   - WordPress will automatically run the uninstall process

3. **Verification**
   - Plugin should disappear from the plugins list
   - All data is removed (see what's deleted below)

---

### Method 2: FTP/File Manager (If Delete Button Doesn't Work)

1. **Deactivate First** (if possible via WordPress admin)

2. **Connect via FTP** or use cPanel File Manager

3. **Delete Plugin Folder**
   - Navigate to: `/wp-content/plugins/`
   - Delete the folder: `smart-faq-manager/`

4. **Run Manual Cleanup** (Optional)
   - Go to phpMyAdmin
   - Run the cleanup queries below

---

### Method 3: WP-CLI

```bash
wp plugin deactivate smart-faq-manager
wp plugin delete smart-faq-manager
```

---

## What Gets Deleted Automatically

When you delete the plugin via WordPress admin, the following is automatically cleaned up:

### ✅ Database Tables (3 tables)
- `wp_smart_faq_items` - All FAQs
- `wp_smart_faq_cache` - All cached matches
- `wp_smart_faq_analytics` - All analytics data

### ✅ Options (All settings)
All options starting with `smart_faq_` including:
- Cache settings
- Display settings
- Matching algorithm settings
- Analytics settings
- Appearance settings
- All other plugin settings

### ✅ Transients (Temporary cache)
All transients used by the plugin

### ✅ Post Meta (Manual selections)
All post meta starting with `_smart_faq_` including:
- Manual FAQ selections
- Manual mode settings

### ✅ Widget Data
All widget instances and configurations

### ✅ Cron Jobs
Scheduled cleanup tasks

### ✅ Uploaded Files
Any files uploaded to `/wp-uploads/smart-faq-manager/` (if any)

### ✅ Cache
WordPress object cache flushed

---

## Manual Cleanup (Only if Needed)

If the automatic uninstall doesn't run or you want to manually verify cleanup:

### Step 1: Check and Delete Tables

```sql
-- Check if tables exist
SHOW TABLES LIKE 'wp_smart_faq%';

-- Delete tables if they exist
DROP TABLE IF EXISTS wp_smart_faq_items;
DROP TABLE IF EXISTS wp_smart_faq_cache;
DROP TABLE IF EXISTS wp_smart_faq_analytics;
```

### Step 2: Check and Delete Options

```sql
-- Check plugin options
SELECT * FROM wp_options WHERE option_name LIKE 'smart_faq_%';

-- Delete all plugin options
DELETE FROM wp_options WHERE option_name LIKE 'smart_faq_%';
```

### Step 3: Check and Delete Transients

```sql
-- Delete transients
DELETE FROM wp_options WHERE option_name LIKE '_transient_smart_faq_%';
DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_smart_faq_%';
```

### Step 4: Check and Delete Post Meta

```sql
-- Check post meta
SELECT * FROM wp_postmeta WHERE meta_key LIKE '_smart_faq_%';

-- Delete post meta
DELETE FROM wp_postmeta WHERE meta_key LIKE '_smart_faq_%';
```

### Step 5: Check and Delete Widget Data

```sql
-- Check widget data
SELECT * FROM wp_options WHERE option_name LIKE '%smart_faq_widget%';

-- Delete widget data
DELETE FROM wp_options WHERE option_name LIKE '%smart_faq_widget%';
```

---

## Verification Checklist

After uninstall, verify everything is clean:

- [ ] Plugin folder deleted: `/wp-content/plugins/smart-faq-manager/`
- [ ] No database tables: `SHOW TABLES LIKE 'wp_smart_faq%';` returns empty
- [ ] No options: `SELECT * FROM wp_options WHERE option_name LIKE 'smart_faq_%';` returns empty
- [ ] No post meta: `SELECT * FROM wp_postmeta WHERE meta_key LIKE '_smart_faq_%';` returns empty
- [ ] No cron jobs: Check scheduled events for `smart_faq_daily_cleanup`
- [ ] Plugin not in plugins list
- [ ] No PHP errors on site
- [ ] No JavaScript errors in browser console

---

## Troubleshooting Deletion Issues

### Issue: Delete Button Does Nothing

**Possible Causes:**
1. JavaScript error on plugins page
2. File permissions issue
3. Plugin files have trailing whitespace causing headers error
4. Another plugin conflict

**Solutions:**

**Solution 1: Check Browser Console**
1. Go to Plugins page
2. Press F12 (open Developer Tools)
3. Click Console tab
4. Try clicking Delete
5. Look for any JavaScript errors
6. Fix or report the error

**Solution 2: Check File Permissions**
- Plugin folder should be writable
- Check permissions: 755 for folders, 644 for files
- Ensure web server can delete files

**Solution 3: Force Delete via FTP**
- Use FTP/File Manager to delete `/wp-content/plugins/smart-faq-manager/`
- Then run manual cleanup SQL queries

**Solution 4: Disable Other Plugins**
- Temporarily deactivate all other plugins
- Try deleting Smart FAQ Manager
- Reactivate other plugins

---

## Data Retention Policy

**Deactivation** (Clicking "Deactivate"):
- ✅ All data PRESERVED
- ✅ Settings PRESERVED
- ✅ FAQs PRESERVED
- ✅ Analytics PRESERVED
- Cron jobs removed
- Cache cleared
- Reactivation restores everything

**Deletion** (Clicking "Delete"):
- ❌ All data DELETED
- ❌ Settings DELETED
- ❌ FAQs DELETED
- ❌ Analytics DELETED
- ❌ Everything GONE permanently
- No recovery possible

---

## Before Uninstalling

### Backup Your Data

If you might want to restore later:

1. **Export FAQs**
   - Go to FAQ Manager → All FAQs
   - Click "Export FAQs"
   - Save the JSON file

2. **Export Settings** (manual)
   - Note down your settings
   - Or screenshot the Settings page

3. **Database Backup**
   - Backup your entire WordPress database
   - Or just backup the 3 Smart FAQ tables

---

## Reinstallation

If you deleted and want to reinstall:

1. **Clean Install**
   - Upload and activate plugin
   - Fresh database tables created
   - Default settings applied
   - No previous data

2. **Restore from Backup**
   - Install plugin
   - Import FAQs (if you exported them)
   - Reconfigure settings manually

---

## Testing Uninstall

Want to test if uninstall works properly?

1. **On a Test Site** (not production!)
2. Activate the plugin
3. Add a few test FAQs
4. Deactivate
5. Delete
6. Check database for any remaining data
7. Should be completely clean

---

## Support

If you have issues deleting the plugin:

1. Check this guide's troubleshooting section
2. Try manual deletion via FTP
3. Run manual cleanup SQL queries
4. Check WordPress.org support forums
5. Contact your hosting provider if file permissions issue

---

## Summary

The plugin is designed to **delete cleanly** with no orphaned data:

✅ **One-Click Removal**: Deactivate → Delete → Done
✅ **Complete Cleanup**: All tables, options, meta removed
✅ **No Traces**: Zero footprint after deletion
✅ **Safe Deactivation**: Data preserved if just deactivating
✅ **Proper Security**: Checks permissions before deleting

**You can safely delete this plugin anytime!** 🗑️

---

**Updated Plugin**: `smart-faq-manager.zip` (108 KB)

Uninstall process is enterprise-grade and follows WordPress best practices!




