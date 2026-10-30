-- Deletes ALL test registrations so the first real one is MYE-00001.
-- Run only when testing is finished:   sudo mysql < reset_for_launch.sql
USE myedvt_symposium;
DELETE FROM registrations;
ALTER TABLE registrations AUTO_INCREMENT = 1;
SELECT COUNT(*) AS registrations_left FROM registrations;   -- must show 0
