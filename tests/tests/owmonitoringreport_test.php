<?php

class OWMonitoringReportTest extends ezpDatabaseTestCase {

    public function __construct( ) {
        parent::__construct( );
        $this->setName( "OWMonitoringReport Unit Tests" );
    }

    public function setUp( ) {
        parent::setUp( );
        $ini = eZINI::instance( 'owmonitoring.ini' );
        $ini->setVariables( array( 'OWMonitoringUnitTestMinute' => array(
                'Identifier' => 'test.minute',
                'PrepareFrequency' => 'minute',
                'Tests' => array( 'unit.test1' => 'UnitTest::UnitTest1' )
            ) ) );
        $ini->setVariables( array( 'OWMonitoringUnitTestWeekly' => array(
                'Identifier' => 'test.weekly',
                'PrepareFrequency' => 'weekly',
                'Tests' => array( 'unit.test1' => 'UnitTest::UnitTest1' )
            ) ) );
    }

    public function tearDown( ) {
        parent::tearDown( );
    }

    public function testfetchCount( ) {
        $this->assertEquals( OWMonitoringReport::fetchCount( ), 20 );

        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute' ), 4 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute', '2013-07-17 12:30:59' ), 2 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute', '2013-07-17 12:19:00', '2013-07-17 12:20:00' ), 0 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute', '2013-07-17 12:20:00', '2013-07-17 12:21:00' ), 1 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute', '2013-07-17 12:30:00', '2013-07-17 12:31:00' ), 1 );

        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour' ), 5 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour', '2013-07-17 00:30:00' ), 3 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour', '2013-07-17 00:00:00', '2013-07-17 00:15:00' ), 1 );

        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour' ), 5 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour', '2013-07-17 00:30:00' ), 3 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour', '2013-07-17 00:00:00', '2013-07-17 00:15:00' ), 1 );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.quarter_hour', '2013-07-17 01:00:00', '2013-07-17 01:15:00' ), 0 );
    }

    public function test__constructSansIndentifient( ) {
        $this->setExpectedException( 'Exception' );
        new OWMonitoringReport( );
    }

    public function test__constructIndentifientVide( ) {
        $this->setExpectedException( 'Exception', "OWMonitoringReport::__construct : Report identifier must be set" );
        new OWMonitoringReport( '' );
    }

    public function test__constructIndentifientOK( ) {
        $this->assertInstanceOf( 'OWMonitoringReport', new OWMonitoringReport( 'test.report' ) );
    }

    public function testGetIdentifier( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getIdentifier( ), 'test.report' );
    }

    public function testHasData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->hasData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->hasData( 'test' ), TRUE );
    }

    public function testGetData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), array( 'data' => 'test' ) );
    }

    public function testSetData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), array( 'data' => 'test' ) );
        $report->setData( 'test', 'test_2' );
        $this->assertEquals( $report->getData( 'test' ), array( 'data' => 'test_2' ) );
        $time = time( );
        $report->setData( 'test', 'test_2', $time );
        $this->assertEquals( $report->getData( 'test' ), array(
            'data' => 'test_2',
            'clock' => $time
        ) );
        $report->setData( 'test', array(
            'test',
            'test_2'
        ), $time );
        $this->assertEquals( $report->getData( 'test' ), array(
            array(
                'data' => 'test',
                'clock' => $time
            ),
            array(
                'data' => 'test_2',
                'clock' => $time
            )
        ) );

    }

    public function testAppendToData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), array( 'data' => 'test' ) );
        $report->appendToData( 'test', 'test_2' );
        $this->assertEquals( $report->getData( 'test' ), array(
            array( 'data' => 'test' ),
            array( 'data' => 'test_2' )
        ) );
    }

    public function testDeleteData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), array( 'data' => 'test' ) );
        $report->deleteData( 'test' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
    }

    public function testGetDatas( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getDatas( ), array( ) );
        $report->setData( 'test', 'test' );
        $report->setData( 'test_2', 'test_2' );
        $this->assertEquals( $report->getDatas( ), array(
            'test' => array( array( 'data' => 'test' ) ),
            'test_2' => array( array( 'data' => 'test_2' ) )
        ) );
    }

    public function testSetDatas( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getDatas( ), array( ) );
        $report->setDatas( array(
            'test' => 'test',
            'test_2' => 'test_2'
        ) );
        $this->assertEquals( $report->getDatas( ), array(
            'test' => array( array( 'data' => 'test' ) ),
            'test_2' => array( array( 'data' => 'test_2' ) )
        ) );
        $report->setDatas( array(
            'test' => array(
                'test_A',
                'test_B'
            ),
            'test_2' => 'test_2'
        ) );
        $this->assertEquals( $report->getDatas( ), array(
            'test' => array(
                array( 'data' => 'test_A' ),
                array( 'data' => 'test_B' )
            ),
            'test_2' => array( array( 'data' => 'test_2' ) )
        ) );
    }

    public function testSendReport( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $report->setDatas( array(
            'test' => 'test',
            'test_2' => 'test_2'
        ) );
        $this->assertEquals( $report->sendReport( ), TRUE );
    }

    public function testMakeReport( ) {
        $report = OWMonitoringReport::makeReport( 'OWMonitoringUnitTestMinute' );
        $this->assertEquals( $report->countDatas( ), 1 );
    }

    public function testPrepareReport( ) {
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.weekly' ), 2 );
        $report = OWMonitoringReport::prepareReport( 'OWMonitoringUnitTestWeekly' );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.weekly' ), 3 );
    }
    
    /**
     * @expectedException OWMonitoringReportException
     */
    public function testPrepareReportAlreadyExists( ) {
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute' ), 4 );
        $report = OWMonitoringReport::prepareReport( 'OWMonitoringUnitTestMinute' );
        $this->assertEquals( OWMonitoringReport::fetchCount( 'test.minute' ), 4 );

    }

}
?>