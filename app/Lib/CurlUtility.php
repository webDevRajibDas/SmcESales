<?php
class CurlUtility {
	public static function get($url, $username = null, $password = null) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($username && $password) {
			curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
		}

		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}

	public static function post($url, $data, $username = null, $password = null) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		if ($username && $password) {
			curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
		}

		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}
	public function xmltobj($data){
		return json_decode(json_encode(simplexml_load_string($data)));
	}
}

?>