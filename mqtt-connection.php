<?php
/*
Plugin Name: Conexão MQTT - Cadastro de Servidor
Description: Plugin para cadastro de servidores MQTT
Version: 0.0.6
Author: G28 - Guilherme Pereira
Namespace: G28\MqttConnection
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require "vendor/autoload.php";

use function G28\MqttConnection\runPlugin;

runPlugin( __FILE__ );