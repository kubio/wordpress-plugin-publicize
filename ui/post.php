<?php

class Publicize_UI_Post {

	/**
	 * 投稿選択メタボックスを追加
	 */
	public static function add_pbcz_meta_box() {
		add_meta_box('pbcz_meta_box', 'SNS投稿設定', array(__CLASS__, 'create_pbcz_meta_box'), 'post', 'side', 'high' );
	}

	/**
	 * メタボックス内のコンテンツを出力
	 */
	public static function create_pbcz_meta_box() {
		global $post;
		$fb_page_id = get_option(FB_SELECT_PAGE);
		$fb_token = get_option(FB_SELECT_PAGE_TOKEN);
		$tw_access_token = get_option(TW_TOKEN);
		if( $fb_page_id !== false && $fb_token !== false):?>
		<div class="post_facebook"><input type="checkbox" name="select_facebook" value="fb_ok" checked="true" /><label for="select_facebook">Facebookへ投稿する</label></div>
		<?php endif;
		if( $tw_access_token !== false && $tw_access_token !== '' ):?>
		<div class="post_twitter"><input type="checkbox" name="select_twitter" value="tw_ok" checked="true" /><label for="select_facebook">Twitterへ投稿する</label></div>
		<?php endif;
		if( $fb_page_id === false && $fb_token === false && $tw_access_token === false ): ?>
		<a href="<?php echo admin_url('options-general.php?page=pbcz_setting'); ?>">投稿するSNSアカウントを設定する</a>
		<?php else:
			$key = 'post_message';
			$message = get_post_meta($post->ID, $key, true);
			if(!$message){
				$message = get_option($key, '%%title%% が投稿されました。'.PHP_EOL.'%%link%%');
			}
			echo '<textarea name="post_message" style="margin: 2px; width: 249px; height: 108px;">'.$message.'</textarea>';
		endif;
	}

	/**
	 * 記事保存時の処理
	 */
	public static function pbcz_save_post( $post_id ){
		if(!isset($_POST['post_message'])) return false;
		$keyArray = array(
			'select_facebook'	=> $_POST['select_facebook'],
			'select_twitter'=> $_POST['select_twitter'],
			'post_message'=> $_POST['post_message']
		);

		foreach ($keyArray as $key => $value) {
			if (get_post_meta($post_id, $key, true) == "") {
				add_post_meta($post_id, $key, $value, true);
			}
			elseif($value != get_post_meta($post_id, $key, true)) {
				update_post_meta($post_id, $key, $value);
			}
			elseif($value=="") {
				delete_post_meta($post_id, $key, get_post_meta($post_id, $key, true));
			}
		}
	}

	/**
	 * 記事公開時の処理（SNS投稿）
	 */
	public static function pbcz_published_post($new_status, $old_status, $post){
		$isPublish = false;
		if($new_status === 'publish'){
			error_log($old_status,0);
			switch($old_status){
				case 'draft':
				case 'auto-draft':
				case 'future':
				case 'pending':
					$isPublish = true;
					break;
				default:
					return;
			}
		}

		if($isPublish) {
				if( isset($_POST['post_message']) && isset($_POST['select_twitter']) && isset($_POST['select_facebook'])){
			    $message = $_POST['post_message'];
					$isFacebook = $_POST['select_facebook'];
					$isTwitter = $_POST['select_twitter'];
				}else{
					$message = get_post_meta($post->ID, 'post_message', true);
					$isFacebook = get_post_meta($post->ID, 'select_facebook', true);
					$isTwitter = get_post_meta($post->ID, 'select_twitter', true);
				}
				if(!$message){
					$message = get_option('post_message', '%%title%% が投稿されました。'.PHP_EOL.'%%link%%');
				}

				$link = get_permalink($post->ID);
				$name = $post->post_title;
				$description = $post->post_excerpt;
				$message = preg_replace(array('/%%title%%/', '/%%link%%/') , array($post->post_title, $link), $message);

				if($isFacebook){
					Facebook_Util::post($message, $link, $name, $description);
				}
				if($isTwitter){
					Twitter_Util::post($message);
				}
		}
	}
}
