<?php

class OWMonitoringZabbixTool extends OWMonitoringTool {

    protected $agentConfig;
    protected $sender;
    protected $checkINI = FALSE;
    protected $serverName;
    protected $serverPort;
    protected $hostnameList;

    static function instance( ) {
        if( !isset( $GLOBALS['OWMonitoringZabbixToolGlobalInstance'] ) || !($GLOBALS['OWMonitoringZabbixToolGlobalInstance'] instanceof self) ) {
            $GLOBALS['OWMonitoringZabbixToolGlobalInstance'] = new self( );
        }
        return $GLOBALS['OWMonitoringZabbixToolGlobalInstance'];
    }

    public function setHostnameList( $hostnameList ) {
        $this->hostnameList = $hostnameList;
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
            OWMonitoringLogger::logError( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix. Bad configuration." );
            return FALSE;
        }
        if( !is_array( $this->hostnameList ) && empty( $this->hostnameList ) ) {
            OWMonitoringLogger::logError( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix. The hostname is not defined." );
            return FALSE;
        }
        $dataList = $report->getDatas( );
        $defaultClock = $report->getClock( );
        $dataIDList = array( );
        if( empty( $dataList ) ) {
            OWMonitoringLogger::logError( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix. Report is empty." );
            return FALSE;
        }
        foreach( $dataList as $name => $valueArray ) {
            foreach( $valueArray as $valueItem ) {
                $clock = isset( $valueItem['clock'] ) ? $valueItem['clock'] : $defaultClock;
                foreach( $this->hostnameList as $hostname ) {
                    $this->sender->addData( $hostname, $report->getIdentifier( ) . '.' . $name, $valueItem['data'], $clock );
                }
                $dataIDList[] = $report->getIdentifier( ) . '.' . $name;
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
            $resultArray = array(
                'request_result' => $data['response'],
                'processed' => $processed,
                'failed' => $failed,
                'total' => $total
            );
            if( $failed == 0 ) {
                OWMonitoringLogger::logNotice( $report->getIdentifier( ) . " report has been successfully sent to Zabbix.\n" . $resultLog );
                $resultArray['status'] = self::SENDING_SUCCESSFUL;
            } else {
                OWMonitoringLogger::logWarning( $report->getIdentifier( ) . " report has been successfully sent to Zabbix but some data failed.\n" . $resultLog );
                $resultArray['status'] = self::SENDING_INCOMPLETE;
            }
        } catch( Exception $e ) {
            OWMonitoringLogger::logError( "Report " . $report->getIdentifier( ) . " can not be sent to Zabbix.\n" . $e->getMessage( ) );
            return array(
                'request_result' => $e->getMessage( ),
                'processed' => 0,
                'failed' => $report->countDatas( ),
                'total' => $report->countDatas( ),
                'status' => self::SENDING_FAILED
            );
        }
        return $resultArray;
    }

    public function sendAlert( ) {

    }

    protected function checkINISettings( ) {
        $toolINI = eZINI::instance( 'owmonitoringtool.ini' );
        $this->checkINI = TRUE;

        if( !$toolINI->hasVariable( 'Zabbix', 'ServerName' ) ) {
            OWMonitoringLogger::logError( "[Zabbix]ServerName not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->serverName = $toolINI->variable( 'Zabbix', 'ServerName' );
            if( empty( $this->serverName ) ) {
                OWMonitoringLogger::logError( "[Zabbix]ServerName is empty" );
                $this->checkINI = FALSE;
            }
        }

        if( !$toolINI->hasVariable( 'Zabbix', 'ServerPort' ) ) {
            OWMonitoringLogger::logError( "[Zabbix]ServerPort not defined in owmonitoringtool.ini" );
            $this->checkINI = FALSE;
        } else {
            $this->serverPort = $toolINI->variable( 'Zabbix', 'ServerPort' );
            if( empty( $this->serverPort ) ) {
                OWMonitoringLogger::logNotice( "[Zabbix]serverPort is empty. Use default port 10051." );
                $this->serverPort = 10051;
            }
        }
        return $this->checkINI;
    }

}
