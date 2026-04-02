<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
	<title><?php wp_title(); ?></title>
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//use.typekit.net/dso0mjc.css">    
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/app.css?version=0.0.88"> 
     <?php 
  
      /* Always have wp_head() just before the closing </head>
       * tag of your theme, or you will break many plugins, which
       * generally use this hook to add elements to <head> such
       * as styles, scripts, and meta tags.
       */
      wp_head();
   ?>
  </head>
  <body <?php body_class(); ?>>

    <nav id="mobile-nav" class="text-center">
      <div class="row gm-top--2">
        <div class="column small-12 gm-bottom--4">
          <div class="row align-middle">
            <div class="column small-8 text-left">
              <img src="<?php bloginfo('template_directory'); ?>/img/logo.svg" alt="Executive Heads" class="header__logo__img" />
            </div>
            <div class="column small-4">
              <a onclick="toggleNav();" class="mobile-nav__close">&times;</a>              
            </div>
          </div>
        </div>
        <div class="column small-12">
          <?php wp_nav_menu(array('theme_location' => 'header', 'items_wrap' => '<ul class="%2$s">%3$s</ul>',)); ?>
        </div>
      </div>
    </nav>

    <div id="main">

      <header class="row shrink-width align-bottom">
        <div class="column small-6 large-2 header__logo">
          <a href="/">
            <img src="<?php bloginfo('template_directory'); ?>/img/logo.svg" alt="" class="header__logo__img" />
          </a>
        </div>
        <div class="column text-right show-for-large large-10">
          <nav class="nav">
            <?php wp_nav_menu(array('theme_location' => 'header', 'menu_class' => 'inline-list--double','items_wrap' => '<ul class="%2$s">%3$s</ul>',)); ?>

            <?php /*            
            <ul class="inline-list--double">
              <?php foreach ($primaryNav as $navItem): ?>
                <li class="nav__li"><a class="nav__a" href="<?php echo $navItem->url; ?>"><?php echo $navItem->title; ?></a></li>
              <?php endforeach; ?>
            </ul>
            */ ?>

          </nav>
        </div>
        <div class="column small-6 hide-for-large text-right">
          <a onclick="toggleNav();" class="header__mobile-nav">
            <img src="<?php bloginfo('template_directory'); ?>/img/menu.svg" alt="Open Menu" width="30" />
          </a>
        </div>
	  
      </header>