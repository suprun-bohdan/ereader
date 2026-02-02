# e-reader – Nextcloud app

Read EPUB and PDF books from Nextcloud Files.

## Installation

1. Copy the app into your Nextcloud **apps** directory:
   ```bash
   cp -r /path/to/ereader /var/www/html/nextcloud/apps/ereader
   ```
   The folder name **must** be `ereader` (same as app id in `appinfo/info.xml`).

2. Set owner to the web server user (e.g. `www-data`):
   ```bash
   sudo chown -R www-data:www-data /var/www/html/nextcloud/apps/ereader
   ```

3. Enable the app:
   ```bash
   cd /var/www/html/nextcloud   # or your Nextcloud root
   php occ app:enable ereader
   ```

4. Open Nextcloud in the browser. The **e-reader** entry should appear in the app menu (left sidebar). If not, check:
   - **Apps** → is "e-reader" listed? Enable it if it is disabled.
   - Nextcloud version ≥ 28 and PHP ≥ 8.1 (see `appinfo/info.xml`).

## If the app does not appear in the app list

- List all apps (enabled and disabled):
  ```bash
  php occ app:list
  ```
  Look for `ereader` in "Enabled" or "Disabled" section. If it is not there, Nextcloud does not see the app.

- Check for PHP errors when Nextcloud loads apps:
  ```bash
  tail -50 /var/www/html/nextcloud/data/nextcloud.log
  ```
  Or enable debug in `config/config.php`: `'debug' => true`, reload the Apps page and check the log.

- Ensure the app directory name is exactly `ereader` and that `appinfo/info.xml` exists and is valid.

- Ensure Nextcloud version is 28 or higher and PHP is 8.1 or higher.
