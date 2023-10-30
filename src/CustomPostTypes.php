<?php

namespace G28\MqttConnection;

class CustomPostTypes
{

    const META_SERVER_LAST_CONNECTION_STATUS = 'mqtt_connection_last_connection_status';
    const META_SERVER_LAST_FIVE_CONNECTIONS = 'mqtt_connection_last_five_connections';

    public function __construct()
    {
        add_action('init', [$this, 'register_mqtts_post_type']);
        add_action('save_post', [$this, 'save_server_meta_data']);
        add_filter('use_block_editor_for_post_type', [$this, 'disable_gutenberg'], 10, 2);
        add_filter('manage_servidor_posts_columns', [$this, 'add_server_columns']);
        add_action('manage_servidor_posts_custom_column', [$this, 'add_server_column_data']);
        add_filter('manage_dispositivo_posts_columns', [$this, 'add_device_columns']);
        add_action('manage_dispositivo_posts_custom_column', [$this, 'add_device_column_data']);
    }

    public function disable_gutenberg($current_status, $post_type)
    {
        if ($post_type === 'servidor' || $post_type === 'dispositivo') return false;
        return $current_status;
    }

    public function register_mqtts_post_type()
    {
        register_post_type(
            'servidor',
            [
                'labels' => [
                    'name' => __('Servidores MQTT'),
                    'singular_name' => __('Servidor MQTT'),
                ],
                'public' => true,
                'has_archive' => true,
                'show_in_rest' => true,
                'rest_base' => 'servidor',
                'show_in_admin_bar' => true,
                'menu_position' => 6,
                'menu_icon' => 'dashicons-networking',
                'register_meta_box_cb' => [$this, 'add_server_meta_boxes'],
                'delete_with_user' => true,
                'supports' => [
                    'title',
                    'author',
                ],
            ]
        );
        register_post_type(
            'dispositivo',
            [
                'labels' => [
                    'name' => __('Dispositivos'),
                    'singular_name' => __('Dispositivo'),
                ],
                'public' => true,
                'has_archive' => true,
                'show_in_rest' => true,
                'rest_base' => 'dispositivo',
                'show_in_admin_bar' => true,
                'menu_position' => 7,
                'menu_icon' => 'dashicons-smartphone',
                'register_meta_box_cb' => [$this, 'add_device_meta_boxes'],
                'delete_with_user' => true,
                'supports' => [
                    'title',
                    'author',
                ],
            ]
        );
    }

    public function add_server_meta_boxes()
    {
        add_meta_box(
            'server_address',
            'Dados do Servidor',
            [$this, 'render_server_fields'],
            'servidor',
            'normal',
            'high'
        );
    }

    public function add_device_meta_boxes()
    {

        add_meta_box(
            'device_data',
            'Dados do Dispositivo',
            [$this, 'render_device_fields'],
            'dispositivo',
            'normal',
            'high'
        );
        add_meta_box(
            'device_stats',
            'Estatísticas do Dispositivo',
            [$this, 'render_device_stats'],
            'dispositivo',
            'normal',
            'high'
        );
        add_meta_box(
            'server_parent',
            'Servidor',
            [$this, 'render_device_server'],
            'dispositivo',
            'side',
            'default',
        );
        add_meta_box(
            'device_actions',
            'Ações',
            [$this, 'render_device_actions'],
            'dispositivo',
            'side',
            'default',
        );
    }

    public function add_server_columns($columns)
    {
        $columns['server_address'] = 'Endereço';
        $columns['server_client_id'] = 'Client ID';
        $columns['server_last_connection'] = "Última conexão";
        return $columns;
    }

    public function add_server_column_data($column_id)
    {
        if ($column_id == 'server_address') {
            echo get_post_meta(get_the_ID(), 'server_address', true);
        }
        if ($column_id == 'server_client_id') {
            echo get_post_meta(get_the_ID(), 'server_client_id', true);
        }
        if ($column_id == 'server_last_connection') {
            $status = get_post_meta(get_the_ID(), 'mqtt_connection_last_connection_status', true);
            if ('error' === $status) {
                echo "<span style='color: white;background-color: #b22222;padding: 4px 8px;border-radius:1rem;'>falhou</span>";
            } else {
                echo "<span style='color: white;background-color: #38a138;padding: 4px 8px;border-radius:1rem;'>sucesso</span>";
            }
        }
    }

    public function add_device_columns($columns)
    {
        unset($columns['author']);
        $columns['server'] = "Servidor";
        $columns['device_name'] = 'Nome do Dispositivo';
        $columns['device_client_name'] = 'Nome do Cliente';
        $columns['device_responsible'] = 'Responsável';
        $columns['device_contact'] = 'Contato';
        return $columns;
    }

