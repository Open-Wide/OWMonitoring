<?php

$INI = eZINI::instance( 'owmonitoring.ini' );
if( $INI->hasVariable( 'OWMonitoring', 'PrepareActiveReports' ) ) {
    $activeReports = $INI->variable( 'OWMonitoring', 'PrepareActiveReports' );
    foreach( $activeReports as $report ) {
        try {
            $report = OWMonitoringReport::prepareReport( $report );
        } catch(Exception $e) {
            echo $e->getMessage( ) . PHP_EOL;
        }
    }
}
