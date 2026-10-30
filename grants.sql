-- LIVE database user with minimum rights (the form only needs to add rows).
-- Change the password, run:  sudo mysql < grants.sql   then set DB_USER/DB_PASS in config.php
CREATE USER IF NOT EXISTS 'symposium_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_LONG_RANDOM_PASSWORD';
GRANT INSERT ON myedvt_symposium.registrations TO 'symposium_app'@'localhost';
FLUSH PRIVILEGES;
