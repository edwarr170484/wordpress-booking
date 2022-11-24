<?php
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_tg_manager.php';
    require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_services_table.php';

    if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Настройки сохранены', 'ez_tg_booking' ), 'updated' );
	}

    if( isset( $_GET[ 'tab' ] ) ) {
        $active_tab = $_GET[ 'tab' ];
    }else{
        wp_redirect('?page=booking/admin/options.php&tab=bookings');
    }

    $manager = new Ez_Tg_Booking_Manager();
    $table = new Ez_Services_Table();

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        global $wpdb;
        $table_name = $wpdb->prefix . "ez_booking_services";

        /* check plugin form action */
        switch($_POST['ez_tg_booking_form_action']){
            case 'add_new':
                if($manager->insert($_POST)){
                    add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Запись добавлена', 'ez_tg_booking' ), 'success' );
                }else{
                    add_settings_error( 'ez_tg_booking_messages', 'ez_tg_booking_message', __( 'Ошибка при добавлении записи', 'ez_tg_booking' ), 'error' );
                }
            break;
        }

        /* catch bulk table actions */
        $table->handle_table_actions();
    }

	// show error/update messages
	settings_errors( 'ez_tg_booking_messages' );
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
        break;

        case 'services':
        ?>
            <div class="tours">
                <form method="POST" action="">
                    <?php
                        $table->prepare_items(); 
                        $table->display();
                    ?>
                </form>
            </div>
        <?php
        break;

        case 'schedule':
        ?>
            
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