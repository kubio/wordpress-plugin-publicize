<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

class Facebook_Util{
	private static $facebook;
	private static $user_id = 0;
	private static $params = array();

	public static function initialize($appId=null, $secret=null, $scope='read_stream, publish_stream, status_update, manage_pages'){
		if($appId === null || $secret === null){
			return null;
		}
		self::$facebook = new Facebook(array(
			'appId' => $appId,
			'secret' => $secret,
		));

		self::$user_id = self::$facebook->getUser();

		self::$params = array(
			'scope' => $scope,
			'redirect_uri' => 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]
		);

		return self::$facebook;
	}

	public static function authorize(){
		if(self::$facebook === null || self::$user_id === 0) return false;
		else return true;
	}

	public static function get_oauth_url(){
		return self::$facebook->getLoginUrl(self::$params);
	}

	public static function get_page_list(){

		// ユーザIDを取得する。今回は自分
		$user_profile = self::$facebook->api('/me');
		$uid = $user_profile["id"];

		// ユーザが管理するFBページの一覧を取得する
		//  $pagesの中にはユーザが管理権限を持つFacebookページの情報が入る
		$pages = self::$facebook->api("/$uid/accounts");
		return $pages;
	}

	public static function post($message, $link, $name, $description){

		$fb_id = get_option('fb_page_select');
		$fb_token = get_option('fb_selected_token');

		$result = self::$facebook->api("/$fb_id/feed", "post", array(
					"message"      => $message,
					"link"         => $link,
					"name"         => $name,
					"description"  => $description,
					"access_token" => $fb_token
				));
	}
}