    public function add_device_column_data($column_id)
    {
        if ($column_id == 'server') {
            $post = get_post(get_the_ID());
            $parent = get_post($post->post_parent);
            $author = $parent->post_author;
            $user = get_user_by('id', $author);
            echo $parent->post_title . "<br />(" . $user->display_name . ")";
        }
        if ($column_id == 'device_name') {
            echo get_post_meta(get_the_ID(), 'deviceName', true);
        }
        if ($column_id == 'device_client_name') {
            echo get_post_meta(get_the_ID(), 'device_client_name', true);
        }
        if ($column_id == 'device_responsible') {
            echo get_post_meta(get_the_ID(), 'device_responsible', true);
        }
        if ($column_id == 'device_contact') {
            echo get_post_meta(get_the_ID(), 'device_contact', true);
        }
    }

    public function render_server_fields($post)
    {
        wp_enqueue_style(Plugin::getInstance()->getAssetsPrefix() . 'admin-server');
        wp_enqueue_script(Plugin::getInstance()->getAssetsPrefix() . 'admin-server');
        ob_start();
        include_once sprintf("%sserver-data.php", Plugin::getInstance()->getTemplateDir());
        echo ob_get_clean();
    }

    public function render_device_actions($post)
    {
        ob_start();
        include_once sprintf("%sdevice-actions.php", Plugin::getInstance()->getTemplateDir());
        echo ob_get_clean();
    }

    public function render_device_stats($post)
    {
        $stats = new ListStatistics( $post->ID );
        $stats->prepare_items();
        ob_start();
        $stats->display();
        echo ob_get_clean();
    }

    public function render_device_fields($post)
    {
        wp_enqueue_style(Plugin::getInstance()->getAssetsPrefix() . 'admin-server');
        wp_enqueue_script(Plugin::getInstance()->getAssetsPrefix() . 'admin-server');
        ob_start();
        include_once sprintf("%sdevice-extra-fields.php", Plugin::getInstance()->getTemplateDir());
        echo ob_get_clean();
    }

    public function render_device_server($post)
    {
        ob_start();
        include_once sprintf("%sdevice-server-info.php", Plugin::getInstance()->getTemplateDir());
        echo ob_get_clean();
    }

    public function save_server_meta_data($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $post = get_post($post_id);

        if ($post->post_type === 'servidor') {
            $version = $_POST['server_version'] ?? "";
            $address = $_POST['server_address'] ?? "";
            $client_id = $_POST['server_client_id'] ?? "";
            $username = $_POST['server_username'] ?? "";
            $password = $_POST['server_password'] ?? "";
            $topic_filter = $_POST['server_topic_filter'] ?? "";
            $data_for = $_POST['server_data_for'] ?? "";
            $recycle = $_POST['server_connection_recycle'] ?? "";
            $sensorsRT = $_POST['server_sensorsRT'] ?? "";
            $sensorsTT = $_POST['server_sensorsTT'] ?? "";

            update_post_meta($post_id, 'server_version', $version);
            update_post_meta($post_id, 'server_address', $address);
            update_post_meta($post_id, 'server_client_id', $client_id);
            update_post_meta($post_id, 'server_username', $username);
            update_post_meta($post_id, 'server_password', $password);
            update_post_meta($post_id, 'server_topic_filter', $topic_filter);
            update_post_meta($post_id, 'server_data_for', $data_for);
            update_post_meta($post_id, 'server_connection_recycle', $recycle);
            update_post_meta($post_id, 'server_sensorsRT', $sensorsRT);
            update_post_meta($post_id, 'server_sensorsTT', $sensorsTT);
        }

        if ($post->post_type === "dispositivo") {
            $client_name = $_POST['device_client_name'] ?? "";
            $device_address = $_POST['device_address'] ?? "";
            $device_responsible = $_POST['device_responsible'] ?? "";
            $device_contact = $_POST['device_contact'] ?? "";
            $observation = $_POST['observation'] ?? "";

            update_post_meta($post_id, 'device_client_name', $client_name);
            update_post_meta($post_id, 'device_address', $device_address);
            update_post_meta($post_id, 'device_responsible', $device_responsible);
            update_post_meta($post_id, 'device_contact', $device_contact);
            update_post_meta($post_id, 'observation', $observation);
        }
    }

    public static function update_last_five_connections( $id, $flag )
    {
        $flags_array = get_post_meta($id, CustomPostTypes::META_SERVER_LAST_FIVE_CONNECTIONS, true);
        if (empty($flags_array)) {
            $flags_array = [];
            $flags_array[] = $flag;
        } else {
            $flags_array[] = $flag;
            if (count($flags_array) > 5) {
                array_shift($flags_array);
            }
        }
        update_post_meta($id, CustomPostTypes::META_SERVER_LAST_FIVE_CONNECTIONS, $flags_array);
    }

}