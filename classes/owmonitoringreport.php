<?php

class OWMonitoringReport extends eZPersistentObject {

    protected $reportData = array();

    public function __construct( $identifier_or_row ) {
        if ( empty( $identifier_or_row ) ) {
            throw new OWMonitoringReportException( __METHOD__ . " : Report identifier must be set" );
        }
        if ( is_string( $identifier_or_row ) ) {
            $identifier_or_row = array( 'identifier' => $identifier_or_row );
        }
        parent::__construct( $identifier_or_row );
        $serializedData = $this->attribute( 'serialized_data' );
        if ( !empty( $serializedData ) ) {
            $this->reportData = unserialize( $serializedData );
        }
        if ( $this->attribute( 'date' ) == NULL ) {
            $this->setAttribute( 'date', date( 'Y-m-d H:i:s' ) );
        }
    }

    public function getIdentifier() {
        return $this->attribute( 'identifier' );
    }

    public function getClock() {
        if ( $this->attribute( 'date' ) != NULL ) {
            return strtotime( $this->attribute( 'date' ) );
        }
    }

    public function hasData( $name ) {
        return array_key_exists( $name, $this->reportData );
    }

    public function getData( $name ) {
        if ( $this->hasData( $name ) ) {
            if ( count( $this->reportData[$name] ) == 1 ) {
                return $this->reportData[$name][0];
            } else {
                return $this->reportData[$name];
            }
        }
        return NULL;
    }

    public function setData( $name, $data, $clock = null ) {
        if ( !is_string( $name ) ) {
            return FALSE;
        }
        if ( is_array( $data ) && array_keys( $data ) !== range( 0, count( $data ) - 1 ) ) {
// associative array
            foreach ( $data as $key => $value ) {
                $this->setData( $name . '.' . $key, $value, $clock );
            }
            return TRUE;
        }
        if ( !is_array( $data ) ) {
            $data = array( $data );
        }
        $newData = array();
        foreach ( $data as $dataItem ) {
            $newDataItem = array( 'data' => $dataItem );
            if ( $clock ) {
                $newDataItem['clock'] = $clock;
            }
            $newData[] = $newDataItem;
        }

        $this->reportData[$name] = $newData;
        return TRUE;
    }

    public function appendToData( $name, $data, $clock = null ) {
        if ( $this->hasData( $name ) ) {
            $currentData = $this->reportData[$name];
            $this->setData( $name, $data, $clock );
            $newData = $this->reportData[$name];
            $data = array_merge( $currentData, $newData );
            $this->reportData[$name] = $data;
        } else {
            $this->setData( $name, $data, $clock );
        }
    }

    public function deleteData( $name ) {
        if ( $this->hasData( $name ) ) {
            unset( $this->reportData[$name] );
        }
    }

    public function getHostnames() {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if ( $INI->hasVariable( $this->attribute( 'report_name' ), 'Hostname' ) ) {
            return $INI->variable( $this->attribute( 'report_name' ), 'Hostname' );
        }
        return array();
    }

    public function getDatas() {
        return $this->reportData;
    }

    public function countDatas() {
        return count( $this->reportData );
    }

    public function setDatas( $dataArray, $clock = null ) {
        foreach ( $dataArray as $name => $data ) {
            $this->setData( $name, $data, $clock );
        }
    }

