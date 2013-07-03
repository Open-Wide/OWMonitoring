<?php

class OWMonitoringLogger {
    const NOTICELOG = 'notice';
    const ERRORLOG = 'error';
    const WARNINGLOG = 'warning';

    const ERRORLOG_FILE = 'owmonitoring-error.log';
    const WARNINGLOG_FILE = 'owmonitoring-warning.log';
    const NOTICELOG_FILE = 'owmonitoring-notice.log';

    protected static $cli;
    protected $messages = array( );

    static function instance( ) {
        if( !isset( $GLOBALS['OWMonitoringLoggerInstance'] ) || !($GLOBALS['OWMonitoringLoggerInstance'] instanceof OWMonitoringLogger) ) {
            $GLOBALS['OWMonitoringLoggerInstance'] = new OWMonitoringLogger( );
        }

        return $GLOBALS['OWMonitoringLoggerInstance'];
    }

    public static function logMessage( $msg, $bPrintMsg = true, $logType = self::NOTICELOG ) {
        switch( $logType ) {
            case self::ERRORLOG :
                $logFile = self::ERRORLOG_FILE;
                if( $bPrintMsg )
                    self::writeError( $msg );
                break;

            case self::WARNINGLOG :
                $logFile = self::WARNINGLOG_FILE;
                if( $bPrintMsg )
                    self::writeWarning( $msg );
                break;

            case self::NOTICELOG :
            default :
                $logFile = self::NOTICELOG_FILE;
                if( $bPrintMsg )
                    self::writeNotice( $msg );
                break;
        }
        $logger = self::instance( );
        $logger->messages[] = array(
            'level' => $logType,
            'message' => $msg
        );
        eZLog::write( $msg, $logFile );
    }

    public static function logNotice( $msg, $bPrintMsg = true ) {
        self::logMessage( $msg, $bPrintMsg, self::NOTICELOG );
    }

    public static function logWarning( $msg, $bPrintMsg = true ) {
        self::logMessage( $msg, $bPrintMsg, self::WARNINGLOG );
    }

    public static function logError( $msg, $bPrintMsg = true ) {
        self::logMessage( $msg, $bPrintMsg, self::ERRORLOG );
    }

    public static function writeMessage( $msg, $logType = self::NOTICELOG ) {
        self::$cli = eZCLI::instance( );
        $isWebOutput = self::$cli->isWebOutput( );
        switch( $logType ) {
            case self::ERRORLOG :
                if( !$isWebOutput )
                    self::$cli->error( $msg );
                else
                    eZDebug::writeError( $msg, 'OWMonitoring' );
                break;

            case self::WARNINGLOG :
                if( !$isWebOutput )
                    self::$cli->warning( $msg );
                else
                    eZDebug::writeWarning( $msg, 'OWMonitoring' );
                break;

            case self::NOTICELOG :
            default :
                if( !$isWebOutput )
                    self::$cli->notice( $msg );
                else
                    eZDebug::writeNotice( $msg, 'OWMonitoring' );
                break;
        }
    }

    public static function writeError( $msg ) {
        self::writeMessage( $msg, self::ERRORLOG );
    }

    public static function writeWarning( $msg ) {
        self::writeMessage( $msg, self::WARNINGLOG );
    }

    public static function writeNotice( $msg ) {
        self::writeMessage( $msg, self::NOTICELOG );
    }

    public static function serializeMessages( ) {
        $logger = self::instance( );
        return serialize( $logger->messages );
    }

}
