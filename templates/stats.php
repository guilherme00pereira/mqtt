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
                            $minutes    = date('i:s', (int)$stat->length);
                            ?>
                            <tr>
                                <td><a href="post.php?post=<?php echo $stat->device_id; ?>&action=edit" target="_blank"><?php echo $stat->device_name; ?></a></td>
                                <td><?php echo $stat->content; ?></td>
                                <td><?php echo $stat->started; ?></td>
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
                            <td style="display: flex;">
                                <input type="text" id="gen_file_result" style="display: none; border: none; width: 280px;" />
                                <a href="#" id="gen_file_copyname" style="display: none; margin-left: 20px;">Copiar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="tab-03" class="stats-tab-content">
                <div style="display: flex; padding: 20px 0;">
                    <button id="logFileBtn" class="button button-primary">Atualizar</button>
                    <span id="loadingLog" class="spinner is-active" style="display: none;"></span>
                </div>
                <div id="logFileContent" class="log-content">
                    <?php echo $log ?>
                </div>
            </div>
        </div>
    </div>

</div>