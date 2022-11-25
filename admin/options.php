<?php
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_manager.php';
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_services_table.php';
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_days_table.php';
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_orders_table.php';

    if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Настройки сохранены', 'ez_tg_booking' ), 'updated' );
	}

    if( isset( $_GET[ 'tab' ] ) ) {
        $active_tab = $_GET[ 'tab' ];
    }else{
        wp_redirect('?page=booking/admin/options.php&tab=bookings');
    }

    $services_manager = new Ez_Manager('ez_booking_services');
    $orders_manager = new Ez_Manager('ez_booking_order');
    $days_manager = new Ez_Manager('ez_booking_days');

    settings_errors( 'ez_booking_messages' );
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <nav class="nav-tab-wrapper">
        <a href="?page=booking/admin/options.php&tab=bookings" class="nav-tab <?php echo $active_tab == 'bookings' ? 'nav-tab-active' : ''; ?>">Bookings</a>
        <a href="?page=booking/admin/options.php&tab=services" class="nav-tab <?php echo $active_tab == 'services' ? 'nav-tab-active' : ''; ?>">Servcies</a>
        <a href="?page=booking/admin/options.php&tab=schedule" class="nav-tab <?php echo $active_tab == 'schedule' ? 'nav-tab-active' : ''; ?>">Schedule settings</a>
        <a href="?page=booking/admin/options.php&tab=questions" class="nav-tab <?php echo $active_tab == 'questions' ? 'nav-tab-active' : ''; ?>">Questions settings</a>
    </nav>

    <?php switch($active_tab){
        case 'bookings':
            $orders_table = new Ez_Orders_Table($orders_manager);
            ?>
                <form method="POST" action="">
                    <?php
                        $orders_table->handle_table_actions();
                        $orders_table->prepare_items(); 
                        $orders_table->display();
                    ?>
                </form>
            <?php
        break;

        case 'services':
            $services_table = new Ez_Services_Table($services_manager);
        ?>
            <div class="services">
                <div class="service-form">
                    <h1>Add new service</h1>
                    <form method="POST" action="">
                        <table class="form-table" role="presentation">
	                        <tbody>
                                <tr class="form-field form-required">
                                    <th scope="row"><label for="service_name">Service name <span class="description">(required)</span></label></th>
                                    <td><input name="service_name" type="text" id="service_name" value="" required /></td>
                                </tr>
                                <tr class="form-field form-required">
                                    <th scope="row"><label for="service_price">Service price <span class="description">(required)</span></label></th>
                                    <td><input name="service_price" type="text" id="service_price" value="" required /></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit"><input type="submit" name="createservice" id="createservice" class="button button-primary" value="Add new service"></p>
                    </form>
                </div>
                <form method="POST" action="">
                    <?php
                        $services_table->handle_form_action();
                        $services_table->handle_table_actions();
                        $services_table->prepare_items(); 
                        $services_table->display();
                    ?>
                </form>
            </div>
        <?php
        break;

        case 'schedule':
            $days_table = new Ez_Days_Table($days_manager);
        ?>
            <div class="services">
                <form method="POST" action="">
                    <?php
                        $days_table->handle_table_actions();
                        $days_table->prepare_items(); 
                        $days_table->display();
                    ?>
                </form>
            </div>
        <?php
        break;

        case 'questions':
        ?>
            <div class="admin-ez-options">
                <form action="options.php" method="post">
                    <?php settings_fields( 'ez_booking_options' );?>
                    <?php do_settings_sections( 'ez_booking' );?>
                    <?php submit_button( __( 'Сохранить', 'textdomain' ) );?>
                </form>
            </div>
        <?php
        break;
    }?>
</div>
<style>
    .table{
        width: 100%;
    }
    .table tr td, .table tr th{
        border: 1px solid lightgray;
        padding: 7px 12px;
    }
    .admin-booking-form{
        display: flex;
        align-items:stretch;
    }
    .admin-ez-options, .admin-ez-add{
        width: 50%;
    }
    .full-width{
        width: 100%;
    }
</style>