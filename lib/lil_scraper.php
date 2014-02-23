<?php

require('html2array.php');

if ( isset($argv) || isset($_POST['callback']) ) {

	$data_file = 'data.json';
	$json = json_decode(file_get_contents($data_file), true);

	$uri = null;

	if ( $_POST['uri'] ) {
		$uri = $_POST['uri'];

		$matches = scrape($uri);

		if ( $matches && $matches[1] ) {
			$arr = explode( ' ', $matches[1] );
			$data = array( 'app' => strtolower($arr[0]), 'uri' => $uri, 'ver' => $arr[1] );
			//$json = array_merge($json, $data);
			$json[$key] = $data;
		}

	} else {

	foreach ( $json as $key => $array ) {
		$matches = scrape($key);

		if ( $matches && $matches[1] ) {
			$arr = explode( ' ', $matches[1] );
			$data = array( 'app' => strtolower($arr[0]), 'uri' => $key, 'ver' => $arr[1] );
			//$json = array_merge($json, $data);
			$json[$key] = $data;
		}
	}

	}

	if ( $json ) {
		echo json_encode( $json );
		file_put_contents($data_file, json_encode($json));
	}

} else if ( isset($_POST['get_latest']) ) {

	$vers_file = 'latest.json';
	$latest = json_decode(file_get_contents($vers_file), true);

	foreach ( $latest as $key => $array ) {

		$data = scrape_latest($key, $array['uri']);
		$latest[$key] = array( 'app' => strtolower($key), 'uri' => $array['uri'], 'ver' => $data['ver'] );

	}

	echo json_encode($latest);
	file_put_contents($vers_file, json_encode($latest));

} else if ( isset($_POST['remove_uri']) ) {

	$data_file = 'data.json';
	$json = json_decode(file_get_contents($data_file), true);

	$uri = $_POST['uri'];
	unset($json[$uri]);

	echo json_encode($json);

	file_put_contents($data_file, json_encode($json));
}

//

function scrape( $uri ) {
	$curl = curl_init( $uri );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);

	$parser = new htmlParser($result);

	$dom = $parser->toArray();
	$info = $dom[0]['innerHTML'];
	preg_match( '/meta.?name=.?generator.? content=[\'"]*([^>]+)[\'"] \//', $info, $matches );

	return $matches;
}

function scrape_latest( $key, $uri ) {
	$curl = curl_init( $uri );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);

	$parser = new htmlParser($result);

	switch ( $key ) {

		case 'wordpress':
			$dom = $parser->toArray();
			$info = $dom[0]['innerHTML'];
			preg_match( '/WordPress.+Version ([\d\.]+)/', $info, $matches );
			if ( $matches && $matches[1] ) {
				$data = array( 'app' => strtolower($key), 'uri' => $uri, 'ver' => $matches[1] );
				return $data;
			}

		break;

		case 'ghost':
			$dom = $parser->toArray();
			$info = $dom[0]['innerHTML'];
			preg_match( '/data-version="([\d\.]+)"/', $info, $matches );
			if ( $matches && $matches[1] ) {
				$data = array( 'app' => strtolower($key), 'uri' => $uri, 'ver' => $matches[1] );
				return $data;
			}

		break;

	}

	//return $matches;
}

