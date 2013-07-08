<?php

class DatabaseIntegrityReport {

    protected $INI;
    protected $checkRelationIntegrity = array( );

    function __construct( ) {
        $this->INI = eZINI::instance( 'owmonitoring.ini' );
        if( $this->INI->hasVariable( __CLASS__, 'Tests' ) ) {
            $this->testList = $this->INI->variable( __CLASS__, 'Tests' );
        }
    }

    

}
