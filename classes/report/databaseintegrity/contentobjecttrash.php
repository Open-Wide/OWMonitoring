<?php 

class DatabaseIntegrity_ContentObjectTrash {
    
    /* Count the number of dead contentobject_id in ezcontentobject_trash table */
    static function testContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_trash', 'contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }
    
    /* Count the number of dead parent_node_id in ezcontentobject_trash table */
    static function testParentNode( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_trash', 'parent_node_id NOT IN (
                SELECT node_id
                FROM ezcontentobject_trash
            ) AND parent_node_id NOT IN (
                SELECT node_id
                FROM ezcontentobject_tree
            )' );
    }
    
}

?>