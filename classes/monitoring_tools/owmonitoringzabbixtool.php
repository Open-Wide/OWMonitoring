<?php

require_once 'extension/owmonitoring/lib/php-zabbix-sender/vendor/autoload.php';
// using composer.phar

class OWMonitoringZabbixTool extends OWMonitoringTool {

    protected $agentConfig;
    protected $sender;
    protected $toolINI;

    static function instance( ) {
        $instance = &$GLOBALS['OWMonitoringZabbixToolGlobalInstance'];
        if( !$instance instanceof self ) {
            $instance = new self( );
            $GLOBALS['OWMonitoringZabbixToolGlobalInstance'] = $instance;

        }
        return $instance;
    }

    public function __construct( ) {
        $this->toolINI = eZINI::instance( 'owmonitoringtool.ini' );
        $this->agentConfig = new \Net\Zabbix\Agent\Config;
        $this->sender = new \Net\Zabbix\Sender( );
        $this->sender->setServerName( $this->toolINI->variable( 'Zabbix', 'ServerName' ) );
        $this->sender->setServerPort( $this->toolINI->variable( 'Zabbix', 'ServerPort' ) );
    }

    public function sendReport( OWMonitoringReport $report ) {
        $reportDataPrefix = $this->toolINI->variable( 'Zabbix', 'ReportDataPrefix' );
        $hostname = $this->toolINI->variable( 'Zabbix', 'Hostname' );
        $dataList = $report->getDatas( );
        foreach( $dataList as $name => $value ) {
            if( is_array( $value ) ) {
                foreach( $value as $valueItem ) {
                    $this->sender->addData( $hostname, $reportDataPrefix . '.' . $name, $valueItem );
                }
            } else {
                $this->sender->addData( $hostname, $reportDataPrefix . '.' . $name, $value );
            }
        }
        $result = $this->sender->send( );
        if( $result ) {
            $this->logSendResult( );
        } else {
            throw new Exception( "Send report failed" );

        }
    }

    public function sendAlert( ) {

    }

    protected function logSendResult( ) {
        $info = $this->sender->getLastResponseInfo( );
        $data = $this->sender->getLastResponseArray( );
        echo "request result: success\n";
        echo "response info: $info\n";
        echo "response data:\n";
        var_dump( $data );

        $processed = $this->sender->getLastProcessed( );
        $failed = $this->sender->getLastFailed( );
        $total = $this->sender->getLastTotal( );
        $spent = $this->sender->getLastSpent( );
        echo "parsedInfo: processed = $processed\n";
        echo "parsedInfo: failed    = $failed\n";
        echo "parsedInfo: total     = $total\n";
        echo "parsedInfo: spent     = $spent\n";
    }

}
