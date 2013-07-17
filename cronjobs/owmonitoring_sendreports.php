<?php

$INI = eZINI::instance( 'owmonitoring.ini' );
if( $INI->hasVariable( 'OWMonitoring', 'ActiveReports' ) ) {
    $activeReports = $INI->variable( 'OWMonitoring', 'ActiveReports' );
    foreach( $activeReports as $report ) {
        try {
            $report = OWMonitoringReport::makeReport( $report );
            $report->sendReport( );
        } catch(Exception $e) {
            echo $e->getMessage( ) . PHP_EOL;
        }
    }
}
