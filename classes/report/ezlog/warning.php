<?php

class eZLogReport_Warning extends eZLogReport_BaseAnalyser {

    static function testAnalyseLog( ) {
        $analyser = new self( );
        $result = $analyser->analyzeLogfile( 'var/log/warning.log' );
        return $result;
    }

    public function __construct( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( 'eZLogReport', 'WarningList' ) ) {
            $this->reportedList = $INI->variable( 'eZLogReport', 'WarningList' );
        }
        if( $INI->hasVariable( 'eZLogReport', 'IgnoredWarningList' ) ) {
            $this->ignoredList = $INI->variable( 'eZLogReport', 'IgnoredWarningList' );
        }
        $lastAnalysis = eZSiteData::fetchByName( 'report_eZLogReport' );
        if( $lastAnalysis ) {
            $this->lastAnalysis = new DateTime( $lastAnalysis->attribute( 'value' ) );
        }
        foreach( array_keys( $this->reportedList ) as $key ) {
            $this->report[$key] = 0;
        }
    }

}
