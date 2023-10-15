<?php

namespace G28\MqttConnection;

class CronEvents
{

    private static ?CronEvents $_instance = null;

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
        //$this->connectAvailableServers();
        $this->verifyNewDevices();
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
        try {
            Logger::getInstance()->add("Verificando novos dispositivos");
            $devices = Database::selectDistinctTopics();
            foreach ($devices as $device) {
                $deviceMAC = str_replace(['/RESP/', '/API'], '', $device->topic);
                Logger::getInstance()->add("Verificando dispositivo " . $deviceMAC);
                $post_id = Database::deviceExist($deviceMAC);
                if ($post_id === 0) {
                    $payload = json_decode($device->payload);
                    if($payload->command === "deviceInfo") {
                        $payloadResult = $payload->result;
                        Logger::getInstance()->add("Cadastrando dispositivo " . $deviceMAC);
                        $new_post_id = wp_insert_post([
                            'post_title' => $deviceMAC,
                            'post_type' => 'dispositivo',
                            'post_status' => 'publish',
                            'post_parent' => $device->server_id,
                        ]);
                        if (is_wp_error($new_post_id)) {
                            Logger::getInstance()->add("Error ao cadastrar dispositivo " . $deviceMAC . " no banco de dados: " . $new_post_id->get_error_message());
                        } else {
                            if ($new_post_id) {
                                $this->saveDeviceMetaInfo($payloadResult, $new_post_id);
                            }
                        }
                    }
                }
                else
                {
                    $this->saveDeviceMetaInfo($payloadResult, $post_id);
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
            Logger::getInstance()->add("Cadastrando item do dispositivo: " . $key . " - com valor: " . $value);
            update_post_meta($new_post_id, $key, $value);
        }
    }

}

//{
//    "command": "deviceInfo",
//  "success": true,
//  "result": {
//    "macAddress": "20:32:33:54:F8:C6",
//    "androidVersion": "7.1.2",
//    "deviceId": "625185d4fda711ab",
//    "uptime": 1697298648137,
//    "deviceName": "STV-3000PRO-NDOOHR_v1.1",
//    "appUptime": 1697298679843,
//    "softwareVersion": "4.4.7",
//    "softwareVersionCode": 231,
//    "hardwareModel": "STV-3000",
//    "storageSpaceFree": 2940436480,
//    "storageSpaceTotal": 4594073600,
//    "lastDisplayedFile": "CONTEUDO/MIDIAINDOOR/CIDADE DE VITÓRIA-ES _ Vitória Espírito Santo Brasil - Pontos turísticos _ Aerial View.mp4",
//    "ipAddressInternal": "192.168.0.100",
//    "timeZone": "America/Recife",
//    "rooted": true,
//    "deviceOwner": false,
//    "deviceAdmin": false,
//    "lockTaskApplication": false,
//    "lockTaskMode": false,
//    "currentVolume": 100,
//    "currentPlaylist": "CONTEUDO",
//    "currentScreenLayout": "TELA CHEIA"
//  }
//}