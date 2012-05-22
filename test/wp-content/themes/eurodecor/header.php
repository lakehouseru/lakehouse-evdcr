<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php bloginfo('title'); ?></title>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
		<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php bloginfo('template_url'); ?>/js/tip/tipTip.css" rel="stylesheet" type="text/css" />
		<script src="http://yandex.st/jquery/1.7.2/jquery.js" language="javascript" type="text/javascript"></script>
		<script src="<?php bloginfo('template_url'); ?>/js/Slides/source/slides.jquery.js" language="javascript" type="text/javascript"></script>
		<script src="<?php bloginfo('template_url'); ?>/js/tip/jquery.tipTip.js" language="javascript" type="text/javascript"></script>
		<script src="<?php bloginfo('template_url'); ?>/js/jqswf.js" language="javascript" type="text/javascript"></script>
		<script src="<?php bloginfo('template_url'); ?>/js/app.js" language="javascript" type="text/javascript"></script>
		<?php wp_head(); ?>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<a href="<?php bloginfo('url'); ?>" id="logo_up" class="fltlft"><img src="<?php bloginfo('template_url'); ?>/images/logo.png"  style="margin-top:12px; margin-left:24px;"/></a>
				
				<? if('wpsc-product'!=get_post_type()){?> 
				<a href="<?bloginfo('url');?>/products-page/oboi/" class="fltrt" > <img src="<?php bloginfo('template_url'); ?>/images/go_cat.png"  /></a>
			<? }?>
			<? if(!is_home()){?> 
			<a id="home" src='<? bloginfo('template_url');?>/images/home.swf' href="<? bloginfo('url');?>">
			</a><? }?>
			</div><ul id="menu">
				<li class="ani">
					<a href="<?php bloginfo('url'); ?>/about">о компании</a>
				</li>
				<li class="ani">
					<a href="#news">новости</a>
				</li>
				<li class="ani">
					<a href="<?php bloginfo('url'); ?>/products-page/oboi">каталог</a>
				</li>
				<li class="ani">
					<a href="#about">магазины</a>
				</li>
				<li class="ani">
					<a href="#about">контакты</a>
				</li>
				<li class="last">
					<div id="login">
						<a href="<?php bloginfo('url'); ?>/wp-admin/"> вход</a> | <a href="<?php bloginfo('url'); ?>/wp-login.php?action=register"> регистрация</a>
					</div>
				</li>
			</ul>


