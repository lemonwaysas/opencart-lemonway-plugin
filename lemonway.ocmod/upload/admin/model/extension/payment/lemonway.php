<?php

class ModelExtensionPaymentLemonway extends Model
{
    public function install()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "lemonway_wktoken` (
            `id` int(11) AUTO_INCREMENT,
            `order_id` int(11) NOT NULL,
            `wktoken` varchar(255) NOT NULL,
            `register_card` tinyint(1) NOT NULL DEFAULT 0,

            PRIMARY KEY (`id`),
            UNIQUE KEY `wktoken` (`wktoken`),
            UNIQUE KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "lemonway_oneclick` (
    	    `id` int(11) AUTO_INCREMENT,
    		`customer_id` int(11) NOT NULL,
    		`card_id` int(11) NOT NULL,
    		`card_num` varchar(30),
    		`card_exp` varchar(8),
    		`card_type` varchar(20),
    		`date_add` datetime NOT NULL,
    	    `date_upd` datetime,

    	    PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "lemonway_wktoken`");
    }
}
