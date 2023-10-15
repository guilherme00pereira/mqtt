<?php

namespace G28\MqttConnection;

class Admin
{
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    public function add_admin_menu(): void
    {
        add_menu_page(
            'MQTT',
            'MQTT',
            'manage_options',
            'mqtt-connection',
            array( $this, 'display_plugin_statistics'),
            'dashicons-media-spreadsheet',
            2
        );
    }

    public function display_plugin_statistics(): void
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ob_start();
        include_once sprintf( "%sstats.php", Plugin::getInstance()->getTemplateDir() );
        echo ob_get_clean();
    }

    public function enqueue_admin_scripts()
    {
        wp_register_style(
            Plugin::getInstance()->getAssetsPrefix() . 'admin-server',
            Plugin::getInstance()->getAssetsUrl() . 'css/admin-server.css',
            array(),
            Plugin::getInstance()->getVersion(),
            'all'
        );
        wp_register_script(
            Plugin::getInstance()->getAssetsPrefix() . 'admin-server',
            Plugin::getInstance()->getAssetsUrl() . 'js/admin-server.js',
            array( 'jquery' ),
            Plugin::getInstance()->getVersion(),
            false
        );
    }
}