<?php

class Publicize_UI_Setting {

	/**
	 * サブメニューを追加
	 */
	public static function add_pbcz_submenu() {
		add_options_page('SNS連携設定', 'SNS連携設定', 'administrator', 'pbcz_setting', array(__CLASS__, 'pbcz_setting_sns') );
	}
	/**
	 * SNS 投稿設定
	 */
	public static function pbcz_setting_sns() {
		?>
		<form method="post" action="options-general.php?page=pbcz_setting">
			<?php
				wp_nonce_field('update-options');
				$keys = array(FB_SELECT_PAGE, FB_SELECT_PAGE_TOKEN, TW_TOKEN, 'post_message');
				if ( isset($_POST['action']) && $_POST['action'] === 'update') {
					foreach ($keys as $key) {
						$option = get_option($key);
						if( $option === false && isset($_POST[$key]) ){
							add_option($key, $_POST[$key]);
						}else if( isset($_POST[$key]) && $_POST[$key] !== $option ){
							update_option($key, $_POST[$key]);
						}
					}
				}
				if( get_option(FB_PAGE_LIST) != false && isset($_REQUEST['fb_delete']) && $_REQUEST['fb_delete'] == '9999' ){
					delete_option(FB_USER);
					delete_option(FB_PAGE_LIST);
					delete_option(FB_SELECT_PAGE);
					delete_option(FB_SELECT_PAGE_TOKEN);
				}
				if( get_option(TW_TOKEN) !== false && isset($_REQUEST['tw_delete']) && $_REQUEST['tw_delete'] == '9999' ){
					delete_option(TW_TOKEN);
					delete_option(TW_TOKEN_SECRET);
				}

				$fb_authorize = Facebook_Util::authorize();
			?>

			<section class="fb_auth">
				<h2>Facebookアカウント設定</h2>
				<?php if($fb_authorize === false): ?>
					<div class="fb_no_auth">
						<a href="<?php echo Facebook_Util::get_oauth_url(); ?>" class="button-secondary">Facebookと連携する</a>
					</div> 

				<?php elseif($fb_authorize === 'other'): $user = json_decode(get_option(FB_USER), true);?>
					<div class="fb_auth_complete">
						<span><?php echo $user['name']; ?></span>さんが認証しています。<span><?php echo $user['name']; ?></span>さんへお問い合わせください。
						<a href="options-general.php?page=pbcz_setting&fb_delete=9999" class="button-secondary" onclick="return confirm('アプリ連携を解除しても宜しいですか？');">連携解除</a>
					</div>

				<?php elseif($fb_authorize === 'self'):
					$page_list = Facebook_Util::get_pages();
					$current_page_id = get_option(FB_SELECT_PAGE);
					$token = get_option(FB_SELECT_PAGE_TOKEN);
					$html='';?>
					<div class="fb_auth_complete">
						<select id="<?php echo FB_SELECT_PAGE; ?>" name="<?php echo FB_SELECT_PAGE; ?>">
						<?php foreach($page_list['data'] as $key=>$page): $html .= '<input type="hidden" id="fb_'.$page['id'].'" value="'.$page['access_token'].'">'?>
							<option value="<?php echo $page['id'] ?>" <?php echo ( $current_page_id === $page['id']) ? 'selected' : '' ?>><?php echo $page['name'] ?></option>
						<?php endforeach; ?>
						</select>
						<?php echo $html; ?>
						<a href="options-general.php?page=pbcz_setting&fb_delete=9999" class="button-secondary" onclick="return confirm('アプリ連携を解除しても宜しいですか？');">連携解除</a>
						<input type="hidden" name="<?php echo FB_SELECT_PAGE_TOKEN; ?>" id="<?php echo FB_SELECT_PAGE_TOKEN; ?>" value="<?php echo ($token != false)? $token : $page_list['data'][0]['access_token'] ?>"/>
					</div>
				<?php endif; ?>
				
			</section>


			<section class="tw_auth">
				<h2>Twitterアカウント設定</h2>
				<?php if(Twitter_Util::authorize()):?>
				<div class="tw_auth_complete">
				<p>認証済み</p><a href="options-general.php?page=pbcz_setting&tw_delete=9999" class="button-secondary" onclick="return confirm('アプリ連携を解除しても宜しいですか？');">連携解除</a>
				</div>
				<?php else: ?>
				<div class="tw_no_auth">
					<a href="<?php echo Twitter_Util::get_oauth_url() ; ?>" class="button-secondary">Twitterと連携する</a>
				</div>
				<?php endif; ?>
			</section>

			<section class="post_message">
				<h2>投稿メッセージ</h2>
				<textarea name="post_message" style="margin: 2px; width: 388px; height: 136px;"><?php echo get_option('post_message','%%title%%が投稿されました。 %%link%%'); ?></textarea>
			</section>

			<p class="submit">
				<input type="hidden" name="action" value="update"/>
				<input type="submit" name="Submit" class="button-primary" value="変更を保存" />
			</p>
		</form>
		<script>
		jQuery('#<?php echo FB_SELECT_PAGE; ?>').change(function(){
			jQuery('#<?php echo FB_SELECT_PAGE_TOKEN; ?>').val(
				jQuery('#fb_'+jQuery(this).val()).val()
			);
		});
		</script>
		<?php
	}

	/**
	 * 設定のアップデート
	 */
	public static function update_setting(){
		$charset = get_bloginfo( 'charset' );
		header( "Content-Type: application/json; charset=$charset" );
		echo json_encode(array('status'=>'success', 'message'=>'all green.'));
		die();
	}

	/**
	 * Ajax 通信
	 */
	public static function add_pbcz_script() {
		wp_enqueue_script('publicize_js', plugins_url('publicize/assets') . '/publicize.js', false, '1.0');
	}
}