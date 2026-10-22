## Requirements / deployment
- PHP >= 8.5 (php8.5-fpm), extensions: mysqli, gd, intl, mbstring, curl, zip, xml, redis (if used for sessions), swoole (socket server)
- Install PHP dependencies on every deploy (they are NOT in git): `cd application && composer install --no-dev -o`
- Check syntax of the whole project with PHP 8.5: `tools/lint.sh`
- Socket server: `php swoole/icat6serverphp85.php`

##
## .
...

##
## SQL's for CMS - 25-05-12
ALTER TABLE cat_user ADD COLUMN can_replace_main TINYINT(3) NULL DEFAULT '0';

ALTER TABLE cat_playlist ADD COLUMN is_replace_main TINYINT NULL DEFAULT '0';
## 
## SQL's for CMS - 25-01*
CREATE TABLE IF NOT EXISTS ssp_code_types (
  id tinyint unsigned NOT NULL DEFAULT '0',
  name varchar(50) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO ssp_code_types (id, name) VALUES
    (0, 'dmi'),
    (1, 'dpaa'),
    (2, 'ilab'),
    (3, 'openooh'),
    (4, 'idooh');

ALTER TABLE ssp_criteria CHANGE COLUMN code code VARCHAR(10) NULL DEFAULT NULL;

ALTER TABLE ssp_criteria CHANGE COLUMN type type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE ssp_criteria
    ADD CONSTRAINT FK_ssp_criteria_ssp_code_types FOREIGN KEY (type) REFERENCES ssp_code_types (id) ON UPDATE NO ACTION ON DELETE CASCADE;

ALTER TABLE cat_playlist ADD COLUMN main_campaign_id BIGINT NULL DEFAULT NULL;
