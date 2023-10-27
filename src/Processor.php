<?php

namespace G28\MqttConnection;

class Processor
{
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
            $post_id = 0;
            $devices = Database::selectDevices();
            foreach ($devices as $device) {
                $payload = json_decode($device->payload);
                $payloadResult = $payload->result;
                $deviceMAC = str_replace(['/RESP/', '/API'], '', $device->topic);
                $post_id = Database::deviceExist($deviceMAC);

                if ($post_id === 0) {
                    if ($payload->command === "deviceInfo") {
                        Logger::getInstance()->add("Cadastrando novo dispositivo " . $deviceMAC);
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


                if($payload->command === "playStatistics") {
                    foreach ($payloadResult->statistics as $stat) {
                        $begin = date('Y-m-d H:i', round($stat->started / 1000));
                        Database::insertDeviceStatistics($post_id, $stat->item, $begin, $stat->length);
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