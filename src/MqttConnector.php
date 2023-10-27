<?php

namespace G28\MqttConnection;

use Exception;
use sskaje\mqtt\MQTT;

class MqttConnector
{

    protected array $server_data;
    private string $server_id;
    public ?MQTT $mqtt;

    public function __construct($post_id, $data)
    {
        $this->server_id = $post_id;
        $this->server_data = $data;
    }

    public function getServerId(): string
    {
        return $this->server_id;
    }

    public function run()
    {

        try {

            $file = './' . Plugin::getInstance()->prefixTableName('lock.pid');
            $lock = new Lock($file);
            $gmt_time = microtime(true);

            if ($lock->acquire()) {
                $recycle_secs = intval($this->server_data['server_connection_recycle'][0]);
                $mqtt = $this->buildMQTTClient();
                $result = $mqtt->connect();

                if (!($result)) {
                    Logger::getInstance()->add("Error connecting to server " . $this->server_data['server_address'][0]);
                    update_post_meta($this->server_id, CustomPostTypes::META_SERVER_LAST_CONNECTION_STATUS, 'error');
                    CustomPostTypes::update_last_five_connections( $this->server_id, 0);
                    return;
                } else {
                    Logger::getInstance()->add("Connected to server " . $this->server_data['server_address'][0]);
                    update_post_meta($this->server_id, CustomPostTypes::META_SERVER_LAST_CONNECTION_STATUS, 'success');
                    CustomPostTypes::update_last_five_connections( $this->server_id, 1);
                }

                $this->mqtt = $mqtt;

                $topics[$this->server_data['server_topic_filter'][0]] = 1;
                $callback = new SubscribeCallback($this);

                Logger::getInstance()->add("Setting handler");
                $mqtt->setHandler($callback);

                Logger::getInstance()->add("Subscribing");
                $mqtt->subscribe($topics);

                while ($this->mqtt && (microtime(true) - $gmt_time < $recycle_secs) && $mqtt->loop()) {
                    Logger::getInstance()->add("Looping");
                    set_time_limit(0);
                }
                Logger::getInstance()->add("Disconnecting");

                $mqtt->disconnect();
            }
        } catch (Exception $error) {
            Logger::getInstance()->add("Error during watchdog, disconnecting: " . $error->getMessage());
            if (!empty($mqtt)) {
                try {
                    $mqtt->disconnect();
                }
                catch (Exception $eee) {
                    Logger::getInstance()->add($eee->getMessage());
                }
            }
        }
    }

    public function buildMQTTClient(): MQTT
    {
        $mqtt = new MQTT($this->server_data['server_address'][0], $this->server_data['server_client_id'][0]);

        switch ($this->server_data['server_version'][0]) {
            case "3_1_1":
                $mqtt->setVersion(MQTT::VERSION_3_1_1);
                break;
            default:
                $mqtt->setVersion(MQTT::VERSION_3_1);
                break;
        }

        if (strpos($this->server_data['server_address'][0], 'ssl://') === 0) {
            $mqtt->setSocketContext(
                stream_context_create(
                    [
                        'ssl' => [
                            /*   'cafile'                => '/path/to/CACert-mqtt.crt',*/
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'disable_compression' => true,
                            'ciphers' => 'ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:kEDH+AESGCM:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:ECDHE-RSA-RC4-SHA:ECDHE-ECDSA-RC4-SHA:AES128:AES256:RC4-SHA:HIGH:!aNULL:!eNULL:!EXPORT:!DES:!3DES:!MD5:!PSK',
                            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT,
                            'SNI_enabled' => true,
                            'allow_self_signed' => true
                        ]
                    ]
                )
            );
        } else {
            $mqtt->setSocketContext(stream_context_create());
        }

        if ($this->server_data['server_username'][0] && $this->server_data['server_password'][0]) {
            $mqtt->setAuth(
                $this->server_data['server_username'][0],
                $this->server_data['server_password'][0]
            );
        }
        return $mqtt;
    }

}