    public function sendReport() {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        $sendByMail = false;
        $sendToMonitoringTool = false;
        $sendByMailResult = true;
        $sendToMonitoringToolResult = true;
        $sendingChannels = array();
        if ( $INI->hasVariable( $this->attribute( 'report_name' ), 'SendingChannels' ) ) {
            $sendingChannels = $INI->variable( $this->attribute( 'report_name' ), 'SendingChannels' );
        } elseif ( $INI->hasVariable( 'OWMonitoring', 'DefaultSendingChannels' ) ) {
            $sendingChannels = $INI->variable( 'OWMonitoring', 'DefaultSendingChannels' );
        }
        if ( $sendingChannels ) {
            if ( in_array( 'Mail', $sendingChannels ) ) {
                $sendByMail = true;
            }
            if ( in_array( 'MonitoringTool', $sendingChannels ) ) {
                $sendToMonitoringTool = true;
            }
        } else {
            OWMonitoringLogger::logError( __METHOD__ . " : No sending channel not defined in owmonitoring.ini" );
            return FALSE;
        }
        if ( $sendByMail && !$this->attribute( 'mail_sent' ) ) {
            $sendByMailResult = $this->sendByMail();
            if ( $sendByMailResult ) {
                $this->setAttribute( 'mail_sent', date( 'Y-m-d H:i:s' ) );
                $this->store();
            }
        }
        if ( $sendToMonitoringTool && $this->attribute( 'status' ) != OWMonitoringTool::SENDING_SUCCESSFUL ) {
            if ( !$INI->hasVariable( 'OWMonitoring', 'MonitoringToolClass' ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : [OWMonitoring]MonitoringToolClass not defined in owmonitoring.ini" );
                return FALSE;
            }
            $monitoringToolClass = $INI->variable( 'OWMonitoring', 'MonitoringToolClass' );
            if ( !class_exists( $monitoringToolClass ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : Class $monitoringToolClass not found" );
                return FALSE;
            }

            if ( !$INI->hasVariable( $this->attribute( 'report_name' ), 'Hostname' ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : [" . $this->attribute( 'report_name' ) . "]Hostname not defined in owmonitoring.ini" );
                return FALSE;
            }
            $hostnameList = $INI->variable( $this->attribute( 'report_name' ), 'Hostname' );
            if ( !is_array( $hostnameList ) && empty( $hostnameList ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : [" . $this->attribute( 'report_name' ) . "]Hostname must be an nonempty array." );
                return FALSE;
            }
            $tool = $monitoringToolClass::instance();
            $tool->setHostnameList( $hostnameList );
            $sendingResult = $tool->sendReport( $this );
            $this->setAttribute( 'request_result', $sendingResult['request_result'] );
            $this->setAttribute( 'processed', $sendingResult['processed'] );
            $this->setAttribute( 'failed', $sendingResult['failed'] );
            $this->setAttribute( 'total', $sendingResult['total'] );
            $this->setAttribute( 'status', $sendingResult['status'] );
            $this->setAttribute( 'last_sending', date( 'Y-m-d H:i:s' ) );
            $this->store();
            if ( $sendingResult['status'] != OWMonitoringTool::SENDING_SUCCESSFUL ) {
                $sendToMonitoringToolResult = false;
                OWMonitoringLogger::logNotice( __METHOD__ . " : Report " . $this->getIdentifier() . " is stored in the database for next attempt" );
            }
        }
        if ( $sendByMailResult && $sendToMonitoringToolResult ) {
            $this->remove();
        }
        return;
    }

    protected function sendByMail() {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'report', $this );
        if ( $INI->hasVariable( $this->attribute( 'report_name' ), 'EmailReceivers' ) ) {
            $receivers = $INI->variable( $this->attribute( 'report_name' ), 'EmailReceivers' );
        } elseif ( $INI->hasVariable( 'OWMonitoring', 'DefaultEmailReceivers' ) ) {
            $receivers = $INI->variable( 'OWMonitoring', 'DefaultEmailReceivers' );
        } else {
            $receivers = array();
        }

        $ini = eZINI::instance();
        $mail = new eZMail();

        $templateResult = $tpl->fetch( 'design:owmonitoring/reportmail.tpl' );
        $mail->setBody( $templateResult );
        if ( $tpl->hasVariable( 'content_type' ) ) {
            $mail->setContentType( $tpl->variable( 'content_type' ) );
        }

        if ( $tpl->hasVariable( 'subject' ) ) {
            $subject = $tpl->variable( 'subject' );
        } else {
            $subject = 'Report ' . $this->attribute( 'report_name' ) . ' ' . implode( ', ', $this->attribute( 'hostnames' ) );
        }
        $mail->setSubject( $subject );

        if ( $tpl->hasVariable( 'email_receiver' ) ) {
            $receiver = $tpl->variable( 'email_receiver' );
        } else {
            if ( is_array( $receivers ) ) {
                $receiver = array_shift( $receivers );
            } else {
                $receiver = $receivers;
            }
        }
        if ( !$mail->validate( $receiver ) ) {
            $receiver = $ini->variable( "InformationCollectionSettings", "EmailReceiver" );
            if ( !$receiver ) {
                $receiver = $ini->variable( "MailSettings", "AdminEmail" );
            }
        }
        $mail->setReceiver( $receiver );

        $sender = $tpl->variable( 'email_sender' );
        if ( !$mail->validate( $sender ) ) {
            $sender = $ini->variable( "MailSettings", "EmailSender" );
        }
        $mail->setSender( $sender );

        $replyTo = $tpl->variable( 'email_reply_to' );
        if ( !$mail->validate( $replyTo ) ) {
// If replyTo address is not set in the template, take it from the settings
            $replyTo = $ini->variable( "MailSettings", "EmailReplyTo" );
            if ( !$mail->validate( $replyTo ) ) {
// If replyTo address is not set in the settings, use the sender address
                $replyTo = $sender;
            }
        }
        $mail->setReplyTo( $replyTo );

// Handle CC recipients
        if ( $tpl->hasVariable( 'email_cc_receivers' ) ) {
            $ccReceivers = $tpl->variable( 'email_cc_receivers' );
        } else {
            if ( is_array( $receivers ) ) {
                $ccReceivers = $receivers;
            }
        }
        if ( $ccReceivers ) {
            if ( !is_array( $ccReceivers ) ) {
                $ccReceivers = array( $ccReceivers );
            }
            foreach ( $ccReceivers as $ccReceiver ) {
                if ( $mail->validate( $ccReceiver ) ) {
                    $mail->addCc( $ccReceiver );
                }
            }
        }


// Handle BCC recipients
        $bccReceivers = $tpl->variable( 'email_bcc_receivers' );
        if ( $bccReceivers ) {
            if ( !is_array( $bccReceivers ) ) {
                $bccReceivers = array( $bccReceivers );
            }

            foreach ( $bccReceivers as $bccReceiver ) {
                if ( $mail->validate( $bccReceiver ) ) {
                    $mail->addBcc( $bccReceiver );
                }
            }
        }
        $sending = eZMailTransport::send( $mail );
        if ( $sending ) {
            OWMonitoringLogger::logNotice( __METHOD__ . " : Report " . $this->getIdentifier() . " successfully send to $receiver" );
        } else {
            OWMonitoringLogger::logError( __METHOD__ . " : Report " . $this->getIdentifier() . " sending failed" );
        }
        return $sending;
    }

    static function prepareReport( $reportName ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if ( !$INI->hasVariable( $reportName, 'Identifier' ) ) {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Identifier not defined in owmonitoring.ini" );
        }
        if ( $INI->hasVariable( $reportName, 'PrepareFrequency' ) ) {
            $prepareFrequency = $INI->variable( $reportName, 'PrepareFrequency' );
            switch ( $prepareFrequency ) {
                case 'minute' :
                    $fromDate = new DateTime( date( 'Y-m-d H:i:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 minute' );
                    break;
                case 'quarter_hour' :
                    $date = new DateTime( date( 'Y-m-d H:i:00' ) );
                    $quarter = intval( $date->format( 'i' ) / 15 );
                    $fromDate = new DateTime( date( 'Y-m-d H:' . (15 * $quarter) . ':00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+15 minute' );
                    break;
                case 'hourly' :
                    $fromDate = new DateTime( date( 'Y-m-d H:00:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 hour' );
                    break;
                case 'daily' :
                    $fromDate = new DateTime( date( 'Y-m-d 00:00:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 day' );
                    break;
                case 'weekly' :
                    $fromDate = new DateTime( date( 'Y-m-d 00:00:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 week' );
                    break;
                case 'monthly' :
                    $fromDate = new DateTime( date( 'Y-m-01 00:00:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 month' );
                    break;
                default :
                    throw new OWMonitoringReportException( __METHOD__ . " : bad frequency" );
            }
            $lastReport = eZSiteData::fetchByName( 'report_' . $reportName );
            if ( $lastReport ) {
                $lastReportDate = new DateTime( $lastReport->attribute( 'value' ) );
                if ( $lastReportDate >= $fromDate && $lastReportDate < $toDate ) {
                    throw new OWMonitoringReportException( __METHOD__ . " : $reportName already exits" );
                }
            }
        }
        $report = self::makeReport( $reportName );
        $report->store();
        $siteData = new eZSiteData( array(
            'name' => 'report_' . $reportName,
            'value' => date( 'Y-m-d H:i:s' )
            ) );
        $siteData->store();
        OWMonitoringLogger::logNotice( __METHOD__ . " : Report " . $report->getIdentifier() . " is stored in the database" );
    }

    static function makeReport( $reportName, $forceClock = NULL ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if ( $INI->hasVariable( $reportName, 'Identifier' ) ) {
            $identifier = $INI->variable( $reportName, 'Identifier' );
        } else {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Identifier not defined in owmonitoring.ini" );
        }
        if ( $INI->hasVariable( $reportName, 'Tests' ) ) {
            $testList = $INI->variable( $reportName, 'Tests' );
        } else {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Tests not defined in owmonitoring.ini" );
        }
        try {
            $report = new OWMonitoringReport( $identifier );
            $report->setAttribute( 'report_name', $reportName );
        } catch ( Exception $e ) {
            throw new OWMonitoringReportException( __METHOD__ . " : Report instancation failed\n" . $e->getMessage() );
        }
        if ( $forceClock === TRUE ) {
            $forceClock = time();
        }
        foreach ( $testList as $testIdentifier => $testFunction ) {
            $storeValue = TRUE;
            list( $testClass, $testMethod ) = explode( '::', $testFunction );
            $testClass = $reportName . '_' . $testClass;
            $testMethod = 'test' . $testMethod;
            $testFunction = $testClass . '::' . $testMethod;
            if ( !class_exists( $testClass ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : Class $testClass not found" );
                continue;
            } elseif ( !is_callable( $testFunction ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : Can not call $testFunction method" );
                continue;
            } else {
                try {
                    $testValue = call_user_func( $testFunction );
                } catch ( OWMonitoringReportNoValueException $e ) {
                    OWMonitoringLogger::logError( __METHOD__ . " : " . $e->getMessage() );
                    $storeValue = FALSE;
                }
                if ( $storeValue ) {
                    $report->setData( $testIdentifier, $testValue, $forceClock );
                }
            }
        }
        if ( $report->countDatas() == 0 ) {
            throw new OWMonitoringReportException( __METHOD__ . " : Report is empty" );
        }
        return $report;
    }

    /* eZPersistentObject methods */

    public static function definition() {
        return array(
            'fields' => array(
                'report_name' => array(
                    'name' => 'report_name',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'identifier' => array(
                    'name' => 'identifier',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'date' => array(
                    'name' => 'date',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'serialized_data' => array(
                    'name' => 'serialized_data',
                    'datatype' => 'text',
                    'default' => null,
                    'required' => true
                ),
                'request_result' => array(
                    'name' => 'request_result',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'processed' => array(
                    'name' => 'processed',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => false
                ),
                'failed' => array(
                    'name' => 'failed',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => false
                ),
                'total' => array(
                    'name' => 'total',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => false
                ),
                'status' => array(
                    'name' => 'status',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => false
                ),
                'last_sending' => array(
                    'name' => 'last_sending',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'mail_sent' => array(
                    'name' => 'mail_sent',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
            ),
            'keys' => array(
                'identifier',
                'date'
            ),
            'class_name' => 'OWMonitoringReport',
            'name' => 'owmonitoring_report',
            'function_attributes' => array(
                'datas' => 'getDatas',
                'hostnames' => 'getHostnames',
            ),
            'set_functions' => array()
        );
    }

    function store( $fieldFilters = NULL ) {
        $this->setAttribute( 'serialized_data', serialize( $this->reportData ) );
        parent::store( $fieldFilters );
    }

    static function fetch( $identifier, $fromDate = NULL, $toDate = NULL ) {
        $rows = self::fetchList( $identifier, $fromDate, $toDate, 1 );
        if ( isset( $rows[0] ) ) {
            return $rows[0];
        }
        return null;
    }

    static function fetchList( $identifier = NULL, $fromDate = NULL, $toDate = NULL, $limit = NULL ) {
        $conds = array();
        if ( $identifier ) {
            $conds[] = "identifier LIKE '$identifier'";
        }
        if ( $fromDate ) {
            $conds[] = "date >= '$fromDate'";
        }
        if ( $toDate ) {
            $conds[] = "date < '$toDate'";
        }
        if ( !empty( $conds ) ) {
            $conds = ' WHERE ' . implode( ' AND ', $conds );
        }
        return self::fetchObjectList( self::definition(), null, null, array(
                'identifier' => 'asc',
                'date' => 'asc'
                ), $limit, true, false, null, null, $conds );
    }

    static function fetchCount( $identifier = NULL, $fromDate = NULL, $toDate = NULL ) {
        return count( self::fetchList( $identifier, $fromDate, $toDate ) );
    }

    static function removeOldReports( $reportLifetime ) {
        return self::removeObject( self::definition(), array( 'date' => array(
                    '>',
                    date( 'Y-m-d H:i:s', strtotime( '+' . $reportLifetime . ' minute' ) )
            ) ) );
    }

}
