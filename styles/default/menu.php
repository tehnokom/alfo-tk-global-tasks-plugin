<?php
global $wp_query;

define('TKGT_STYLE_URL', plugin_dir_url(__FILE__));

$current_page = home_url($_SERVER['REQUEST_URI']);
?>
    <ul class="tkgt-menu">
        <?php foreach ($tkgt_menu_items as $menu_item) {
            $current = $current_page === $menu_item['href'] || $current_page === $menu_item['fullpage_href'] ?
                'class="current selected"' : '';
            $href = $menu_item['href'];
            $full_href = $menu_item['fullpage_href'];
            $full_title = __('Advanced view', 'tkgt');
            $full_target = 'target="_blank"';

            if (TK_GTaskPage::isFullPage() && !TK_GTaskPage::isSingleFullPage()) {
                $href = !empty($full_href) ? $full_href : $href;
                $full_title = __('Open in new tab', 'tkgt');
            }
            ?>
            <li <?php echo $current ?> >
                <a href="<?php echo $href ?>"><?php echo $menu_item['title'] ?></a>
                <?php
                if (!empty($full_href)) {
                    ?>
                    <a href="<?php echo $full_href ?>" title="<?php echo $full_title ?>" <?php echo $full_target ?> >
                        <img src="<?php echo TKGT_STYLE_URL . 'images/nw.png' ?>">
                    </a>
                    <?php
                }
                ?>
            </li>
        <?php } ?>
    </ul>
<?php
