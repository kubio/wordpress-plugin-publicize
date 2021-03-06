<?php

require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../vendor/abraham/twitteroauth/twitteroauth/twitteroauth.php';

class Twitter_Util{
	private static $connect;
	private static $temporary_credentials;

	public static function initialize($ckey, $csecret){
		//既にアクセストークンを保持しているか判定
		if(get_option(TW_TOKEN) != false){
			self::$connect = new TwitterOAuth($ckey, $csecret, get_option(TW_TOKEN), get_option(TW_TOKEN_SECRET));
			self::$connect->host = 'https://api.twitter.com/1.1/';
			return ;
		}else{
			self::$connect = new TwitterOAuth($ckey, $csecret);
			self::$connect->host = 'https://api.twitter.com/1.1/';
		}

		if(!(isset($_GET['page']) && $_GET['page'] === 'pbcz_setting')){
			return ;
		}

		//コールバックか新規か判定
		if(isset($_REQUEST['callback']) && $_REQUEST['callback'] === 'true' && isset($_SESSION['pbcz_oauth_token']) && isset($_SESSION['pbcz_oauth_token_secret'])){
			self::$temporary_credentials = array(
				'oauth_token'	=> $_SESSION['pbcz_oauth_token'],
				'oauth_token_secret'	=>$_SESSION['pbcz_oauth_token_secret']);
		}else{
			self::$temporary_credentials = self::$connect->getRequestToken('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"].'&callback=true');
			if(isset(self::$temporary_credentials['oauth_token']) && isset(self::$temporary_credentials['oauth_token_secret'])){
				$_SESSION['pbcz_oauth_token'] = self::$temporary_credentials['oauth_token'];
				$_SESSION['pbcz_oauth_token_secret'] = self::$temporary_credentials['oauth_token_secret'];
			}
		}

		//アクセストークンの取得
		if( isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']) ){
			self::$connect = new TwitterOAuth($ckey, $csecret, $_SESSION['pbcz_oauth_token'], $_SESSION['pbcz_oauth_token_secret']);
			$token_credentials = self::$connect->getAccessToken($_REQUEST['oauth_verifier']);
			if(isset($token_credentials['oauth_token']) && isset($token_credentials['oauth_token_secret'])){
				self::$connect = new TwitterOAuth($ckey, $csecret, $token_credentials['oauth_token'], $token_credentials['oauth_token_secret']);
				if(get_option(TW_TOKEN) != false){
					update_option(TW_TOKEN, $token_credentials['oauth_token']);
					update_option(TW_TOKEN_SECRET, $token_credentials['oauth_token_secret']);
				}else{
					add_option(TW_TOKEN, $token_credentials['oauth_token']);
					add_option(TW_TOKEN_SECRET, $token_credentials['oauth_token_secret']);
				}
			}
		}
	}

	public static function authorize(){
		$token = get_option(TW_TOKEN);
		if(self::$connect === null || $token == false) return false;
		else return true;
	}

	public static function get_oauth_url(){
		$redirect_url = self::$connect->getAuthorizeURL(self::$temporary_credentials);
		return $redirect_url;
	}

	public static function post($message){
		if($message){
			return self::$connect->post('statuses/update', array('status' =>$message));
		}else{
			return false;
		}
	}
}
