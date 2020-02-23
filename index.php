<?php

require_once './vendor/autoload.php';
use GeoIp2\Database\Reader;

$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
try{
	$uri = $_SERVER['REQUEST_URI'];
	if(preg_match('#^\/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$#i', $uri, $match) ){
		$ipaddr = $match[1];
	}elseif($uri!='/'){
		throw new Exception('INVALID IPv4', 1000);
	}

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

	$response['ip'] = $ipaddr;
	$geoip && $response['geo'] = $geoip;
}catch(Exception $e){
	$response =  [
		'code' => $e->getCode(),
		'msg' => $e->getMessage(),
	];
}

/********************/
header('content-type: application/json;charset=utf8');
echo json_encode($response).PHP_EOL;
