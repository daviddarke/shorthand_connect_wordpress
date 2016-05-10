<?php

function get_stories() {
	global $serverURL;
	$token = get_option('sh_token_key');
	$user_id = get_option('sh_user_id');

	$valid_token = false;

	$stories = array();

	//Attempt to connect to the server
	if($token && $user_id) {
		$url = $serverURL.'/api/index/';
		$vars = 'user='.$user_id.'&token='.$token;
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$response = curl_exec( $ch );
		$data = json_decode($response);
		if(isset($data->stories)) {
			$valid_token = true;
			$stories = $data->stories;
		}
	}
	return $stories;
}

function get_story($story_id) {

	WP_Filesystem();
	$destination = wp_upload_dir();
	$destination_path = $destination['path'].'/shorthand/'.$story_id;

	global $serverURL;
	$token = get_option('sh_token_key');
	$user_id = get_option('sh_user_id');

	$valid_token = false;

	$story = array();

	//Attempt to connect to the server
	if($token && $user_id) {
		$url = $serverURL.'/api/story/'.$story_id.'/';
		$vars = 'user='.$user_id.'&token='.$token;
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$response = curl_exec( $ch );
		$zipfile = tempnam('/tmp', 'sh_zip');
		$handle = fopen($zipfile, "w");
		fwrite($handle, $response);
		fclose($handle);

		$unzipfile = unzip_file( $zipfile, $destination_path);
   
   		if ( $unzipfile ) {
   			$story['path'] = $destination_path;
   		}
	}
	return $story;
}
?>