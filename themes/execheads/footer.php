<?php 
    $menuLocations = get_nav_menu_locations();                                            
    $footer1 = wp_get_nav_menu_items($menuLocations['about']);
    $footer2 = wp_get_nav_menu_items($menuLocations['services']);
    $footer3 = wp_get_nav_menu_items($menuLocations['assignments']);
    $footer4 = wp_get_nav_menu_items($menuLocations['knowledgehub']);
?>
      <footer>
        <div class="clearfix"></div>
        <div class="row footer__row">
          <div class="column text-center large-text-left small-12 large-8 footer__column footer__column--first">
            <div class="row">
              <div class="column small-12 large-3">
                  <p class="blue-text uppercase">About</p>
                  <p>
                    <?php foreach ($footer1 as $navItem1): ?>
                        <a class="uppercase" target="<?php echo $navItem1->target; ?>" href="<?php echo $navItem1->url; ?>"><small><?php echo $navItem1->title; ?></small></a><br />
                    <?php endforeach; ?>
                  </p>
              </div>
              <div class="column small-12 large-3">
                  <p class="blue-text uppercase">Services</p>
                  <p>
                    <?php foreach ($footer2 as $navItem2): ?>
                        <a class="uppercase" target="<?php echo $navItem2->target; ?>" href="<?php echo $navItem2->url; ?>"><small><?php echo $navItem2->title; ?></small></a><br />
                    <?php endforeach; ?>
                  </p>
              </div>
              <div class="column small-12 large-3">
                  <p class="blue-text uppercase">Assignments</p>
                  <p>
                    <?php foreach ($footer3 as $navItem3): ?>
                        <a class="uppercase" target="<?php echo $navItem3->target; ?>" href="<?php echo $navItem3->url; ?>"><small><?php echo $navItem3->title; ?></small></a><br />
                    <?php endforeach; ?>
                  </p>
              </div>
              <div class="column small-12 large-3">
                  <p class="blue-text uppercase">Knowledge Hub</p>
                  <p>
                    <?php foreach ($footer4 as $navItem4): ?>
                        <a class="uppercase" target="<?php echo $navItem4->target; ?>" href="<?php echo $navItem4->url; ?>"><small><?php echo $navItem4->title; ?></small></a><br />
                    <?php endforeach; ?>
                  </p>
              </div>
            </div>
          </div>
          <div class="column text-center large-text-left small-12 large-4 footer__column footer__column--second">
            <div class="row align-center">
              <div class="column small-12 large-10">
                <p class="uppercase"><strong>Contact</strong></p>
                <?php the_field('footer_contact_information', 'option'); ?>
                <ul class="inline-list gm-top--2 gm-bottom--2">
                  <li><a href="https://www.linkedin.com/company/executiveheads/"><img src="<?php bloginfo('template_directory'); ?>/img/linkedin.svg" alt="Executive Heads on LinkedIn" width="25" /></a></li>
                  <li><a href="https://vimeo.com/executiveheads"><img src="<?php bloginfo('template_directory'); ?>/img/vimeo.svg" alt="Executive Heads on YouTube" width="25" /></a></li>
                  <li><a href="https://twitter.com/Exec_Heads"><img src="<?php bloginfo('template_directory'); ?>/img/twitter.svg" alt="Executive Heads on Twitter" width="25" /></a></li>
                  <li><a href="https://www.instagram.com/executive_heads/"><img src="<?php bloginfo('template_directory'); ?>/img/instagram.svg" alt="Executive Heads on Instagram" width="25" /></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      </footer>

      <div class="row align-middle absolute-footer">
        <div class="column small-12 medium-6 large-8">
          <div class="row align-middle">
            <div class="column medium-text-left large-text-center">
              <img src="<?php bloginfo('template_directory'); ?>/img/avatar.svg" alt="Executive Heads" class="width-100 absolute-footer__avatar" />
            </div>
            <div class="column small-12 medium-10">
              <p class="no-margin-bottom"><small>&copy; <?php echo date('Y'); ?> EXECUTIVE HEADS<br />All rights reserved. Heads Resourcing Ltd.<br />Registered address: Fleming Court Leigh Road, Eastleigh, Southampton, Hampshire, SO50 9PD</small></p>
            </div>
          </div>
        </div>
        <div class="column small-12 medium-6 large-4">
          <p class="no-margin-bottom"><small><a href="/terms-of-use" class="uppercase">Terms of use</a> | <a href="/privacy-policy" class="uppercase">Privacy Policy</a><br />Design by <a target="_blank" href="https://pogodesign.co.uk/">Pogo</a>. Created By <a target="_blank" href="https://www.createdbyarc.com/">Arc</a>.</small></p>
        </div>
      </div>

    </div><!--#main-->
    <script src="<?php bloginfo('template_directory'); ?>/js/libraries.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/app.js?v=0.0.1023"></script>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//use.typekit.net/dso0mjc.css">    
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153113030-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153113030-1');
</script>
    <?php wp_footer(); ?>
  </body>
</html>