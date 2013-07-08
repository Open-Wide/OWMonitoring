<?php

class DatabaseIntegrity_ContentClassAttribute {
    
    /* Count the number of dead contentclass_id in ezcontentclass_attribute table */
    static function testContentClass( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass_attribute', 'contentclass_id NOT IN (
                SELECT id
                FROM ezcontentclass
            )' );
    }

}
