<?php

class OWMonitoringTool {

    /* Send report to the monitoring tool */
    abstract public function sendReport( OWMonitoringReport $report );

    /* Send alert to the monitoring tool */
    abstract public function sendAlert( );

}
