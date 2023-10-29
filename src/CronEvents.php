<?php

namespace G28\MqttConnection;

use stdClass;

class CronEvents
{

    private static ?CronEvents $_instance = null;

    public static function getInstance(): ?CronEvents
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {

    }

    public function register()
    {
        add_action('mqtt_connection_cron_watchdog', array($this, 'execute'));
        add_filter('cron_schedules', array($this, 'cron_schedules'));
    }

    public function execute()
    {

        Logger::getInstance()->add("");
        Logger::getInstance()->add("========== Executando cron ==========");
        Logger::getInstance()->add("");

        $processor = new Processor();
        $processor->connectAvailableServers();
        $processor->verifyNewDevices();

        Database::cleanTableData();
        Database::pruneStatsTable();

        Logger::getInstance()->add("");
        Logger::getInstance()->add("========== Finalizando execução do cron ==========");
        Logger::getInstance()->add("");

    }

    public function cron_schedules($schedules)
    {
        if (!isset($schedules["5min"])) {
            $schedules["5min"] = array(
                'interval' => 300,
                'display' => __('Every 5 minutes'));
        }
        return $schedules;
    }


}
