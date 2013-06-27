<?php

class OWMonitoringReport {

    protected $identifier;
    protected $reportData;

    public function __contruct( $identifier ) {
        $this->identifier = $identifier;
        $this->reportData = array( );
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

    public function setDatas( $dataArray ) {
        foreach( $dataArray as $name => $data ) {
            $this->setData( $name, $data );
        }
    }

}
