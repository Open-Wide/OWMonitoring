<?php

class DatabaseIntegrity_ContentClassClassGroup {
    
    /* Count the number of dead contentclass_id in ezcontentclass_classgroup table */
    static function testContentClass( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass_classgroup', 'contentclass_id NOT IN (
                SELECT id
                FROM ezcontentclass
            )' );
    }
    
    /* Count the number of dead group_id in ezcontentclass_classgroup table */
    static function testContentClassGroup( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass_classgroup', 'group_id NOT IN (
                SELECT id
                FROM ezcontentclassgroup
            )' );
    }

}
