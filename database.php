<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
"drop table if exists {$CFG->dbprefix}booking",
"drop table if exists {$CFG->dbprefix}booking_attachments"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}booking",
"create table {$CFG->dbprefix}booking (
    booking_id int(11) unsigned NOT NULL AUTO_INCREMENT,
    link_id        INTEGER NOT NULL,
    user_id        INTEGER NOT NULL,
    booking_start  TIMESTAMP  NOT NULL,
    booking_end    TIMESTAMP NULL,
    title          VARCHAR(255) NOT NULL DEFAULT '',
    settings       mediumtext,
    updated_at     DATETIME NOT NULL,
    pre            tinyint(1) unsigned NOT NULL DEFAULT '0',
    pre_expire     VARCHAR(255) NULL,

    CONSTRAINT `{$CFG->dbprefix}booking_ibfk_1`
        FOREIGN KEY (`link_id`)
        REFERENCES `{$CFG->dbprefix}lti_link` (`link_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}booking_ibfk_2`
        FOREIGN KEY (`user_id`)
        REFERENCES `{$CFG->dbprefix}lti_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    PRIMARY KEY (`booking_id`),
    UNIQUE(link_id, user_id, booking_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8"),

array( "{$CFG->dbprefix}booking_attachments",
"create table {$CFG->dbprefix}booking_attachments (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    booking_id int(11) unsigned NOT NULL,
    file_id    int(11) NOT NULL,

    KEY `booking_attachment_1_idx` (`booking_id`),
    KEY `booking_attachment_2_idx` (`file_id`),

    CONSTRAINT `{$CFG->dbprefix}booking_attachments_ibfk_1`
        FOREIGN KEY (`booking_id`)
        REFERENCES `{$CFG->dbprefix}booking` (`booking_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}booking_attachments_ibfk_2`
        FOREIGN KEY (`file_id`)
        REFERENCES `{$CFG->dbprefix}blob_file` (`file_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
        
    PRIMARY KEY (`id`),
    UNIQUE(booking_id, file_id, id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);

