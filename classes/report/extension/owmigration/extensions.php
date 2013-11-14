<?php

class OWMigration_Extensions {

    /* Export for each extension the current installed version and the lastest version */
    static function testVersions( ) {
        $result = array( );
        if( is_callable( 'OWMigration::extensionList' ) ) {
            $extensionList = OWMigration::extensionList( );
            if( $extensionList ) {
                foreach( $extensionList as $extension ) {
                    $result[$extension['name']] = array(
                        'current_version' => $extension['current_version'],
                        'latest_version' => $extension['latest_version']
                    );
                }
                return $result;
            }
        }
        throw new OWMonitoringReportNoValueException( );
    }

}
