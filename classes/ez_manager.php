<?php

class Ez_Manager{
    private $tabel;
    private $manager;

    public function __construct($table_name){
        global $wpdb;
        $this->manager = $wpdb;
        $this->table = $wpdb->prefix . $table_name;
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

    public function insert($data, $types){
        $result = $this->manager->insert( 
            $this->table, 
            $data, 
            $types
        );

        return $result;
    }

    public function update($id, $dataArray, $typesArray){
        return $this->manager->update(
            $this->table,
            $dataArray,
            array( 'id' => $id ),
            $typesArray,
            array( '%d' )
        );
    }

    public function remove($id){
        return $this->manager->delete( $this->table, array( 'id' => $id ), array( '%d' ) );
    }
}