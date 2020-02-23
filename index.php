<?php

require_once './vendor/autoload.php';
use GeoIp2\Database\Reader;

$response = [
	'ip' => $ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
];

try{
	$reader = new Reader(__DIR__.'/data/GeoIP2-City.mmdb');
	$record = $reader->city($ipaddr);

	$geoip = [
		'country' => $record->country->isoCode, 
		'country_name' => $record->country->name, 
		'country_cn' => $record->country->names['zh-CN'], 
		'name_most_specific' => $record->mostSpecificSubdivision->name, 
		'city' => $record->city->name, 
		'postal' => $record->postal->code, 
		'location_latitude' => $record->location->latitude, 
		'location_longitude' => $record->location->longitude, 
		'traits' => $record->traits->network, 
	];
	$geoip && $response['geo'] = $geoip;
}catch(Exception $e){
}


/********************/
header('content-type: application/json;charset=utf8');
echo json_encode($response);
