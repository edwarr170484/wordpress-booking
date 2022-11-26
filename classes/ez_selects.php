<?php

require_once ABSPATH . 'wp-content/plugins/booking/classes/ez_manager.php';

class Ez_Selects{
    private $orders;
    private $workingDays = [];
    private $ordredDates = [];
    private $availableDates = [];
    private $datesListLength;
    private $services_manager;
    private $orders_manager;
    private $days_manager;

    function __construct($datesListLength){
        $this->services_manager = new Ez_Manager('ez_booking_services');
        $this->orders_manager = new Ez_Manager('ez_booking_order');
        $this->days_manager = new Ez_Manager('ez_booking_days');

        $today = new DateTime('now');

        $this->orders = $this->orders_manager->getUpperDate($today->format('Y-m-d'));
        
        $days = $this->days_manager->list();

        if(count($days) > 0){
            foreach($days as $day){
                if($day['is_active']){
                    array_push($this->workingDays, $day['day_name']);
                }
            }
        }

        if(count($this->orders) > 0){
            foreach($this->orders as $order){
                if(isset($this->ordredDates[$order['date']])){
                    array_push($this->ordredDates[$order['date']]['times'], $order['time']);
                }else{
                    $this->ordredDates[$order['date']] = [
                        'times' => [$order['time']]
                    ];
                }
            }
        }

        $this->datesListLength = $datesListLength;
    }

    public function getAvailableDates(){
        $start = new DateTime('now');

        for($i = 0; $i < $this->datesListLength; $i++){
            if(in_array($start->format('l'), $this->workingDays)){
                $this->availableDates[] = [
                    'label' => $start->format('d.m.Y'),
                    'value' => $start->format('Y-m-d'),
                    'times' => $this->getAvailableTimes($start)
                ];
            }

            $start->modify('+1 day');
        }

        return $this->availableDates;
    }

    public function getAvailableTimes($date){
        $dayTimes = [];
        
        $days = $this->days_manager->getDaysByName($date->format('l'));

        if($days){
            $dayTimes = json_decode($days[0]['times']);
        }

        if(isset($this->ordredDates[$date->format('Y-m-d')])){
            $orderedTimes = $this->ordredDates[$date->format('Y-m-d')]['times'];

            foreach($orderedTimes as $time){
                $dayTimes = array_filter($dayTimes, function($dayTime) use ($time){
                    return $dayTime != $time;
                });
            }
        }

        return $dayTimes;
    }
}