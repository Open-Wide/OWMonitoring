<?php

class eZLogReport_Error extends eZLogReport_BaseAnalyser {

    static function testAnalyseLog( ) {
        $analyser = new self( );
        $retult = $analyser->analyzeLogfile( 'var/log/error.log' );
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
        $lastAnalysis = eZSiteData::fetchByName( 'report_eZLogReport' );
        if( $lastAnalysis ) {
            $this->lastAnalysis = new DateTime( $lastAnalysis->attribute( 'value' ) );
        }
        foreach( $this->reportedList as $key => $value ) {
            $this->report[$key] = 0;
        }
    }

}
