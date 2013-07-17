<?php

class OWMonitoringZabbixTool extends OWMonitoringTool {

    protected $agentConfig;
    protected $sender;
    protected $checkINI = FALSE;
    protected $serverName;
    protected $serverPort;
    protected $reportDataPrefix;
    protected $hostname;

    static function instance( ) {
        if( !isset( $GLOBALS['OWMonitoringZabbixToolGlobalInstance'] ) || !($GLOBALS['OWMonitoringZabbixToolGlobalInstance'] instanceof self) ) {
            $GLOBALS['OWMonitoringZabbixToolGlobalInstance'] = new self( );
        }
        return $GLOBALS['OWMonitoringZabbixToolGlobalInstance'];
    }

    public function __construct( ) {
        if( !$this->checkINISettings( ) ) {
            return FALSE;
        }
        $this->agentConfig = new \Net\Zabbix\Agent\Config;
        $this->sender = new \Net\Zabbix\Sender( );
        $this->sender->setServerName( $this->serverName );
        $this->sender->setServerPort( $this->serverPort );
    }

    public function sendReport( OWMonitoringReport $report ) {
        if( !$this->checkINI ) {
            OWMonitoringLogger::writeError( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix. Bad configuration." );
            return FALSE;
        }
        $reportDataPrefix = $this->reportDataPrefix . '.' . $report->getIdentifier( );
        $dataList = $report->getDatas( );
        $dataIDList = array( );
        foreach( $dataList as $name => $valueArray ) {
            foreach( $valueArray as $valueItem ) {
                $clock = isset( $valueItem['clock'] ) ? $valueItem['clock'] : NULL;
                $this->sender->addData( $this->hostname, $reportDataPrefix . '.' . $name, $valueItem['data'], $clock );
                $dataIDList[] = $reportDataPrefix . '.' . $name;
            }
        }
        try {
            $result = $this->sender->send( );
            $info = $this->sender->getLastResponseInfo( );
            $data = $this->sender->getLastResponseArray( );
            $resultLog = ">> request result: " . $data['response'] . "\n";

            $processed = $this->sender->getLastProcessed( );
            $failed = $this->sender->getLastFailed( );
            $total = $this->sender->getLastTotal( );
            $spent = $this->sender->getLastSpent( );
            $resultLog .= sprintf( ">> parsedInfo: processed = %d\n", $processed );
            $resultLog .= sprintf( ">> parsedInfo: failed    = %d\n", $failed );
            $resultLog .= sprintf( ">> parsedInfo: total     = %d\n", $total );
            $resultLog .= sprintf( ">> parsedInfo: spent     = %f sec\n", $spent );
            $resultLog .= sprintf( ">> Send data list        = %s\n", implode( ', ', $dataIDList ) );
            if( $failed == 0 ) {
                OWMonitoringLogger::writeNotice( $report->getIdentifier( ) . " report has been successfully sent to Zabbix.\n" . $resultLog );
                return TRUE;
            } else {
                OWMonitoringLogger::writeWarning( $report->getIdentifier( ) . " report has been successfully sent to Zabbix but some data failed.\n" . $resultLog );
                return TRUE;
            }
        } catch( Exception $e ) {
            OWMonitoringLogger::writeNotice( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix.\n" . $e->getMessage( ) );
            return FALSE;
        }
    }

    public function sendAlert( ) {

    }

    protected function checkINISettings( ) {
        $toolINI = eZINI::instance( 'owmonitoringtool.ini' );
        $this->checkINI = TRUE;

        if( !$toolINI->hasVariable( 'Zabbix', 'ServerName' ) ) {
            OWMonitoringLogger::writeError( "[Zabbix]ServerName not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->serverName = $toolINI->variable( 'Zabbix', 'ServerName' );
            if( empty( $this->serverName ) ) {
                OWMonitoringLogger::writeError( "[Zabbix]ServerName is empty" );
                $this->checkINI = FALSE;
            }
        }

        if( !$toolINI->hasVariable( 'Zabbix', 'ServerPort' ) ) {
            OWMonitoringLogger::writeError( "[Zabbix]ServerPort not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->serverPort = $toolINI->variable( 'Zabbix', 'ServerPort' );
            if( empty( $this->serverPort ) ) {
                OWMonitoringLogger::writeNotice( "[Zabbix]serverPort is empty. Use default port 10051." );
                $this->serverPort = 10051;
            }
        }

        if( !$toolINI->hasVariable( 'Zabbix', 'ReportDataPrefix' ) ) {
            OWMonitoringLogger::writeError( "[Zabbix]ReportDataPrefix not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->reportDataPrefix = $toolINI->variable( 'Zabbix', 'ReportDataPrefix' );
            if( empty( $this->reportDataPrefix ) ) {
                OWMonitoringLogger::writeError( "[Zabbix]ReportDataPrefix is empty" );
                $this->checkINI = FALSE;
            }
        }

        if( !$toolINI->hasVariable( 'Zabbix', 'Hostname' ) ) {
            OWMonitoringLogger::writeError( "[Zabbix]Hostname not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->hostname = $toolINI->variable( 'Zabbix', 'Hostname' );
            if( empty( $this->hostname ) ) {
                OWMonitoringLogger::writeError( "[Zabbix]Hostname is empty" );
                $this->checkINI = FALSE;
            }
        }
        return $this->checkINI;
    }

}
