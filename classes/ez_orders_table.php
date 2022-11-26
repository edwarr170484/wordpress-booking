<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_services_table.php';
require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_days_table.php';

class Ez_Orders_Table extends WP_List_Table {
    private $manager;

    public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'order_user_info' => __( '<b>User info</b>', 'ez_booking' ),
            'order_services' => __( '<b>Services</b>', 'ez_booking' ),
			'order_info' => __( '<b>Order info</b>', 'ez_booking' ),
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

		/*if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'asc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'ASC';
			} elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'DESC';
			}
		}*/

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
            '<input type="checkbox" name="orderId[]" value="%s" />', $item['id']
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
                        <span style="font-weight: 700">%s</span><br/>
                        <i>%s, %s</i>
                        <input type="hidden" name="order[' . $item['id'] . '][customer_name]" value="%s" class="full-width"  />
                    </td>
                </tr>
            </table>   
            ', $avatar, $item['customer_name'], $item['customer_phone'], $item['customer_email'], $item['customer_name']
        );
    }

    function column_order_services( $item){
        $services_manager = new Ez_Manager('ez_booking_services');
        $days_manager = new Ez_Manager('ez_booking_days');

        $services = $services_manager->list();
        $orderedServces = json_decode($item['services']);

        $orderServices = '';
        if(count($services) > 0){
            foreach($services as $service){
                if(in_array($service['id'], $orderedServces)){
                    $orderServices .= $service['service_name'] . ' - ' . $service['service_price'] . ' EUR<br/>';
                }
            }
        }

        return sprintf('%s', $orderServices);
    }

    function column_order_info($item){
        $comment = $item['comment'] && $item['comment'] != '' ? $item['comment'] : '-';
        return sprintf(
            '<span style="font-weight: 700">Date:</span> %s<br/>
             <span style="font-weight: 700">Time:</span> %s<br/> 
             <span style="font-weight: 700">Comment:</span> %s
            ', $item['date'], $item['time'], $comment
        );
    }

    function column_order_answers($item){
        $options = get_option('ez_booking_options');
        $answers = json_decode($item['answers']);

        $userAnswers = '';

        for($i=1; $i <= 5; $i++){
            $userAnser = isset($answers[$i - 1]) ? $answers[$i - 1] : 'User answer: None';

            $userAnswers .= '
            <div style="padding-bottom: 5px;margin-bottom: 10px; border-bottom: 1px solid #c3c4c7">
                <span style="font-weight: 700">' . $options['question_' . $i] . '</span><br/>
                <span>' . $userAnser . '</span>
            </div>';
        }

        return sprintf('%s', $userAnswers);
    }

    function column_order_is_confirm( $item){
        $checked = $item['is_confirmed'] ? 'checked' : '';
        return sprintf('<input type="checkbox" name="order[' . $item['id'] . '][is_confirmed]" value="1" class="full-width" %s />', $checked);
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
                if(isset($_POST['order']) && count($_POST['order']) > 0){
                    foreach($_POST['order'] as $key => $order){
                        $data = [
                            'is_confirmed' => $order['is_confirmed']
                        ];
                        
                        $this->manager->update($key, $data, ['%d']);
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