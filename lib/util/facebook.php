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

		if(isset($_REQUEST['code']) && self::$user_id !== 0){
			$pages = json_encode( self::get_page_list() );
			if(get_option(FB_PAGE_LIST) != false){
				update_option(FB_PAGE_LIST, $pages);
			}else{
				add_option(FB_PAGE_LIST, $pages);
			}

			$user = json_encode(self::$facebook->api('/me'));
			if(get_option(FB_USER) !== ''){
				update_option(FB_USER, $user);
			}else{
				add_option(FB_USER, $user);
			}
		}

		return self::$facebook;
	}

	public static function authorize(){
		$pages = self::get_pages();
		//まだ誰も認証していない？
		//　→ 認証ボタンを表示
		if( (self::$facebook === null || self::$user_id === 0) && sizeof($pages) === 0 )
			return false;
		//誰かが認証しているが、WPにログインしているユーザーは、その認証者本人ではない
		//　→ 「認証済 アプリ認証者名」を表示
		if( (self::$facebook === null || self::$user_id === 0) && sizeof($pages) > 0 )
			return 'other';
		//誰かが認証していて、WPにログインしているユーザーも、その認証者本人である
		//　→ リストを表示
		if( self::$facebook !== null && self::$user_id !== 0 && sizeof($pages) > 0 )
			return 'self';

		return false;
	}

	public static function get_oauth_url(){
		return self::$facebook->getLoginUrl(self::$params);
	}

	private static function get_page_list(){
		// ユーザが管理するFBページの一覧を取得する
		//  $pagesの中にはユーザが管理権限を持つFacebookページの情報が入る
		$user_id = self::$user_id;
		$pages = self::$facebook->api("/{$user_id}/accounts");
		return $pages;
	}

	public static function get_pages(){
		return json_decode(get_option(FB_PAGE_LIST),true);
	}

	public static function post($message, $link, $name, $description){

		$fb_id = get_option(FB_SELECT_PAGE);
		$fb_token = get_option(FB_SELECT_PAGE_TOKEN);
		if($message && $fb_id && $fb_token){
				$result = self::$facebook->api("/$fb_id/feed", "post", array(
							"message"      => $message,
							"link"         => $link,
							"name"         => $name,
							"description"  => $description,
							"access_token" => $fb_token
						));
		}
	}
}
