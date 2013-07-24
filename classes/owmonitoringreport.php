<?php

class OWMonitoringReport extends eZPersistentObject {

    protected $identifier;
    protected $reportData;
    protected $date;
    protected $serialized_data;

    protected $request_result;
    protected $processed;
    protected $failed;
    protected $total;
    protected $status;
    protected $last_sending;

    public function __construct( $identifier_or_row ) {
        if( is_array( $identifier_or_row ) ) {
            parent::__construct( $identifier_or_row );
        } else {
            if( empty( $identifier_or_row ) ) {
                throw new OWMonitoringReportException( __METHOD__ . " : Report identifier must be set" );
            }
            $this->identifier = $identifier_or_row;
            $this->reportData = array( );
        }
        if( !empty( $this->serialized_data ) ) {
            $this->reportData = unserialize( $this->attribute( 'serialized_data' ) );
        }
    }

    public function getIdentifier( ) {
        return $this->identifier;
    }

    public function getClock( ) {
        if( $this->attribute( 'date' ) != NULL ) {
            return strtotime( $this->attribute( 'date' ) );
        }
    }

    public function hasData( $name ) {
        return array_key_exists( $name, $this->reportData );
    }

    public function getData( $name ) {
        if( $this->hasData( $name ) ) {
            if( count( $this->reportData[$name] ) == 1 ) {
                return $this->reportData[$name][0];
            } else {
                return $this->reportData[$name];
            }
        }
        return NULL;
    }

    public function setData( $name, $data, $clock = null ) {
        if( !is_string( $name ) ) {
            return FALSE;
        }
        if( is_array( $data ) && array_keys( $data ) !== range( 0, count( $data ) - 1 ) ) {
            // associative array
            foreach( $data as $key => $value ) {
                $this->setData( $name . '.' . $key, $value, $clock );
            }
            return TRUE;
        }
        if( !is_array( $data ) ) {
            $data = array( $data );
        }
        $newData = array( );
        foreach( $data as $dataItem ) {
            $newDataItem = array( 'data' => $dataItem );
            if( $clock ) {
                $newDataItem['clock'] = $clock;
            }
            $newData[] = $newDataItem;
        }

        $this->reportData[$name] = $newData;
        return TRUE;
    }

