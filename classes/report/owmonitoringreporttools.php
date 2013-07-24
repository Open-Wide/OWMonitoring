<?php

class OWMonitoringReportTools {

    static function executeCountQuery( $tableName, $whereQueryPart = FALSE ) {
        $db = eZDB::instance( );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $query = "SELECT count( * ) as row_count FROM " . $tableName;
        if( $whereQueryPart ) {
            $query .= " WHERE " . $whereQueryPart;
        }
        try {
            $rows = $db->arrayQuery( $query );
        } catch( Exception $e ) {
            throw new OWMonitoringReportNoValueException( );
        }
        return isset( $rows[0] ) && isset( $rows[0]['row_count'] ) ? $rows[0]['row_count'] : FALSE;
    }

}
