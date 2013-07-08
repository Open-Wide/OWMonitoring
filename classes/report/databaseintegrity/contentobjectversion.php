<?php 

class DatabaseIntegrity_ContentObjectVersion {
    
    /* Count the number of dead contentobject_id in ezcontentobject_version table */
    static function testContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_version', 'contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }
    
}

?>