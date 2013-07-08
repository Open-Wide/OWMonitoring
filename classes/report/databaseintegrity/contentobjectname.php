<?php 

class DatabaseIntegrity_ContentObjectName {
    
    /* Count the number of content objects not related to a content objet name */
    static function testContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject', 'id NOT IN (
                SELECT contentobject_id
                FROM ezcontentobject_name
            )' );
    }
    
}

?>