<?php

class OWMonitoringReport {

    protected $identifier;
    protected $reportData;

    public function __construct( $identifier ) {
        if( empty( $identifier ) ) {
            throw new Exception( __METHOD__ . " : Report identifier must be set" );
        }
        $this->identifier = $identifier;
        $this->reportData = array( );
    }

    public function getIdentifier( ) {
        return $this->identifier;
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
        return $tool->sendReport( $this );
    }

    static function makeReport( $reportName ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( $reportName, 'Identifier' ) ) {
            $identifier = $INI->variable( $reportName, 'Identifier' );
        } else {
            throw new Exception( __METHOD__ . " : [$reportName]Identifier not defined in owmonitoring.ini" );
        }
        if( $INI->hasVariable( $reportName, 'Tests' ) ) {
            $testList = $INI->variable( $reportName, 'Tests' );
        } else {
            throw new Exception( __METHOD__ . " : [$reportName]Tests not defined in owmonitoring.ini" );
        }
        try {
            $report = new OWMonitoringReport( $identifier );
        } catch( Exception $e ) {
            throw new Exception( __METHOD__ . " : Report instancation failed\n" . $e->getMessage( ) );
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
                $report->setData( $testIdentifier, $testValue );
            }
        }
        if( $report->countDatas( ) == 0 ) {
            throw new Exception( __METHOD__ . " : Report is empty" );
        }
        return $report;
    }

}
