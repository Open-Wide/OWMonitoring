<?php

class eZInfo_Versions {

    static function testeZpublish() {
        return eZPublishSDK::version();
    }

    static function testExtension() {
        $extensionInfoString = '';
        foreach ( eZExtension::activeExtensions() as $extension ) {
            @$extensionInfo = eZExtension::extensionInfo( $extension );
            if ( $extensionInfo ) {
                $name = isset( $extensionInfo['name'] ) ? $extensionInfo['name'] : 'unknown name';
                $version = isset( $extensionInfo['version'] ) ? $extensionInfo['version'] : 'unknown version';
                $extensionInfoString .= '[' . $extension . '] ' . $name . ' = ' . $version . PHP_EOL;
            }
        }
        return $extensionInfoString;
    }

    static function testAll() {
        $InfoString = 'eZpublish Version = ' . self::testeZpublish() . PHP_EOL;
        $InfoString .= self::testExtension();
        return $InfoString;
    }

}
