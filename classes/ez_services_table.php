<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ez_Services_Table extends WP_List_Table {
    private $manager;

    public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'service_name' => __( '<b>Service name</b>', 'ez_booking' ),
			'service_price' => __( '<b>Service price</b>', 'ez_booking' ),
			'service_active' => __( '<b>Active</b>', 'ez_booking' ),
		);

		return $columns;
	}

    public function __construct($manager) {
		parent::__construct( array(
			'singular' => 'tour',
			'plural' => 'tours',
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

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="serviceId[]" value="%s" />', $item['id']
        );
    }

    function column_service_name( $item){
        return sprintf(
            '<input type="text" name="service[' . $item['id'] . '][name]" value="%s" class="full-width" />', $item['service_name']
        );
    }

    function column_service_price( $item){
        return sprintf(
            '<input type="text" name="service[' . $item['id'] . '][price]" value="%s" />', $item['service_price']
        );
    }

    function column_service_active( $item){
        $checked = $item['is_active'] ? 'checked' : '';
        return sprintf('<input type="checkbox" name="service[' . $item['id'] . '][active]" value="1" class="full-width" %s />', $checked);
    }

    function get_bulk_actions() {
        $actions = array(
            'save' => '??????????????????',
            'delete' => '??????????????'
        );

        return $actions;
    }

    public function handle_form_action(){
        if(isset($_POST['service_name']) && isset($_POST['service_price'])){
            $data = [ 
                'service_name' => $_POST['service_name'],
                'service_price' => $_POST['service_price'],
            ];

            $this->manager->insert($data, ['%s', '%s']);

            add_settings_error( 'ez_booking_messages', 'ez_booking_message', __( 'New service added', 'ez_tg_booking' ), 'success' );
        }
    }

    public function handle_table_actions(){
        switch($_POST['action']){
            case 'save':
                if(isset($_POST['service']) && count($_POST['service']) > 0){
                    foreach($_POST['service'] as $key => $service){
                        $data = [
                            'service_name' => $service['name'],
                            'service_price' => $service['price'],
                            'is_active' => $service['active']
                        ];

                        $this->manager->update($key, $data, ['%s', '%s', '%d']);
                    }

                    add_settings_error( 'ez_booking_messages', 'ez_booking_message', __( 'Saves success', 'ez_tg_booking' ), 'success' );
                }
            break;

            case 'delete':
                if(count($_POST['serviceId']) > 0){
                    foreach($_POST['serviceId'] as $serviceId){
                        $this->manager->remove($serviceId);
                    }

                    add_settings_error( 'ez_booking_messages', 'ez_booking_message', __( 'Deleted success', 'ez_tg_booking' ), 'success' );
                }
            break;
        }
    }
}