<?php

require_once __DIR__	. '/vendor/autoload.php';

define( "OBJ_DIR", "obj/" );

$app = new \Slim\Slim();

$readed = array();

if( FALSE !== ( $handle = opendir( OBJ_DIR ) ) )
{
	while( FALSE !== ( $entry = readdir( $handle ) ) )
	{
		if( $entry != "." && $entry != ".." && strtolower( substr( $entry, strrpos( $entry, '.' ) + 1) ) == 'json' )
		{
			//echo "$entry\n";
			$string				= file_get_contents( OBJ_DIR . $entry );
			$readed[ $entry ]	= json_decode( $string, true );
		}
	}
	
	closedir($handle);
}

$app->get( "/", function() use ( $app )
{
	echo( json_encode( [ "resource" => "GOGOGOGO" ] ) );
});

foreach( $readed as $key => &$obj_file )
{
	
	$app->get( "/" . $obj_file['name'], function() use ( $app, $obj_file )
	{
		echo( json_encode( [ "resource" => "/" . $obj_file['name'] ] ) );
	});
	
	$app->get( "/" . $obj_file['name_plural'], function() use ( $app, $obj_file )
	{
		echo( json_encode( [ "resource" => "/" . $obj_file['name_plural'] ] ) );
	});
}

$app->run();