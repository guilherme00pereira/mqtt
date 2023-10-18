<?php

namespace G28\MqttConnection;

class Admin
{
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_ajax_get_log', array( $this, 'get_log' ) );
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
        wp_enqueue_style( Plugin::getInstance()->getAssetsPrefix() . 'admin-stats' );
        wp_enqueue_script( Plugin::getInstance()->getAssetsPrefix() . 'admin-stats' );

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ob_start();
        include_once sprintf( "%sstats.php", Plugin::getInstance()->getTemplateDir() );
        echo ob_get_clean();
    }

    public function get_log()
    {
        if( ! wp_verify_nonce( $_GET['nonce'], 'mqtt-connection' ) ) {
            die( 'Ação não permitida' );
        }
        $log_content = Logger::getInstance()->getLogContent();
        echo json_encode( $log_content );
        wp_die();
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
            ['in-footer' => true]
        );

        wp_register_style( 
            Plugin::getInstance()->getAssetsPrefix() . 'admin-stats',
            Plugin::getInstance()->getAssetsUrl() . 'css/admin-stats.css',
            array(),
            Plugin::getInstance()->getVersion(),
            'all'
        );
        wp_register_script( 
            Plugin::getInstance()->getAssetsPrefix() . 'admin-stats',
            Plugin::getInstance()->getAssetsUrl() . 'js/admin-stats.js',
            array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ),
            Plugin::getInstance()->getVersion(),
            ['in-footer' => true]
        );

        wp_localize_script(
            Plugin::getInstance()->getAssetsPrefix() . 'admin-stats',
            'ajaxobj',
            array(
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'mqtt-connection' ),
                'action_log'    => 'get_log'
            )
        );
    }
}