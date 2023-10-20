<?php

namespace G28\MqttConnection;

class CronEvents
{

    private static ?CronEvents $_instance = null;

    const LOCK_OPTION = 'mqtt_connection_lock';

    public static function getInstance(): ?CronEvents {
        if ( is_null( self::$_instance ) ) {
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
//        $lock_status = get_option(self::LOCK_OPTION);
//        if( !$lock_status ) {
//            add_option(self::LOCK_OPTION, 'unlocked');
//        }
//        if ($lock_status === 'unlocked') {
//            update_option(self::LOCK_OPTION, 'locked');

            Logger::getInstance()->add("");
            Logger::getInstance()->add("========== Executando cron ==========");
            Logger::getInstance()->add("");

            $this->connectAvailableServers();

            $this->verifyNewDevices();

            //Database::cleanTableData();

            Logger::getInstance()->add("");
            Logger::getInstance()->add("========== Finalizando execução do cron ==========");
            Logger::getInstance()->add("");

//            update_option(self::LOCK_OPTION, 'unlocked');
//        }
    }

    public function cron_schedules( $schedules )
    {
        if (!isset($schedules["5min"])) {
            $schedules["5min"] = array(
                'interval' => 300,
                'display' => __('Every 5 minutes'));
        }
        return $schedules;
    }

    /**
     * @return void
     */
    public function connectAvailableServers(): void
    {
        $servers = [];
        $query = new \WP_Query([
            'post_type' => 'servidor',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ]);
        while ($query->have_posts()) {
            $query->the_post();
            $servers[] = get_the_ID();
        }
        wp_reset_query();

        foreach ($servers as $server) {
            $metadata = get_post_meta($server);
            if (!empty($metadata['server_client_id'][0])) {
                $connector = new MqttConnector($server, $metadata);
                $connector->run();
            }
        }
    }

    public function verifyNewDevices()
    {
        $readedDevices = [];
        try {
            Logger::getInstance()->add("Verificando novos dispositivos");
            $post_id = 0;
            $devices = Database::selectDevices();
            foreach ($devices as $device) {
                $payload = json_decode($device->payload);
                $payloadResult = $payload->result;

                $deviceMAC = str_replace(['/RESP/', '/API'], '', $device->topic);

                if(in_array($deviceMAC, $readedDevices)) {
                    continue;
                } else {
                    Logger::getInstance()->add("Verificando dispositivo " . $deviceMAC);
                    $post_id = Database::deviceExist($deviceMAC);

                    if ($post_id === 0) {
                        if ($payload->command === "deviceInfo") {
                            Logger::getInstance()->add("Cadastrando dispositivo " . $deviceMAC);
                            $readedDevices[] = $deviceMAC . "_I";
                            $post_id = wp_insert_post([
                                'post_title' => $deviceMAC,
                                'post_type' => 'dispositivo',
                                'post_status' => 'publish',
                                'post_parent' => $device->server_id,
                            ]);
                            if (is_wp_error($post_id)) {
                                Logger::getInstance()->add("Error ao cadastrar dispositivo " . $deviceMAC . " no banco de dados: " . $post_id->get_error_message());
                            } else {
                                if (!empty($post_id)) {
                                    $this->saveDeviceMetaInfo($payloadResult, $post_id);
                                }
                            }
                        }

                    } else {
                        $this->saveDeviceMetaInfo($payloadResult, $post_id);
                    }
                }

                if( is_array( $payloadResult->statistics ) && count( $payloadResult->statistics ) > 0 )
                {
                    foreach( $payloadResult->statistics as $stat )
                    {
                        $stat->device_id = $post_id;
                        Database::insertDeviceStatistics( $stat );
                    }
                }

            }
        } catch (\Exception $error) {
            Logger::getInstance()->add($error->getMessage());
        }
    }

    /**
     * @param $payloadResult
     * @param $new_post_id
     * @return void
     */
    public function saveDeviceMetaInfo($payloadResult, $new_post_id): void
    {
        foreach ($payloadResult as $key => $value) {
            update_post_meta($new_post_id, $key, $value);
        }
    }

}
