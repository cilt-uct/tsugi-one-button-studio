<?php

// To allow this to be called directly or from admin/upgrade.php
if ( !isset($PDOX) ) {
    require_once "../config.php";
    $CURRENT_FILE = __FILE__;
    require $CFG->dirroot."/admin/migrate-setup.php";
}

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
    "drop table if exists {$CFG->dbprefix}booking_day_template",
    "drop table if exists {$CFG->dbprefix}booking_venue",
    "drop table if exists {$CFG->dbprefix}booking_day",
    "drop table if exists {$CFG->dbprefix}booking",
    "drop table if exists {$CFG->dbprefix}booking_admin",
    "drop table if exists {$CFG->dbprefix}booking_series",
    "drop table if exists {$CFG->dbprefix}booking_attachments",
);
// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
    array(  
        "{$CFG->dbprefix}booking_day_template",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_day_template (
            `id` INT NOT NULL AUTO_INCREMENT,
            `template` MEDIUMTEXT NOT NULL,
            PRIMARY KEY (`id`))
          ENGINE = InnoDB;"
    ),
    array(  
        "{$CFG->dbprefix}booking_venue",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_venue (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `CA` VARCHAR(99) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `color` VARCHAR(50) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 0,
            `template_id` INT(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_CA` (`CA` ASC),
            INDEX `idx_booking_template` (`template_id` ASC),
            CONSTRAINT `fk_booking_template`
              FOREIGN KEY (`template_id`)
              REFERENCES {$CFG->dbprefix}booking_day_template (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;"
    ),
    array(  
        "{$CFG->dbprefix}booking_day",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_day (
            `id` BIGINT(20) NOT NULL,
            `slot` INT(11) NOT NULL,
            `day` DATE NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            `venue` VARCHAR(99) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_unique` (`slot` ASC, `day` ASC, `venue` ASC),
            INDEX `idx_date` (`day` ASC),
            INDEX `idx_time` (`start_time` ASC, `end_time` ASC),
            INDEX `idx_venue` (`venue` ASC),
            CONSTRAINT `fk_booking_venue`
              FOREIGN KEY (`venue`)
              REFERENCES {$CFG->dbprefix}booking_venue (`CA`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;"
    ),
    array(  
        "{$CFG->dbprefix}booking",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking (
            `booking_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `link_id` INT(11) NOT NULL DEFAULT 0,
            `user_id` INT(11) NOT NULL DEFAULT 0,
            `booking_day` BIGINT(20) NOT NULL,
            `EID` VARCHAR(99) NOT NULL DEFAULT '',
            `email` VARCHAR(255) NOT NULL DEFAULT '',
            `title` VARCHAR(255) NOT NULL DEFAULT '',
            `settings` MEDIUMTEXT NULL DEFAULT NULL,
            `created_at` DATETIME NOT NULL,
            `created_by` INT(11) NULL DEFAULT NULL,
            `updated_at` DATETIME NOT NULL,
            `updated_by` INT(11) NOT NULL DEFAULT 0,
            `venue` VARCHAR(99) NOT NULL,
            `agreed` TINYINT(1) NOT NULL DEFAULT 0,
            `need_approval` INT(11) NULL DEFAULT 0,
            PRIMARY KEY (`booking_id`),
            UNIQUE INDEX `idx_unique` (`link_id` ASC, `user_id` ASC, `booking_id` ASC),
            INDEX `idx_index_user` (`user_id` ASC),
            INDEX `idx_booking_day` (`booking_day` ASC),
            CONSTRAINT `booking_ibfk_link`
              FOREIGN KEY (`link_id`)
              REFERENCES {$CFG->dbprefix}lti_link (`link_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `booking_ibfk_user`
              FOREIGN KEY (`user_id`)
              REFERENCES {$CFG->dbprefix}lti_user (`user_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_booking_day`
              FOREIGN KEY (`booking_day`)
              REFERENCES {$CFG->dbprefix}booking_day (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB"
    ),
    array(  
        "{$CFG->dbprefix}booking_admin",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_admin (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `EID` VARCHAR(99) NULL DEFAULT NULL,
            PRIMARY KEY (`id`))
          ENGINE = InnoDB"
    ),
    array(  
        "{$CFG->dbprefix}booking_series",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_series (
            `user_id` INT(11) NOT NULL,
            `series_id` VARCHAR(99) NOT NULL,
            `workspace_id` VARCHAR(50) NOT NULL,
            `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            PRIMARY KEY (`user_id`, `series_id`),
            UNIQUE INDEX `idx_user_id` (`user_id` ASC))
          ENGINE = InnoDB"
    ),
    array(  
        "{$CFG->dbprefix}booking_attachments",
        "CREATE TABLE IF NOT EXISTS {$CFG->dbprefix}booking_attachments (
            `booking_id` BIGINT(20) NOT NULL,
            `file_id` INT(11) NOT NULL,
            PRIMARY KEY (`booking_id`, `file_id`),
            INDEX `idx_attachment_file` (`file_id` ASC),
            CONSTRAINT `fk_attachments_file`
              FOREIGN KEY (`file_id`)
              REFERENCES {$CFG->dbprefix}blob_file (`file_id`)
              ON DELETE CASCADE
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_booking`
              FOREIGN KEY (`booking_id`)
              REFERENCES {$CFG->dbprefix}booking (`booking_id`)
              ON DELETE CASCADE
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;"
    ),
);

// Database upgrade
$DATABASE_UPGRADE = function($oldversion) {
    global $CFG, $PDOX;

    // This is a place to make sure added fields are present
    // if you add a field to a table, put it in here and it will be auto-added
    $add_some_fields = array(
        // array('migration', 'is_admin', 'TINYINT(1) NOT NULL DEFAULT 0'),

        // array('migration_site', 'title', 'VARCHAR(99)'),
        // array('migration_site', 'state', "enum('init','starting','exporting','running','importing','completed','error','admin')"),
        // array('migration_site', 'transfer_site_id', 'varchar(255)'),
        // array('migration_site', 'report', 'mediumtext'),
        // array('migration_site', 'site_url', 'VARCHAR(255)'),
    );

    foreach ( $add_some_fields as $add_field ) {
        if (count($add_field) != 3 ) {
            echo("Badly formatted add_field");
            var_dump($add_field);
            continue;
        }
        $table = $add_field[0];
        $column = $add_field[1];
        $type = $add_field[2];
        $sql = false;
        if ( $PDOX->columnExists($column, $CFG->dbprefix.$table ) ) {
            if ( $type == 'DROP' ) {
                $sql= "ALTER TABLE {$CFG->dbprefix}$table DROP COLUMN $column";
            } else {
                // continue;
                $sql= "ALTER TABLE {$CFG->dbprefix}$table MODIFY $column $type";
            }
        } else {
            if ( $type == 'DROP' ) continue;
            $sql= "ALTER TABLE {$CFG->dbprefix}$table ADD $column $type";
        }
        echo("Upgrading: ".$sql."<br/>\n");
        error_log("Upgrading: ".$sql);
        $q = $PDOX->queryReturnError($sql);
    }

    // $sql= "SELECT count(*) as c FROM `{$CFG->dbprefix}booking_venue`";
    // $count = $PDOX->rowDie($sql);

    // if ($count) {
    //     echo("Check booking venues: ".$sql." (". json_encode($count) ."-". $count['c'] === 0 .")<br/>\n");
    //     error_log("Check booking venues: ".$sql." (". json_encode($count) .")");
    
    //     if ($count['c'] === 0) {
    //         $sql = "INSERT INTO {$CFG->dbprefix}booking_venue ".
    //                 " (`id`,`CA`,`name`,`color`,`active`) VALUES ". 
    //                 " (1,'OBS1-CA','OB1','#3E4A89',1), ".
    //                 " (2,'OBS2-CA','OB2','#25828E',0), ".
    //                 " (3,'OBS3-CA','Podcast','#6DCD59',0);";
    //     }
    // }

    return 202012101330;

}; // Don't forget the semicolon on anonymous functions :)

// Do the actual migration if we are not in admin/upgrade.php
if ( isset($CURRENT_FILE) ) {
    include $CFG->dirroot."/admin/migrate-run.php";
}


