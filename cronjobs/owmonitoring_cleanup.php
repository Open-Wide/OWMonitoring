<?php

$INI = eZINI::instance( 'owmonitoring.ini' );
if( $INI->hasVariable( 'OWMonitoring', 'OldReportCleanup' ) && $INI->variable( 'OWMonitoring', 'OldReportCleanup' ) != 0 ) {
    $oldReport = $INI->variable( 'OWMonitoring', 'OldReportCleanup' );
    OWMonitoringReport::removeOldReports( $oldReport );
}
