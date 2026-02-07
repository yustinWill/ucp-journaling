# Laravel Spatie Backup - Daily Database Backup Setup

## ✅ Setup Completed

Your Laravel application is now configured to automatically backup the database daily at **1 AM** using Spatie's Laravel Backup package.

## 📋 What Was Implemented

### 1. **Package Installation**
- Installed `spatie/laravel-backup` v9.3.6 and dependencies
- Published default backup configuration

### 2. **Scheduler Configuration** 
Created `app/Console/Kernel.php` with the following schedule:

```php
// Daily database backup at 1 AM
$schedule->command('backup:run --only-db')
         ->dailyAt('01:00')
         ->appendOutputTo(storage_path('logs/backup.log'));

// Clean old backups daily at 2 AM
$schedule->command('backup:clean')
         ->dailyAt('02:00')
         ->appendOutputTo(storage_path('logs/backup.log'));
```

### 3. **Backup Configuration**
Located in `config/backup.php` with the following defaults:

- **Backup Destination**: `storage/app/` (local disk)
- **Compression**: Default ZIP compression (level 9)
- **Encryption**: AES-256 (can be configured via env)
- **Database**: Auto-detects from `DB_CONNECTION` env variable

### 4. **Backup Retention Policy**
Automatically keeps backups:
- All backups for 7 days
- Daily backups for 16 days
- Weekly backups for 8 weeks
- Monthly backups for 4 months
- Yearly backups for 2 years
- Max storage: 5000 MB (configurable)

## 🚀 How to Enable Daily Backups

### Step 1: Install MySQL Tools (Production)
The backup requires `mysqldump` (comes with MySQL):

**macOS (Homebrew):**
```bash
brew install mysql-client
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt-get install mysql-client
```

**Docker:**
Already included in MySQL/MariaDB images

### Step 2: Start the Laravel Scheduler
The scheduler needs to run as a background process. Add a cron job to your server:

```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Or for development with Laravel Valet:
```bash
valet isolate
php artisan schedule:work
```

### Step 3: (Optional) Configure Email Notifications
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=backups@yourapp.com
MAIL_FROM_NAME="Your App Backups"
```

### Step 4: (Optional) Encrypt Backups
Edit `.env`:
```env
BACKUP_ARCHIVE_PASSWORD=your-secure-password
```

## 📁 Backup Storage Location

Backups are stored in: `storage/app/`

List backups:
```bash
php artisan backup:list
```

## 🛠️ Available Commands

```bash
# Run backup immediately
php artisan backup:run --only-db

# Run backup with full app files
php artisan backup:run

# Schedule next run
php artisan schedule:run

# Monitor backup health
php artisan backup:monitor

# Clean old backups
php artisan backup:clean

# List all backups
php artisan backup:list
```

## 🔔 Notifications (Optional Setup)

The system can send notifications on:
- Backup success/failure
- Unhealthy backups detected
- Cleanup success/failure

Current setup: Mail notifications (requires mail config)

### Configure Slack Notifications
1. Create Slack webhook in your workspace
2. Add to `config/backup.php`:
```php
'slack' => [
    'webhook_url' => env('BACKUP_SLACK_WEBHOOK'),
],
```

3. Update notifications in config:
```php
'notifications' => [
    \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['slack'],
    // ... other notifications
],
```

## 📝 Log Files

Backup logs are stored in: `storage/logs/backup.log`

View recent backups:
```bash
tail -f storage/logs/backup.log
```

## ⚙️ Customization

### Exclude Tables from Backup
Edit `config/database.php`:
```php
'mysql' => [
    'driver' => 'mysql',
    // ...
    'dump' => [
        'exclude_tables' => ['sessions', 'cache', 'jobs'],
        'useSingleTransaction' => true,
    ],
],
```

### Change Backup Time
Edit `app/Console/Kernel.php`:
```php
$schedule->command('backup:run --only-db')
         ->dailyAt('02:30');  // Change from 01:00 to 02:30
```

### Change Retention Policy
Edit `config/backup.php` `cleanup.default_strategy` section:
```php
'keep_all_backups_for_days' => 30,  // Keep all for 30 days instead of 7
'keep_daily_backups_for_days' => 30,
// ... etc
```

### Store Backups on S3
1. Install AWS package: `composer require aws/aws-sdk-php`
2. Add S3 disk to `config/filesystems.php`
3. Update `config/backup.php`:
```php
'disks' => [
    'local',
    's3',  // Add S3
],
```

## 🔍 Testing

To test the backup manually:
```bash
# Create a backup now
php artisan backup:run --only-db

# Check if backup file was created
ls -lh storage/app/
```

## 📚 Documentation
- [Spatie Laravel Backup Docs](https://spatie.be/docs/laravel-backup/v9/introduction)
- [Database Dumper Docs](https://github.com/spatie/db-dumper)

## ✨ Next Steps

1. **Start the scheduler** (see Step 2 above)
2. **Verify MySQL tools** are installed if doing production backups
3. **Test a backup** manually: `php artisan backup:run --only-db`
4. **Monitor logs** to confirm successful backups at 1 AM daily
5. **(Optional) Set up notifications** for backup alerts
