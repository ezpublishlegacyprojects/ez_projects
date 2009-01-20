<?php

$dbHost = 'localhost';
$dbLogin = 'user';
$dbPassword = 'password';
$dbName = 'dbname';

if ( $_SERVER['argc'] < 3 )
{
    exit( 1 );
}

$repository = basename( $_SERVER['argv'][1] );
$revision = $_SERVER['argv'][2];

$link = mysql_connect( $dbHost, $dbLogin, $dbPassword );

mysql_select_db( $dbName );

mysql_query( "INSERT INTO ezpending_actions ( action, param )
                     VALUES ( 'import_svn_log', '" . mysql_real_escape_string( "$repository;$revision", $link ) . "')"
             , $link );

?>