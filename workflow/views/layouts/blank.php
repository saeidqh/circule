<?php
use yii\helpers\Html;
use yii\bootstrap\BootstrapPluginAsset;
use app\assets\AppAsset;
use app\assets\ThemeAsset;

BootstrapPluginAsset::register($this);
AppAsset::register($this);
ThemeAsset::register($this);
?>
<!DOCTYPE html>
<html lang=en>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<meta charset=utf-8>
<script type="text/javascript" src="js/jquery.js"></script>
<title><?= Html::encode($this->title); ?></title>
<!-- Mobile specific metas -->
<meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1">

<meta name=msapplication-TileColor content=#3399cc>

<body>

<!-- Google Analytics:  -->
<?php echo $content ; ?>
