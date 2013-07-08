<?php 

class DatabaseIntegrity_ContentObject {
    
    /* Count the number of dead contentclass_id in ezcontentobject table */
    static function testContentClass( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject', 'contentclass_id NOT IN (
                SELECT id
                FROM ezcontentclass
            )' );
    }
    
    /* Count the number of dead section_id in ezcontentobject table */
    static function testSection( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject', 'section_id NOT IN (
                SELECT id
                FROM ezsection
            )' );
    }
    
    /* Count the number of content object not related to a node (in trash or not) */
    static function testNode( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject', 'id NOT IN (
                SELECT contentobject_id
                FROM ezcontentobject_trash
            ) AND id NOT IN (
                SELECT contentobject_id
                FROM ezcontentobject_tree
            )' );
    }
    
    /* Count the number of content objects not present in the table ezcontent_name */
    static function testContentName( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject', 'id NOT IN (
                SELECT contentobject_id
                FROM ezcontentobject_name
            )' );
    }
    
}

