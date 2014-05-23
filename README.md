#wordpress-plugin-publicize
==========================

###1. Wordpressのpluginディレクトリへ設置
```
$ cd [PLUGIN_DIR]
$ git clone https://github.com/kubio/wordpress-plugin-publicize
```

###2. 必要なライブラリをインストール
```
$ cd ./wordpress-plugin-publicize
$ composer install
```

###3. それぞれのアプリIDなどを入力します。
``` php:publicize.php
Facebook_Util::initialize('[Your fb app_id]', '[Your fb app_secret]');
Twitter_Util::initialize('[Your twitter app_id]','[Your twitter app_secret]');
```

###4. Wordpressの管理画面で、プラグインを有効化してください。
