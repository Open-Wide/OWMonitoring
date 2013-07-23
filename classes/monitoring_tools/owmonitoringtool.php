<?php

abstract class OWMonitoringTool {
    
    const SENDING_SUCCESSFUL = 0;
    const SENDING_INCOMPLETE = 1;
    const SENDING_FAILED = 2;

    /* Send report to the monitoring tool */
    abstract public function sendReport( OWMonitoringReport $report );

    /* Send alert to the monitoring tool */
    abstract public function sendAlert( );

}
