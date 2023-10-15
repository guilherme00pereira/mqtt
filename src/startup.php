<?php

namespace G28\MqttConnection;

use G28\MqttConnection\Admin;

if (!function_exists(__NAMESPACE__ . 'runPlugin')) {
    function runPlugin($root): void
    {
        $plugin = Plugin::getInstance();
        $plugin->start($root);

        CronEvents::getInstance()->register();

        register_activation_hook($root, function (){
            if (! wp_next_scheduled ('mqtt_connection_cron_watchdog')) {
                wp_schedule_event(time(), '5min', 'mqtt_connection_cron_watchdog', [], true);
            }
        });
        register_deactivation_hook($root, function (){
            wp_clear_scheduled_hook('mqtt_connection_cron_watchdog');
        });
        add_action('plugins_loaded', function () {
            new Admin();
            new CustomPostTypes();
        });
    }

}