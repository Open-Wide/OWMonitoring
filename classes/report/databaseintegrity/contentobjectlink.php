<?php

class DatabaseIntegrity_ContentObjectLink {

    /* Count the number of dead contentclassattribute_id in ezcontentobject_link table */
    static function testContentClassAttribute( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_link', 'contentclassattribute_id NOT IN (
                SELECT id
                FROM ezcontentclass_attribute
            ) AND id != 0' );
    }

    /* Count the number of dead from_contentobject_id in ezcontentobject_link table */
    static function testFromContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_link', 'from_contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }

    /* Count the number of dead to_contentobject_id in ezcontentobject_link table */
    static function testToContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_link', 'to_contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }

}
?>