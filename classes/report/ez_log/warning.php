<?php

class eZLogReport_Warning extends eZLogReport_BaseAnalyser {

    static function testAnalyseLog( ) {
        $analyser = new self( );
        $retult = $analyser->analyzeLogfile( 'var/log/warning.log' );
        $siteData = new eZSiteData( array(
            'name' => __CLASS__,
            'value' => date( 'Y-m-d H:i:s' )
        ) );
        $siteData->store( );
        return $retult;
    }

    public function __construct( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( 'eZLogReport', 'WarningList' ) ) {
            $this->reportedList = $INI->variable( 'eZLogReport', 'WarningList' );
        }
        if( $INI->hasVariable( 'eZLogReport', 'IgnoredWarningList' ) ) {
            $this->ignoredList = $INI->variable( 'eZLogReport', 'IgnoredWarningList' );
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