    public function appendToData( $name, $data, $clock = null ) {
        if( $this->hasData( $name ) ) {
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
        if( $this->hasData( $name ) ) {
            unset( $this->reportData[$name] );
        }
    }

    public function getDatas( ) {
        return $this->reportData;
    }

    public function countDatas( ) {
        return count( $this->reportData );
    }

    public function setDatas( $dataArray, $clock = null ) {
        foreach( $dataArray as $name => $data ) {
            $this->setData( $name, $data, $clock );
        }
    }

    public function sendReport( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( !$INI->hasVariable( 'OWMonitoring', 'MonitoringToolClass' ) ) {
            OWMonitoringLogger::logError( __METHOD__ . " : [OWMonitoring]MonitoringToolClass not defined in owmonitoring.ini" );
            return FALSE;
        }
        $monitoringToolClass = $INI->variable( 'OWMonitoring', 'MonitoringToolClass' );
        if( !class_exists( $monitoringToolClass ) ) {
            OWMonitoringLogger::logError( __METHOD__ . " : Class $monitoringToolClass not found" );
            return FALSE;
        }
        $tool = $monitoringToolClass::instance( );
        $sendingResult = $tool->sendReport( $this );
        switch ($sendingResult['status']) {
            case OWMonitoringTool::SENDING_SUCCESSFUL :
                $this->remove( );
                break;
            default :
                $this->setAttribute( 'request_result', $sendingResult['request_result'] );
                $this->setAttribute( 'processed', $sendingResult['processed'] );
                $this->setAttribute( 'failed', $sendingResult['failed'] );
                $this->setAttribute( 'total', $sendingResult['total'] );
                $this->setAttribute( 'status', $sendingResult['status'] );
                $this->setAttribute( 'last_sending', date( 'Y-m-d H:i:s' ) );
                $this->store( );
                OWMonitoringLogger::logNotice( __METHOD__ . " : Report " . $this->getIdentifier( ) . " is stored in the database for next attempt" );
                break;
        }
        return;
    }

    static function prepareReport( $reportName ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( $reportName, 'Identifier' ) ) {
            $identifier = $INI->variable( $reportName, 'Identifier' );
        } else {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Identifier not defined in owmonitoring.ini" );
        }
        if( $INI->hasVariable( $reportName, 'PrepareFrequency' ) ) {
            $prepareFrequency = $INI->variable( $reportName, 'PrepareFrequency' );
            switch( $prepareFrequency ) {
                case 'minute' :
                    $fromDate = new DateTime( date( 'Y-m-d H:i:00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+1 minute' );
                    break;
                case 'quarter_hour' :
                    $quarter = intval( $date->format( 'i' ) / 15 );
                    $fromDate = new DateTime( date( 'Y-m-d H:' . (15 * $quarter) . ':00' ) );
                    $toDate = clone($fromDate);
                    $toDate->modify( '+15 minute' );
                    break;
                case 'houly' :
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
                    break;
            }
            $lastReport = eZSiteData::fetchByName( 'report_' . $reportName );
            if( $lastReport ) {
                $lastReportDate = new DateTime( $lastReport->attribute( 'value' ) );
                if( $lastReportDate >= $fromDate && $lastReportDate < $toDate ) {
                    throw new OWMonitoringReportException( __METHOD__ . " : $reportName already exits" );
                }
            }
        }
        $report = self::makeReport( $reportName, TRUE );
        $report->store( );
        $siteData = new eZSiteData( array(
            'name' => 'report_' . $reportName,
            'value' => date( 'Y-m-d H:i:s' )
        ) );
        $siteData->store( );
        OWMonitoringLogger::logNotice( __METHOD__ . " : Report " . $report->getIdentifier( ) . " is stored in the database" );
    }

    static function makeReport( $reportName, $forceClock = NULL ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( $reportName, 'Identifier' ) ) {
            $identifier = $INI->variable( $reportName, 'Identifier' );
        } else {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Identifier not defined in owmonitoring.ini" );
        }
        if( $INI->hasVariable( $reportName, 'Tests' ) ) {
            $testList = $INI->variable( $reportName, 'Tests' );
        } else {
            throw new OWMonitoringReportException( __METHOD__ . " : [$reportName]Tests not defined in owmonitoring.ini" );
        }
        try {
            $report = new OWMonitoringReport( $identifier );
        } catch( Exception $e ) {
            throw new OWMonitoringReportException( __METHOD__ . " : Report instancation failed\n" . $e->getMessage( ) );
        }
        if( $forceClock === TRUE ) {
            $forceClock = time( );
        }
        foreach( $testList as $testIdentifier => $testFunction ) {
            list( $testClass, $testMethod ) = explode( '::', $testFunction );
            $testClass = $reportName . '_' . $testClass;
            $testMethod = 'test' . $testMethod;
            $testFunction = $testClass . '::' . $testMethod;
            if( !class_exists( $testClass ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : Class $testClass not found" );
                continue;
            } elseif( !is_callable( $testFunction ) ) {
                OWMonitoringLogger::logError( __METHOD__ . " : Can not call $testFunction method" );
                continue;
            } else {
                try {
                    $testValue = call_user_func( $testFunction );
                } catch (  Exception $e ) {
                    OWMonitoringLogger::logError( __METHOD__ . " : " . $e->getMessage( ) );
                    $testValue = FALSE;
                }
                $report->setData( $testIdentifier, $testValue, $forceClock );
            }
        }
        if( $report->countDatas( ) == 0 ) {
            throw new OWMonitoringReportException( __METHOD__ . " : Report is empty" );
        }
        return $report;
    }

    /* eZPersistentObject methods */

    public static function definition( ) {
        return array(
            'fields' => array(
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
            ),
            'keys' => array(
                'identifier',
                'date'
            ),
            'class_name' => 'OWMonitoringReport',
            'name' => 'owmonitoring_report',
            'function_attributes' => array( ),
            'set_functions' => array( )
        );
    }

    function store( $fieldFilters = NULL ) {
        $this->setAttribute( 'serialized_data', serialize( $this->reportData ) );
        if( $this->attribute( 'date' ) == NULL ) {
            $this->setAttribute( 'date', date( 'Y-m-d H:i:s' ) );
        }
        parent::store( $fieldFilters );
    }

    static function fetch( $identifier, $fromDate = NULL, $toDate = NULL ) {
        $rows = self::fetchList( $identifier, $fromDate, $toDate, 1 );
        if( isset( $rows[0] ) )
            return $rows[0];
        return null;
    }

    static function fetchList( $identifier = NULL, $fromDate = NULL, $toDate = NULL, $limit = NULL ) {
        $conds = array( );
        if( $identifier ) {
            $conds[] = "identifier LIKE '$identifier'";
        }
        if( $fromDate ) {
            $conds[] = "date >= '$fromDate'";
        }
        if( $toDate ) {
            $conds[] = "date < '$toDate'";
        }
        if( !empty( $conds ) ) {
            $conds = ' WHERE ' . implode( ' AND ', $conds );
        }
        return self::fetchObjectList( self::definition( ), null, null, array(
            'identifier' => 'asc',
            'date' => 'asc'
        ), $limit, true, false, null, null, $conds );
    }

    static function fetchCount( $identifier = NULL, $fromDate = NULL, $toDate = NULL ) {
        return count( self::fetchList( $identifier, $fromDate, $toDate ) );
    }

    static function removeOldReports( $reportLifetime ) {
        return self::removeObject( self::definition( ), array( 'date' => array(
                '>',
                date( 'Y-m-d H:i:s', strtotime( '+' . $reportLifetime . ' minute' ) )
            ) ) );
    }

}
