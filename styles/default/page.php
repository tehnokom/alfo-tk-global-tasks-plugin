<?php

define('TKGT_STYLE_ROOT', plugin_dir_path(__FILE__));
define('TKGT_STYLE_URL', plugin_dir_url(__FILE__));

wp_enqueue_style('tkgt-page-css');
wp_enqueue_script('tkgt-page-js');

tkgt_before_header();

get_header();
?>

    <div class="tkgt-block">
        <?php tkgt_get_breadcrumbs(); ?>
    </div>
    <div class="tkgt-block">
        <?php
        tkgt_the_menu();
        tkgt_the_tasks();
        ?>
    </div>
<?php
get_footer();