<?php

require 'autoload.php';

$cli = eZCLI::instance( );
$script = eZScript::instance( array(
    'description' => ("eZ Publish Migration Handler\n" . "Permet le déploiement des modifications à effextuer au n iveau de la base de données\n" . "\n" . ".extension/OWMigration/bin/php/migrate.php --migration-class=MigrationClass"),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true
) );

$script->startup( );
$sys = eZSys::instance( );
$script->initialize( );

$report = new OWMonitoringReport( );
$report->setData( 'import.company.create', intval( rand( 1, 60 ) ) );
$report->appendToData( 'import.company.create', intval( rand( 1, 60 ) ) );
$report->setData( 'import.company.time_import', intval( rand( 20, 40 ) ) );
$report->setData( 'import.company.update', intval( rand( 60,100 ) ) );
$report->sendReport( $report );

$script->shutdown( 0 );
?>