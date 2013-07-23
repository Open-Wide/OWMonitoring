<?php

class eZLogReport_Error extends eZLogReport_BaseAnalyser {

    static function testAnalyseLog( ) {
        $analyser = new self( );
        return $analyser->analyzeLogfile( 'var/log/error.log' );
    }

    public function __construct( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( 'eZLogReport', 'ErrorList' ) ) {
            $this->reportedList = $INI->variable( 'eZLogReport', 'ErrorList' );
        }
        if( $INI->hasVariable( 'eZLogReport', 'IgnoredErrorList' ) ) {
            $this->ignoredList = $INI->variable( 'eZLogReport', 'IgnoredErrorList' );
        }
    }

}
