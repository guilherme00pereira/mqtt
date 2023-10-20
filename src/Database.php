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

        $table_name = Plugin::getInstance()->prefixTableName('stats');

        $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        device_id bigint(20) NOT NULL,
        content text NOT NULL,
        started datetime NOT NULL,
        length int(11) NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public static function uninstallTables()
    {
        
    }

    public static function selectDevices()
    {
        global $wpdb;

        $sql = "select topic, utc, payload, server_id from " . Plugin::getInstance()->prefixTableName("data") . " order by utc desc limit 100";
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

    public static function selectStatisticsAllDevices()
    {
        global $wpdb;

        $sql = "select p.ID, p.post_title as device, pm.meta_value as content from " . $wpdb->prefix . "posts p
        inner join " . $wpdb->prefix . "postmeta pm on pm.post_id = p.ID
        where meta_key = 'statistics' and
        post_id in (select ID from wpen_posts where post_type = 'dispositivo')";
        return $wpdb->get_results($sql);
    }

    public static function getDeviceStatistics( $post_id )
    {
        global $wpdb;

        $sql = $wpdb->prepare("select p.post_title as device, pm.meta_value as content from " . $wpdb->prefix . "posts p
        inner join " . $wpdb->prefix . "postmeta pm on pm.post_id = p.ID
        where meta_key = 'statistics' and
        post_id  = %s", $post_id);
        return $wpdb->get_results($sql);
    }

    public static function insertDeviceStatistics( $stat )
    {
        global $wpdb;
        $sql = $wpdb->prepare("insert into " . Plugin::getInstance()->prefixTableName("stats") . " (device_id, content, started, length) values (%d, %s, %s, %d)", $stat->device_id, $stat->content, $stat->started, $stat->length);
        $wpdb->query($sql);
    }

    public static function cleanTableData()
    {
        global $wpdb;
        $wpdb->query("delete from " . Plugin::getInstance()->prefixTableName("data") . " where utc < DATE_SUB(NOW(), INTERVAL 1 DAY)");

    }
}