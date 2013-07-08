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
            return $this->reportData[$name];
        }
        return NULL;
    }

    public function setData( $name, $data ) {
        if( !is_string( $name ) ) {
            return FALSE;
        }
        $this->reportData[$name] = $data;
        return TRUE;
    }

    public function appendToData( $name, $data ) {
        if( $this->hasData( $name ) ) {
            $currentData = $this->getData( $name );
            if( !is_array( $currentData ) ) {
                $currentData = array( $currentData );
            }
            $currentData[] = $data;
            $this->setData( $name, $currentData );
        } else {
            $this->setData( $name, $data );
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

    public function setDatas( $dataArray ) {
        foreach( $dataArray as $name => $data ) {
            $this->setData( $name, $data );
        }
    }

    public function sendReport( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( !$INI->hasVariable( 'OWMonitoring', 'MonitoringToolClass' ) ) {
            OWMonitoringLogger::writeError( __METHOD__ . " : [OWMonitoring]MonitoringToolClass not defined in owmonitoring.ini" );
            return FALSE;
        }
        $monitoringToolClass = $INI->variable( 'OWMonitoring', 'MonitoringToolClass' );
        if( !class_exists( $monitoringToolClass ) ) {
            OWMonitoringLogger::writeError( __METHOD__ . " : Class $monitoringToolClass not found" );
            return FALSE;
        }
        $tool = $monitoringToolClass::instance( );
        $tool->sendReport( $this );
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
                OWMonitoringLogger::writeError( __METHOD__ . " : Class $testClass not found" );
                continue;
            } elseif( !is_callable( $testFunction ) ) {
                OWMonitoringLogger::writeError( __METHOD__ . " : Can not call $testFunction method" );
                continue;
            } else {
                try {
                    $testValue = call_user_func( $testFunction );
                } catch (  Exception $e ) {
                    OWMonitoringLogger::writeError( __METHOD__ . " : " . $e->getMessage( ) );
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
