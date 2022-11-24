<?php

class Ez_Tg_Booking_Manager{
    private $tabel;
    private $manager;

    public function __construct(){
        global $wpdb;
        $this->manager = $wpdb;
        $this->table = $wpdb->prefix . "ez_tg_booking_services";
    }

    public function list(){
        $query_results = $this->manager->get_results("SELECT * FROM $this->table", ARRAY_A);
        return $query_results;		
    }

    public function count(){
        return count($this->list());		
    }

    public function get($id){
        $single = $this->manager->get_row( "SELECT * FROM $this->table WHERE id = " . intval($id), ARRAY_A );
        return $single;
    }

    public function insert($data){
        $result = $this->manager->insert( 
            $this->table, 
            array( 
                'service_name' => $data['ez_tg_booking_name'],
                'service_price' => $data['ez_tg_booking_price'],
                'service_tg_gid' => $data['ez_tg_booking_gid_chats'],
                'service_tg_partner' => $data['ez_tg_booking_partner_chats'],
            ),
            array(
                '%s',	
                '%d',
                '%s'	
            )
        );

        if($result){
            return $this->update($this->manager->insert_id, $data);
        }

        return $result;
    }

    public function update($id, $data){
        return $this->manager->update(
            $this->table,
            array(
                'service_name' => $data['ez_tg_booking_name'],
                'service_price' => $data['ez_tg_booking_price'],
                'service_shortcode' => '[ez-tg-booking-form id=' . $id . '][/ez-tg-booking-form]',
                'service_tg_gid' => $data['ez_tg_booking_gid_chats'],
                'service_tg_partner' => $data['ez_tg_booking_partner_chats'],
            ),
            array( 'id' => $id ),
            array(
                '%s',	
                '%d',
                '%s',
                '%s'	
            ),
            array( '%d' )
        );
    }

    public function remove($id){
        return $this->manager->delete( $this->table, array( 'id' => $id ), array( '%d' ) );
    }
}