<?php

/*
Plugin Name: Publicize Plugin
Plugin URI:
Description: SNS公開プラグイン(Twitter, Facebook)
Version: 0.1
Author: kubio
*/

require dirname(__FILE__). '/publicize-ui.php';
require dirname(__FILE__). '/lib/util/facebook.php';
require dirname(__FILE__). '/lib/util/twitter.php';

Publicize_UI::initialize();
Facebook_Util::initialize('[Your fb app_id]', '[Your fb app_secret]');
Twitter_Util::initialize('[Your twitter app_id]','[Your twitter app_secret]');


