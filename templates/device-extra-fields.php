<?php

/**
 * @var $post WP_Post
 */

$extra_fields       = ['device_client_name', 'device_address', 'device_responsible', 'device_contact', 'observation'];

$client_name        = get_post_meta($post->ID, 'device_client_name', true);
$device_address     = get_post_meta($post->ID, 'device_address', true);
$device_responsible = get_post_meta($post->ID, 'device_responsible', true);
$device_contact     = get_post_meta($post->ID, 'device_contact', true);
$observation        = get_post_meta($post->ID, 'observation', true);

$metafields         = get_post_meta($post->ID);

?>

<table id="device-data">
    <tbody>
    <tr>
        <th scope="row">
            <label for="device_client_name">Nome do Cliente: </label>
        </th>
        <td>
            <input type="text" id="device_client_name" name="device_client_name" value="<?php echo $client_name ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="device_address">Endereço: </label>
        </th>
        <td>
            <input type="text" id="device_address" name="device_address" value="<?php echo $device_address ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="device_responsible">Responsável: </label>
        </th>
        <td>
            <input type="text" id="device_responsible" name="device_responsible" value="<?php echo $device_responsible ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="device_contact">Contato: </label>
        </th>
        <td>
            <input type="text" id="device_contact" name="device_contact" value="<?php echo $device_contact ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="observation">Observação: </label>
        </th>
        <td>
            <input type="text" id="observation" name="observation" value="<?php echo $observation ?>"/>
        </td>
    </tr>
    <?php foreach ( $metafields as $key => $value) {
        $value = $value[0];
        if( in_array($value, $extra_fields) ) {
            continue;
        } else {
            ?>
            <tr>
                <th scope="row">
                    <label><?php echo $key ?>: </label>
                </th>
                <td>
                    <input type="text" name="<?php echo $value ?>" value="<?php echo $value ?>" readonly />
                </td>
            </tr>
            <?php }
        } ?>
    </tbody>
</table>

