<?php

namespace G28\MqttConnection;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class ListStatistics extends \WP_List_Table
{

    public function __construct()
    {
        parent::__construct([
            'singular' => __('Estatística', 'g28-mqtt-connection'),
            'plural' => __('Estatísticas', 'g28-mqtt-connection'),
            'ajax' => false
        ]);
    }

    public static function get_items($per_page = 20, $page_number = 1)
    {
        global $wpdb;

        $sql = "select s.device_id, s.content, s.started, s.length, p.post_title as device_name from "
            . Plugin::getInstance()->prefixTableName('stats') . " s
            inner join " . $wpdb->prefix . "posts p on p.ID = s.device_id";

        if(!empty($_REQUEST['stats_content_ddl'])) {
            $sql .= " where s.content = '" . esc_sql($_REQUEST['stats_content_ddl']) . "'";
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY s.content ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    public static function record_count(): ?string
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM " . Plugin::getInstance()->prefixTableName('stats');

        if(!empty($_REQUEST['stats_content_ddl'])) {
            $sql .= " where content = '" . esc_sql($_REQUEST['stats_content_ddl']) . "'";
        }

        return $wpdb->get_var($sql);
    }

    public function no_items()
    {
        _e('Sem estatísticas para exibir', Plugin::getInstance()->getTextDomain());
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'device_name':
            case 'content':
            case 'started':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    public function column_length( $item ): string
    {
        return date('i:s', (int)$item['length']) . ' minutos';
    }

    public function get_columns(): array
    {
        return [
            'device_name' => __('Dispositivo', Plugin::getInstance()->getTextDomain()),
            'content' => __('Item', Plugin::getInstance()->getTextDomain()),
            'started' => __('Início', Plugin::getInstance()->getTextDomain()),
            'length' => __('Duração', Plugin::getInstance()->getTextDomain()),
        ];
    }

    public function get_sortable_columns(): array
    {
        return array(
            'device_name' => array('device_name', true),
            'content' => array('content', false),
            'started' => array('started', false),
            );
    }

    public function extra_tablenav($which)
    {
        global $wpdb;

        if("top" !== $which) {
            return;
        }

        $contens = Database::getDistinctContent();
        $devices = Database::getDistinctDevicesNames()
        ?>
        <div style="padding-bottom: 25px;">
            <label for="stats_device_ddl">Dispositivos: </label>
            <select id="stats_device_ddl" name="stats_device_ddl" style="width: 200px; margin-right: 20px;">
                <option value="">Selecione</option>
                <?php foreach ($devices as $device): ?>
                    <option value="<?php echo $device->ID; ?>"><?php echo $device->name; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="stats_content_ddl">Arquivos: </label>
            <select id="stats_content_ddl" name="stats_content_ddl" style="width: 200px; margin-right: 20px;">
                <option value="">Selecione</option>
                <?php foreach ($contens as $content): ?>
                    <option value="<?php echo $content; ?>"><?php echo $content; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="start_date">Início:</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr(isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : ''); ?>" style=" margin-right: 20px;">

            <label for="end_date">Fim:</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr(isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : ''); ?>" style=" margin-right: 20px;">
        <?php
        submit_button( 'Filtrar', 'secondary', 'filter_action', false, array( 'id' => 'post-query-submit' ));
        ?> </div> <?php
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = $this->get_items_per_page('devices_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);

        $this->items = self::get_items($per_page, $current_page);

    }
}