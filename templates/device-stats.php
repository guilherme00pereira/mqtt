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
            $content = maybe_unserialize($stat->content)[0];
            $begin = date('Y-m-d H:i', $content->started);
            $minutes = round((int) $content->length / 3600, 2, PHP_ROUND_HALF_UP);
            ?>
            <tr>
                <td>
                    <?php echo $content->item; ?>
                </td>
                <td>
                    <?php echo $begin; ?>
                </td>
                <td>
                    <?php echo $minutes; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
