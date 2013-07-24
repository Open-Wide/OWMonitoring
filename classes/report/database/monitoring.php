<?php

class Database_Monitoring {

    static function testNotInnodb( ) {
        $db = eZDB::instance( );
        $type = $db->databaseName( );
        switch( $type ) {
            case 'mysql' :
                $ini = eZINI::instance( );
                $dbname = $ini->variable( 'DatabaseSettings', 'Database' );
                $warningCount = 0;
                foreach( $db->arrayquery( "SELECT table_name, table_collation, engine FROM information_schema.tables WHERE table_schema = '" . $db->escapeString( $dbname ) . "'" ) as $row ) {
                    if( $row['engine'] != 'InnoDB' ) {
                        $warningCount++;
                    }
                }
                return $warningCount;
            default :
                throw new OWMonitoringReportNoValueException( );
        }
    }

    static function testNotUTF8( ) {
        $db = eZDB::instance( );
        $type = $db->databaseName( );
        switch( $type ) {
            case 'mysql' :
                $ini = eZINI::instance( );
                $dbname = $ini->variable( 'DatabaseSettings', 'Database' );
                $warningCount = 0;
                foreach( $db->arrayquery( "SELECT table_name, table_collation, engine FROM information_schema.tables WHERE table_schema = '" . $db->escapeString( $dbname ) . "'" ) as $row ) {
                    if( substr( $row['table_collation'], 0, 5 ) != 'utf8_' ) {
                        $warningCount++;
                    }
                }
                return $warningCount;
            default :
                throw new OWMonitoringReportNoValueException( );
        }
    }

    static function testEntryCount( ) {
        $INI = eZINI::instance( 'owmonitoring.ini' );
        if( $INI->hasVariable( 'Database', 'EntryCountTables' ) ) {
            $tableCount = array( );
            foreach( $INI->variable( 'Database', 'EntryCountTables' ) as $table ) {
                $tableCount[$table] = OWMonitoringReportTools::executeCountQuery( $table );
            }
            return $tableCount;
        }
        throw new OWMonitoringReportNoValueException( );
    }

}
