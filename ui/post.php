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
		$fb_page_id = get_option('fb_page_select');
		$fb_token = get_option('fb_selected_token');
		$tw_access_token = get_option('tw_access_token');
		if( $fb_page_id !== false && $fb_token !== false):?>
		<div class="post_facebook"><input type="checkbox" name="select_facebook" value="fb_ok" checked="true" /><label for="select_facebook">Facebookへ投稿する</label></div>
		<?php endif;
		if( $tw_access_token !== false && $tw_access_token !== '' ):?>
		<div class="post_twitter"><input type="checkbox" name="select_twitter" value="tw_ok" checked="true" /><label for="select_facebook">Twitterへ投稿する</label></div>
		<?php endif;
		if( $fb_page_id === false && $fb_token === false && $tw_id === false && $tw_token === false ): ?>
		<a href="<?php echo admin_url('options-general.php?page=pbcz_setting'); ?>">投稿するSNSアカウントを設定する</a>
		<?php else:
			$message = get_option('post_message', '');
			echo '<textarea name="post_message" style="margin: 2px; width: 249px; height: 108px;">'.$message.'</textarea>';
		endif;
	}

	public static function pbcz_published_post($post_id, $post){
		if(
			(($_POST["original_post_status"] == 'draft' || $_POST["original_post_status"] == 'auto-draft' )
			&& $_POST["hidden_post_status"] == 'draft'
			&& $_POST["originalaction"] == "editpost")
			|| (!isset($_POST["original_post_status"])
			&&  !isset($_POST["hidden_post_status"])
			&&  !isset($_POST["originalaction"])
			&& $post->post_status == "publish")
		)
		{
			$message = $_POST['post_message'];
			$link = get_permalink($post_id);
			$name = $post->post_title;
			$description = $post->post_excerpt;
			$message = preg_replace(array('/%%title%%/', '/%%link%%/') , array($post->post_title, $link), $message);

			if(isset($_POST['select_facebook'])){
				Facebook_Util::post($message, $link, $name, $description);
			}
			if(isset($_POST['select_twitter'])){
				Twitter_Util::post($message);
			}
		}
	}
}