<?php

class eZCheckLink_URL {

    static function testCountValid( ) {
        return self::executeTestQuery( TRUE );
    }

    static function testCountNotValid( ) {
        return self::executeTestQuery( FALSE );
    }

    static function executeTestQuery( $validity ) {
        return OWMonitoringReportTools::executeCountQuery( 'ezurl u , ezurl_object_link uol, ezcontentobject_attribute coa, ezcontentobject_tree cot', 'uol.url_id = u.id
        AND coa.id = uol.contentobject_attribute_id AND coa.version = uol.contentobject_attribute_version
        AND cot.contentobject_id = coa.contentobject_id AND cot.contentobject_version = coa.version
        AND u.is_valid = ' . intval( $validity ) );
    }

}
