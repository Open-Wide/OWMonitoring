<?php

class eZInfo_Versions {
    static function testeZpublish( ) {
        return eZPublishSDK::version( );
    }

    static function testExtension( ) {
        $extensionInfoString = '';
        foreach( eZExtension::activeExtensions() as $extension ) {
            $extensionInfo = eZExtension::extensionInfo( $extension );
            $name = $extensionInfo['name'] ? $extensionInfo['name'] : 'unknown name';
            $version = $extensionInfo['version'] ? $extensionInfo['version'] : 'unknown version';
            $extensionInfoString .= '[' . $extension . '] ' . $name . ' = ' . $version . PHP_EOL;
        }
        return $extensionInfoString;
    }

    static function testAll( ) {
        $InfoString = 'eZpublish Version = ' . self::testeZpublish( ) . PHP_EOL;
        $InfoString .= self::testExtension( );
        return $InfoString;
    }
    
    static function testTest( ) {
        return $InfoString;
    }

}
