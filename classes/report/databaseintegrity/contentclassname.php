<?php

class DatabaseIntegrity_ContentClassName {
    
    /* Count the number of dead contentclass_id in ezcontentclass_name table */
    static function testContentClass( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass_name', 'contentclass_id NOT IN (
                SELECT id
                FROM ezcontentclass
            )' );
    }

}
