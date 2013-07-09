<?php

class OWMonitoringReportTest extends ezpTestCase {
    public function __construct( ) {
        parent::__construct( );
        $this->setName( "OWMonitoringReport Unit Tests" );
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
        $this->assertEquals( $report->getData( 'test' ), 'test' );
    }

    public function testSetData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), 'test' );
        $report->setData( 'test', 'test_2' );
        $this->assertEquals( $report->getData( 'test' ), 'test_2' );
    }

    public function testAppendToData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), 'test' );
        $report->appendToData( 'test', 'test_2' );
        $this->assertEquals( $report->getData( 'test' ), array(
            'test',
            'test_2'
        ) );
    }

    public function testDeleteData( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
        $report->setData( 'test', 'test' );
        $this->assertEquals( $report->getData( 'test' ), 'test' );
        $report->deleteData( 'test' );
        $this->assertEquals( $report->getData( 'test' ), FALSE );
    }

    public function testGetDatas( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $this->assertEquals( $report->getDatas( ), array( ) );
        $report->setData( 'test', 'test' );
        $report->setData( 'test_2', 'test_2' );
        $this->assertEquals( $report->getDatas( ), array(
            'test' => 'test',
            'test_2' => 'test_2'
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
            'test' => 'test',
            'test_2' => 'test_2'
        ) );
    }

    public function testSendReport( ) {
        $report = new OWMonitoringReport( 'test.report' );
        $report->setDatas( array(
            'test' => 'test',
            'test_2' => 'test_2'
        ) );
        try {
            $this->assertEquals( $report->sendReport(), FALSE );
        } catch (Exception $e) {
            $this->fail('sendReport raise an exception');
        }
    }
    
    public function testMakeReport() {
        self::markTestIncomplete( "Not implemented" );
    }

}
?>