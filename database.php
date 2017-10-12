<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
"drop table if exists {$CFG->dbprefix}booking"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}booking",
"create table {$CFG->dbprefix}booking (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    link_id     INTEGER NOT NULL,
    user_id     INTEGER NOT NULL,    
    booking     TIMESTAMP  NOT NULL,
    title       VARCHAR(255) NOT NULL DEFAULT '',
    attachments int(1) NOT NULL DEFAULT '0',
    settings mediumtext,
    updated_at  DATETIME NOT NULL,

    CONSTRAINT `{$CFG->dbprefix}booking_ibfk_1`
        FOREIGN KEY (`link_id`)
        REFERENCES `{$CFG->dbprefix}lti_link` (`link_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}booking_ibfk_2`
        FOREIGN KEY (`user_id`)
        REFERENCES `{$CFG->dbprefix}lti_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    PRIMARY KEY (`id`),
    UNIQUE(link_id, user_id, id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);

