<?php

require dirname(__FILE__)."/ui/post.php";
require dirname(__FILE__)."/ui/setting.php";

class Publicize_UI {

	public static function initialize(){
		add_action('publish_post', array('Publicize_UI_Post', 'pbcz_published_post'),1,2);
		add_action( 'admin_print_scripts', array('Publicize_UI_Setting', 'add_pbcz_script') );
		add_action( 'wp_ajax_pbcz_update_setting', array('Publicize_UI_Setting','update_setting') );
		add_action( 'wp_ajax_nopriv_pbcz_update_setting', array('Publicize_UI_Setting','update_setting') );
		add_action('admin_menu', array('Publicize_UI_Post','add_pbcz_meta_box') );
		add_action('admin_menu', array('Publicize_UI_Setting','add_pbcz_submenu') );
	}

}