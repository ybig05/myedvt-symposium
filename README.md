# MYEDVT Symposium 2026: simple registration (PHP + Bootstrap + MySQL)

People fill in the form, and the data is saved in MySQL. There is no admin page.

## Run it (4 steps)
```bash
sudo apt install php php-mysql php-mbstring
sudo systemctl start mariadb`   
sudo mysql < schema.sql          # works on a new OR existing database

# open config.php and set DB_PASS to your database password, then:
php -S localhost:8000 -t public
```
Put your poster at `public/poster.png`. Open http://localhost:8000 and click **Register now**.

If the form shows `DEBUG: ...`, that is the real error. Common ones:
- `Access denied for user`: wrong `DB_USER` / `DB_PASS` in config.php.
- `Unknown database`: run `sudo mysql < schema.sql`.
- `could not find driver`: run `sudo apt install php-mysql`.
- `Unknown column 'consent_at'`: run `sudo mysql < schema.sql` again.

## See your data (no admin page needed)
```bash
# on screen
sudo mysql -e "SELECT id, full_name, national_id, age, province, phone, created_at FROM myedvt_symposium.registrations ORDER BY id DESC LIMIT 20"

# count
sudo mysql -e "SELECT COUNT(*) FROM myedvt_symposium.registrations"

# export everything to a file that opens in Excel
sudo mysql --batch -e "SELECT * FROM myedvt_symposium.registrations" > registrations.tsv
```
Or install phpMyAdmin (`sudo apt install phpmyadmin`) for a point-and-click view.

## Before going live
1. `sudo mysql < reset_for_launch.sql` to wipe test data.
2. config.php: `DEBUG = false`, `FORCE_HTTPS = true`, `SITE_HOST = 'your domain'`, real privacy contact details.
3. Use HTTPS, point the web root at `public/`, use `grants.sql` for a restricted database user, and schedule `backup.sh`.
4. The Ministry confirms: the privacy notice wording, the age range, and its POTRAZ data-controller licence (the form collects National ID numbers).
