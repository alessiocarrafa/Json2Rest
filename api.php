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
			$string = file_get_contents( OBJ_DIR . $entry );
			$decoded = json_decode( $string, true );
			$readed[ $decoded['name'] ]	= $decoded;
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
	
	$app->get( "/" . $obj_file['name'] . "/:id", function( $id ) use ( $app, $obj_file )
	{
		$key_name = GetPrimaryKey( $obj_file['data'] );
		echo( json_encode( [ "resource" => "Search for PRIMARY KEY /" . $obj_file['name'] . "/$id with keyname of $key_name" ] ) );
	});
	
	$app->get( "/" . $obj_file['name_plural'], function() use ( $app, $obj_file )
	{
		echo( json_encode( [ "resource" => "/" . $obj_file['name_plural'] ] ) );
	});
	
	foreach( $obj_file['data'] as $data_key => &$data_value )
	{
		$app->get( "/" . $obj_file['name'] . "/" . $data_key . "/:value", function( $value ) use ( $app, $data_key, $data_value )
		{
			$type			= $data_value['type'];
			$casted_value	= NULL;

				 if( $type == "int"		)	$casted_value = intval( $value );
			else if( $type == "string"	)	$casted_value = (string)( $value );
			else							$casted_value = $value;
			
			$external = $data_value['ext'];

			$str = "Search for $data_key with value of $casted_value typed like [ $type ]";
			
			if( $external ) $str .= ", with external referer in " . $external['ext_resource'];

			echo( json_encode( [ "resource" => $str ] ) );
			
			if( $external ) $app->response->redirect( "/" . $external['ext_resource'] . "/" . $casted_value );
		});
	}
	
}

$app->run();

function GetPrimaryKey( $data )
{
	foreach( $data as $key => &$val )
	{
		if( $val['key'] ) return $key;
	}
	return NULL;
}