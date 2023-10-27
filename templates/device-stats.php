<?php

/**
 * @var WP_Post $post
 */

use G28\MqttConnection\Database;

$stats = Database::getDeviceStatistics($post->ID);

?>


<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
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
                <td>
                    <?php echo $stat->content; ?>
                </td>
                <td>
                    <?php echo $stat->started; ?>
                </td>
                <td>
                    <?php echo $minutes; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
