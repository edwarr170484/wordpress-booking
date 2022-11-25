<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ez_Days_Table extends WP_List_Table {
    private $manager;

    public function get_columns() {
		$columns = array(
			'day_name' => __( '<b>Day</b>', 'ez_booking' ),
			'start_time' => __( '<b>Start working time</b>', 'ez_booking' ),
			'end_time' => __( '<b>End working time</b>', 'ez_booking' ),
            'time_period' => __( '<b>Time period (min)</b>', 'ez_booking' ),
            'is_active' => __( '<b>Is day active</b>', 'ez_booking' )
		);

		return $columns;
	}

    public function __construct($manager) {
		parent::__construct( array(
			'singular' => 'day',
			'plural' => 'days',
			'ajax' => false,
		) );

        $this->manager = $manager;
	}

    public function prepare_items() {
		$current_screen = get_current_screen();
		$per_page = 20;

		$args = array(
			'posts_per_page' => $per_page,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => ( $this->get_pagenum() - 1 ) * $per_page,
		);

		if ( ! empty( $_REQUEST['s'] ) ) {
			$args['s'] = $_REQUEST['s'];
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			if ( 'title' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'title';
			} elseif ( 'author' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'author';
			} elseif ( 'date' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'date';
			}
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'asc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'ASC';
			} elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'DESC';
			}
		}

        $this->_column_headers = array($this->get_columns(), array(), array());
		$this->items = $this->manager->list();

		$total_items = $this->manager->count();
		$total_pages = ceil( $total_items / $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
		) );
	}

    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }

    function column_day_name( $item){
        return sprintf(
            '<b>%s</b>', $item['day_name'] 
        );
    }

    function column_start_time( $item){
        return sprintf(
            '<input type="time" name="day[' . $item['id'] . '][start_time]" value="%s" />', $item['start_time']
        );
    }

    function column_end_time( $item){
        return sprintf(
            '<input type="time" name="day[' . $item['id'] . '][end_time]" value="%s" />', $item['end_time']
        );
    }

    function column_time_period( $item){
        return sprintf('<input type="text" name="day[' . $item['id'] . '][time_period]" value="%s" />', $item['time_period']);
    }

    function column_is_active( $item){
        $checked = $item['is_active'] ? 'checked' : '';
        return sprintf('<input type="checkbox" name="day[' . $item['id'] . '][is_active]" value="1" class="full-width" %s />', $checked);
    }

    function get_bulk_actions() {
        $actions = array(
            'save' => 'Сохранить'
        );

        return $actions;
    }

    public function handle_table_actions(){
        switch($_POST['action']){
            case 'save':
                if(isset($_POST['day']) && count($_POST['day']) > 0){
                    foreach($_POST['day'] as $key => $day){
                        $data = [
                            'start_time' => $day['start_time'],
                            'end_time' => $day['end_time'],
                            'time_period' => $day['time_period'],
                            'is_active' => $day['is_active']
                        ];

                        $this->manager->update($key, $data, ['%s', '%s', '%s', '%d']);
                    }

                    add_settings_error( 'ez_booking_messages', 'ez_booking_message', __( 'Saves success', 'ez_tg_booking' ), 'success' );
                }
            break;
        }
    }
}