<?php

class DatabaseIntegrity_ContentClass {

    /* Count the number of content classes not related to a content class name */
    static function testContentClassName( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass', 'id NOT IN (
                SELECT contentclass_id
                FROM ezcontentclass_name
            )' );
    }

    /* Count the number of classes not related to a class group */
    static function testContentClassGroup( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentclass', 'id NOT IN (
                SELECT contentclass_id
                FROM ezcontentclass_classgroup
            )' );
    }

}
