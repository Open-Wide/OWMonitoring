<?php

$INI = eZINI::instance( 'owmonitoring.ini' );
if( $INI->hasVariable( 'OWMonitoring', 'SendActiveReports' ) ) {
    $activeReports = $INI->variable( 'OWMonitoring', 'SendActiveReports' );
    foreach( $activeReports as $report ) {
        try {
            $report = OWMonitoringReport::makeReport( $report );
            $report->sendReport( );
        } catch(Exception $e) {
            echo $e->getMessage( ) . PHP_EOL;
        }
    }
}
