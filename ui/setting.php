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
				$keys = array('fb_page_select', 'fb_selected_token', 'tw_select', 'tw_token', 'post_message');
				if ( isset($_POST['action']) && $_POST['action'] === 'update') {
					foreach ($keys as $key) {
						$option = get_option($key);
						if( $option === false && isset($_POST[$key]) ){
							add_option($key, $_POST[$key]);
						}else if( isset($_POST[$key]) && $_POST[$key] !== $option ){
							update_option($key, $_POST[$key]);
						}else if( !isset($_POST[$key]) || empty($_POST[$key]) ){
							delete_option($key);
						}
					}
				}
			?>

			<section class="fb_auth">
				<h2>Facebookアカウント設定</h2>
				<?php if(Facebook_Util::authorize()): $page_list = Facebook_Util::get_page_list(); $html='';?>
				<div class="fb_auth_complete">
					<select id="fb_page_select" name="fb_page_select">
					<?php foreach($page_list['data'] as $key=>$page): $html .= '<input type="hidden" id="fb_'.$page['id'].'" value="'.$page['access_token'].'">'?>
						<option value="<?php echo $page['id'] ?>" <?php echo (get_option('fb_page_select') === $page['id']) ? 'selected' : '' ?>><?php echo $page['name'] ?></option>
					<?php endforeach; ?>
					</select>
					<?php echo $html; ?>
					<input type="hidden" name="fb_selected_token" id="fb_selected_token" value="<?php echo get_option('fb_selected_token'); ?>"/>
					<!-- <button class="button-secondary">このアカウントに投稿する</button> -->
				</div>
				<?php else: ?>
				<div class="fb_no_auth">
					<a href="<?php echo Facebook_Util::get_oauth_url(); ?>" class="button-secondary">Facebookと連携する</a>
				</div>
				<?php endif; ?>
			</section>


			<section class="tw_auth">
				<h2>Twitterアカウント設定</h2>
				<?php if(Twitter_Util::authorize()):?>
				<div class="tw_auth_complete">
				認証済み
				</div>
				<?php else: ?>
				<div class="tw_no_auth">
					<a href="<?php echo Twitter_Util::get_oauth_url() ; ?>" class="button-secondary">Twitterと連携する</a>
				</div>
				<?php endif; ?>
			</section>

			<section class="post_message">
				<h2>投稿メッセージ</h2>
				<textarea name="post_message" style="margin: 2px; width: 388px; height: 136px;"><?php echo get_option('post_message',''); ?></textarea>
			</section>

			<p class="submit">
				<input type="hidden" name="action" value="update"/>
				<input type="submit" name="Submit" class="button-primary" value="変更を保存" />
			</p>
		</form>
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