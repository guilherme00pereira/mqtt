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
            $content = maybe_unserialize($stat->content);
            if(is_array($content) && count($content) > 0):
                $content    = maybe_unserialize($stat->content)[0];
                $begin      = date('Y-m-d H:i', round($content->started/1000));
                $minutes    = date('g:i:s', (int)$content->length);
            else:
                $content    = new stdClass();
                $content->item = "Sem dados";
                $begin      = 'Sem dados';
                $minutes    = 'Sem dados';
            endif;
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
