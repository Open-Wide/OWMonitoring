<?php

require_once 'tests/owmonitoringreport_test.php';

class OWMonitiringTestSuite extends ezpDatabaseTestSuite {
    public function __construct( ) {
        parent::__construct( );
        $this->insertDefaultData = false;
        $this->setName( "OW Monitoring Test Suite" );
        $this->addTestSuite( 'OWMonitoringReportTest' );
        $this->addTestSuite( 'OWMonitoringZabbixToolTest' );
    }

    public static function suite( ) {
        return new self( );
    }

}
?>