<?php

class DatabaseIntegrity_ContentObjectAttribute {

    /* Count the number of dead contentclassattribute_id in ezcontentobject_attribute table */
    static function testContentClassAttribute( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_attribute', 'contentclassattribute_id NOT IN (
                SELECT id
                FROM ezcontentclass_attribute
            )' );
    }

    /* Count the number of dead contentobject_id in ezcontentobject_attribute table */
    static function testContentObject( ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezcontentobject_attribute', 'contentobject_id NOT IN (
                SELECT id
                FROM ezcontentobject
            )' );
    }

}
?>