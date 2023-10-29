<?php

use G28\MqttConnection\ListStatistics;

$stats = new ListStatistics();
?>

<div class="wrap">
    <h1>Estatísticas MQTT</h1>

    <div class="stats-container">
        <form method="post">
            <?php $stats->prepare_items(); ?>
            <?php $stats->display(); ?>
        </form>
    </div>
</div>