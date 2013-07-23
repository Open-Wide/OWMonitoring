<?php

abstract class eZLogReport_BaseAnalyser {

    protected $file;
    protected $line;
    protected $reportedList = array( );
    protected $ignoredList = array( );
    protected $report = array( );

    protected function analyzeLogfile( $file ) {
        if( $file == NULL ) {
            OWMonitoringLogger::writeError( __METHOD__ . " : Logfile path not defined" );
            return FALSE;
        }
        if( !is_readable( $file ) ) {
            OWMonitoringLogger::writeError( __METHOD__ . " : $file not readable" );
            return FALSE;
        }
        $this->file = $file;
        $this->parseFile( );
        return $this->report;
    }

    protected function parseFile( ) {
        $handle = fopen( $this->file, 'rb' );
        if( $handle ) {
            while( !feof( $handle ) ) {
                $buffer = fgets( $handle );
                if( preg_match( '/^\[ [a-zA-Z]{3} [0-9]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} \]/', $buffer ) > 0 ) {
                    $this->parseLine( );
                    $this->line = '';
                }
                $this->line .= $buffer;
            }
        }
    }

    protected function parseLine( ) {
        $this->line = preg_replace( '/\n/', ' ', $this->line );
        $this->line = preg_replace( '/[ ]+/', ' ', $this->line );
        foreach( $this->ignoredList as $ignored ) {
            if( preg_match( $ignored, $this->line ) > 0 ) {
                return;
            }
        }
        foreach( $this->reportedList as $reportedKey => $reported ) {
            if( preg_match( $reported, $this->line ) > 0 ) {
                $this->reporte( $reportedKey );
                continue;
            }
        }
        $this->reporte( 'other' );
    }

    protected function reporte( $reportCode ) {
        if( isset( $this->report[$reportCode] ) ) {
            $this->report[$reportCode]++;
        } else {
            $this->report[$reportCode] = 1;
        }
    }

}
