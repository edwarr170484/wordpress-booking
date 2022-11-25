<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ez_Orders_Table extends WP_List_Table {
    private $manager;

    public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'order_user_info' => __( '<b>User info</b>', 'ez_booking' ),
            'order_user_services' => __( '<b>Services</b>', 'ez_booking' ),
			'order_user_info' => __( '<b>Order info</b>', 'ez_booking' ),
			'order_user_email' => __( '<b>Email</b>', 'ez_booking' ),
            'order_answers' => __( '<b>Q/A</b>', 'ez_booking' ),
            'order_is_confirm' => __( '<b>Confirmation</b>', 'ez_booking' ),
		);

		return $columns;
	}

    public function __construct($manager) {
		parent::__construct( array(
			'singular' => 'order',
			'plural' => 'orders',
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
            '<input type="checkbox" name="order[]" value="%s" />', $item['id']
        );
    }

    function column_order_user_info( $item){
        $avatar = $item['customer_avatar'] ? $item['customer_avatar'] : '/wp-content/plugins/booking/images/no-avatar.png';
        
        return sprintf(
            '<table>
                <tr>
                    <td>
                        <img src="%s" style="max-width: 55px" />
                    </td>
                    <td>
                        <b>%s</b><br/>
                        <i>%s, %s</i>
                    </td>
                </tr>
            </table>   
            ', $avatar, $item['customer_name'], $item['customer_phone'], $item['customer_email']
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
            'save' => 'Сохранить',
            'delete' => 'Удалить'
        );

        return $actions;
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