<?php 

class DatabaseIntegrity_ContentObjectTree {
    
    /* Count the number of dead contentobject_id in ezcontentobject_tree table */
    static function testContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_tree', 'contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }
    
    /* Count the number of dead parent_node_id in ezcontentobject_tree table */
    static function testParentNode( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_tree', 'parent_node_id NOT IN (
                SELECT node_id
                FROM ezcontentobject_trash
            ) AND parent_node_id NOT IN (
                SELECT node_id
                FROM ezcontentobject_tree
            )' );
    }
    
}

?>
