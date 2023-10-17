<?php

use G28\MqttConnection\Database;
use G28\MqttConnection\Logger;

$stats = Database::selectStatisticsAllDevices();
$log = Logger::getInstance()->getLogContent();

?>

<div class="wrap">
    <h1>MQTT Connector</h1>


    <div id="statistics-tabs">
        <ul class="nav-tab-wrapper">
            <li class="nav-tab stats-tab"><a href="#tab-01">Estatísticas</a></li>
            <li class="nav-tab stats-tab"><a href="#tab-02">Gerador de Arquivo</a></li>
            <li class="nav-tab stats-tab"><a href="#tab-03">Logs</a></li>
        </ul>
        <div class="tabs-content">
            <div id="tab-01" class="stats-tab-content">
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column">Dispositivo</th>
                            <th scope="col" class="manage-column">Item</th>
                            <th scope="col" class="manage-column">Início</th>
                            <th scope="col" class="manage-column">Duração</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $stat): 
                            $content    = maybe_unserialize($stat->content)[0];
                            $begin      = date('Y-m-d H:i', $content->started);
                            $minutes    = round( (int)$content->length / 3600, 2, PHP_ROUND_HALF_UP);
                            ?>
                            <tr>
                                <td><a href="post.php?post=82&action=edit" target="_blank"><?php echo $stat->device; ?></a></td>
                                <td><?php echo $content->item; ?></td>
                                <td><?php echo $begin; ?></td>
                                <td><?php echo $minutes; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="tab-02" class="stats-tab-content">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="gen_file_name">Nome do arquivo:</label></th>
                            <td><input name="gen_file_name" type="text" id="gen_file_name" value="" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="gen_file_ext">Extensão do arquivo: </label></th>
                            <td><input name="gen_file_ext" type="text" id="gen_file_ext" value="" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="gen_file_datetime">Data e hora: </label></th>
                            <td><input name="gen_file_datetime" type="text" id="gen_file_datetime" value="" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td>
                                <button id="gen_file_btn" class="button button-primary">Gerar arquivo</button>
                            </td>
                            <td>
                                <span id="gen_file_result"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-03" class="stats-tab-content">
                <div id="logFileContent" class="log-content">
                    <?php echo $log ?>
                </div>
            </div>
        </div>
    </div>

</div>