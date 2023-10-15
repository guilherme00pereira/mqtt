<?php

namespace G28\MqttConnection;

class Database
{
    public static function maybeInstallTables(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = Plugin::getInstance()->prefixTableName("data");
        $sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		utc  datetime NOT NULL,
		topic tinytext NOT NULL,
		payload text NOT NULL,
		qos tinyint NOT NULL,
		retain tinyint NOT NULL,
		server_id bigint(20) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";
        dbDelta($sql);

        $sql = "ALTER TABLE $table_name ADD INDEX `idx_data_topic` (`topic`(50), `utc`);";
        dbDelta($sql);

        $table_name = Plugin::getInstance()->prefixTableName('buffer');

        $sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		utc  datetime NOT NULL,
		topic tinytext NOT NULL,
		payload text NOT NULL,
		qos tinyint NOT NULL,
		retain tinyint NOT NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";

        dbDelta($sql);

        $sql = "ALTER TABLE $table_name ADD INDEX `idx_buffer_topic` (`topic`(50), `utc`);";
        dbDelta($sql);
    }

    public static function uninstallTables()
    {
        
    }

    public static function selectDistinctTopics()
    {
        global $wpdb;

        $sql = "select distinct(topic) as topic, utc, payload, server_id from " . Plugin::getInstance()->prefixTableName("data") . " order by utc desc limit 50";
        return $wpdb->get_results($sql);
    }

    public static function deviceExist( $deviceMAC )
    {
        global $wpdb;
        $sql = $wpdb->prepare("select ID from " . $wpdb->prefix . "posts where post_type = 'dispositivo' and post_title = %s limit 1", $deviceMAC);
        $result = $wpdb->get_results($sql, ARRAY_A);
        if (count($result) > 0) {
            return $result[0]['ID'];
        }
        return 0;
    }
}