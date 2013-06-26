<?php

class OWMonitoringLog extends eZPersistentObject {
    protected $identifier;
    protected $start_date;
    protected $end_date;
    protected $status;
    protected $log_events;

    const STATUS_RUNNING = 0;
    const STATUS_FINISHED = 1;
    const STATUS_FAILED = 2;

    public function __construct( $identifier ) {
        $this->identifier = $identifier;
        $this->status = self::STATUS_RUNNING;
        $this->start_date = date( 'Y-m-d H-i-s' );
    }
    
    public function report( $eventType, $detail ) {
        
    }

    public function generateReport( ) {

    }
    
    protected function getEvents( $eventType = null ) {
        
    }
    
    protected function countEvents( $eventType = null ) {
        
    }

}
