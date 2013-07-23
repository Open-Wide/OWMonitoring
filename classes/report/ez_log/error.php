<?php

class eZLogReport_Error extends eZLogReport_BaseAnalyser {

    static function testAnalyseLog( ) {
        $analyser = new self( );
        $retult = $analyser->analyzeLogfile( 'var/log/error.log' );
        $siteData = new eZSiteData( array(
            'name' => __CLASS__,
            'value' => date( 'Y-m-d H:i:s' )
        ) );
        $siteData->store( );
        return $retult;
    }

    public function __construct( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( 'eZLogReport', 'ErrorList' ) ) {
            $this->reportedList = $INI->variable( 'eZLogReport', 'ErrorList' );
        }
        if( $INI->hasVariable( 'eZLogReport', 'IgnoredErrorList' ) ) {
            $this->ignoredList = $INI->variable( 'eZLogReport', 'IgnoredErrorList' );
        }
        $lastAnalysis = eZSiteData::fetchByName( __CLASS__ );
        if( $lastAnalysis ) {
            $this->lastAnalysis = new DateTime( $lastAnalysis->attribute( 'value' ) );
        }
        foreach( $this->reportedList as $key => $value ) {
            $this->report[$key] = 0;
        }
    }

}
