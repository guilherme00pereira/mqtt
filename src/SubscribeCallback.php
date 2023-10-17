<?php

namespace G28\MqttConnection;

use sskaje\mqtt\Message;
use sskaje\mqtt\Message\Header\PUBLISH;
use sskaje\mqtt\MessageHandler;
use Exception;
use sskaje\mqtt\MQTT;

class SubscribeCallback extends MessageHandler
{
    private string $server_id;
    public function __construct( $id )
    {
        $this->server_id = $id;
    }

    public function publish(MQTT $mqtt, Message\PUBLISH $publish_object)
    {
        global $wpdb;
        try
        {
            Logger::getInstance()->add("Mqtt publishing");
            $topic = $publish_object->getTopic();
            $msg = $publish_object->getMessage();
            $qos = $publish_object->getQos();
            $retain = $publish_object->getRetain();
            Logger::getInstance()->add("Mqtt msg received {$topic}, {$msg}, {$qos}, {$retain}");

            $tableName = Plugin::getInstance()->prefixTableName('data');

            $publish_objectarr = array($publish_object);

            if (!isset($publish_objectarr)) {
                Logger::getInstance()->add("Mqtt no publish object {$topic}, {$msg}, {$qos}, {$retain}");
                return;
            }
            $numObjs = count($publish_objectarr);
            Logger::getInstance()->add("Mqtt publish object count: {$numObjs}");

            foreach($publish_objectarr as $publish_object) {
                Logger::getInstance()->add("Inserindo no banco de dados");

                $utc = date_format(new \DateTime(), 'Y-m-d H:i:s');
                $result = $wpdb->insert(
                    $tableName,
                    array(
                        'utc'       => $utc,
                        'topic'     => $publish_object->getTopic(),
                        'payload'   => $publish_object->getMessage(),
                        'qos'       =>$publish_object->getQos(),
                        'retain'    => $publish_object->getRetain(),
                        'server_id' => $this->server_id
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s'
                    )
                );
                if(is_bool($result) && $result === false) {
                Logger::getInstance()->add("Inserção de retorno do servidor MQTT no banco falhou");
                } else {
                    Logger::getInstance()->add("Inserção no banco OK => {$result} linhas");
                }
            }
        }
        catch (Exception $e) {
            Logger::getInstance()->add("ERROR publishing ".$e->getMessage());
            //attempt graceful disconnect
            $mqtt->disconnect();
        }
    }

}