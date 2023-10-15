<?php

/**
 * @var $post WP_Post
 */

$version        = get_post_meta($post->ID, 'server_version', true);
$address        = get_post_meta($post->ID, 'server_address', true);
$client_id      = get_post_meta($post->ID, 'server_client_id', true);
$username       = get_post_meta($post->ID, 'server_username', true);
$password       = get_post_meta($post->ID, 'server_password', true);
$topic_filter   = get_post_meta($post->ID, 'server_topic_filter', true);
$data_for       = get_post_meta($post->ID, 'server_data_for', true);
$recycle        = get_post_meta($post->ID, 'server_connection_recycle', true);
$sensorsRT      = get_post_meta($post->ID, 'server_sensorsRT', true);
$sensorsTT      = get_post_meta($post->ID, 'server_sensorsTT', true);

$versions = ['3.1.1', '3.1',];
$data_for_options = ['Forever', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days', 'Last 165 days', 'Last 365 days'];

?>

<table id="server-data">
    <tbody>
    <tr>
        <th scope="row">
            <label for="server_version">Versão: </label>
        </th>
        <td>
            <select name="server_version" id="server_version">
                <?php foreach ($versions as $v): ?>
                    <option value="<?php echo $v ?>" <?php echo $v === $version ? 'selected' : '' ?>><?php echo $v ?></option>
                <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_address">Endereço: </label>
        </th>
        <td>
            <input type="text" id="server_address" name="server_address" value="<?php echo $address ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_client_id">Client ID: </label>
        </th>
        <td>
            <input type="text" id="server_client_id" name="server_client_id" value="<?php echo $client_id ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_username">Usuário: </label>
        </th>
        <td>
            <input type="text" id="server_username" name="server_username" value="<?php echo $username ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_password">Senha: </label>
        </th>
        <td>
            <input type="password" id="server_password" name="server_password" value="<?php echo $password ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_topic_filter">Filtro de tópico: </label>
        </th>
        <td>
            <input type="text" id="server_topic_filter" name="server_topic_filter" value="<?php echo $topic_filter ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_data_for">Dados para: </label>
        </th>
        <td>
            <select name="server_data_for" id="server_data_for">
                <?php foreach ($data_for_options as $d): ?>
                    <option value="<?php echo $d ?>" <?php echo $d === $data_for ? 'selected' : '' ?>><?php echo $d ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_connection_recycle">Reciclar conexão: </label>
        </th>
        <td>
            <input type="text" id="server_connection_recycle" name="server_connection_recycle"
                   value="<?php echo $recycle ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_sensorsRT">Sensores Receive Topic: </label>
        </th>
        <td>
            <input type="text" id="server_sensorsRT" name="server_sensorsRT" value="<?php echo $sensorsRT ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="server_sensorsTT">Sensores Transmit Topic: </label>
        </th>
        <td>
            <input type="text" id="server_sensorsTT" name="server_sensorsTT" value="<?php echo $sensorsTT ?>"/>
        </td>
    </tbody>
</table>