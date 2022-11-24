<?php


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ez_Services_Table extends WP_List_Table {
    private $manager;

    public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'service_name' => __( '<b>Service name</b>', 'ez_tg_booking' ),
			'service_price' => __( '<b>Service price</b>', 'ez_tg_booking' ),
			'service_active' => __( '<b>Active</b>', 'ez_tg_booking' ),
		);

		return $columns;
	}

    public function __construct() {
		parent::__construct( array(
			'singular' => 'tour',
			'plural' => 'tours',
			'ajax' => false,
		) );

        $this->manager = new Ez_Tg_Booking_Manager();
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
            '<input type="checkbox" name="tour[]" value="%s" />', $item['id']
        );
    }

    function column_service_name( $item){
        return sprintf(
            '<input type="text" name="service_name[' . $item['id'] . ']" value="%s" class="full-width" />', $item['service_name']
        );
    }

    function column_service_price( $item){
        return sprintf(
            '<input type="text" name="service_price[' . $item['id'] . ']" value="%s" />', $item['service_price']
        );
    }

    function column_service_active( $item){
        return sprintf(
            '<input type="text" name="service_active[' . $item['id'] . ']" value="1" class="full-width" />', $item['is_active']
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'save' => 'Сохранить',
            'delete' => 'Удалить'
        );

        return $actions;
    }

    public function handle_table_actions(){
        switch($_POST['action']){
            case 'save':
                if(isset($_POST['tour']) && count($_POST['tour']) > 0){
                    foreach($_POST['tour'] as $key => $value){
                        $data = [
                            'ez_tg_booking_name' => $_POST['service_name'][$value],
                            'ez_tg_booking_price' => $_POST['service_price'][$value],
                            'ez_tg_booking_gid_chats' => $_POST['service_tg_gid'][$value],
                            'ez_tg_booking_partner_chats' => $_POST['service_tg_partner'][$value],
                        ];

                        $this->manager->update($value, $data);
                    }

                    add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Выбранные записи успешно сохранены', 'ez_tg_booking' ), 'success' );
                }
            break;

            case 'delete':
                if(count($_POST['tour']) > 0){
                    foreach($_POST['tour'] as $tourId){
                        $this->manager->remove($tourId);
                    }

                    add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Выбранные записи успешно удалены', 'ez_tg_booking' ), 'success' );
                }
            break;
        }
    }
}