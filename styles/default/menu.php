<?php
global $wp_query;

define('TKGT_STYLE_URL', plugin_dir_url(__FILE__));

$current_page = home_url($_SERVER['REQUEST_URI']);
?>
    <ul class="tkgt-menu">
        <?php foreach ($tkgt_menu_items as $menu_item) {
            $current = $current_page === $menu_item['href'] || $current_page === $menu_item['fullpage_href'] ?
                'class="current selected"' : '';
            ?>
            <li <?php echo $current ?> >
                <a href="<?php echo $menu_item['href'] ?>"><?php echo $menu_item['title'] ?></a>
                <?php
                if (!empty($menu_item['fullpage_href'])) {
                    ?>
                    <a href="<?php echo $menu_item['fullpage_href'] ?>">
                        <img src="<?php echo TKGT_STYLE_URL . 'images/nw.png' ?>">
                    </a>
                    <?php
                }
                ?>
            </li>
        <?php } ?>
    </ul>
<?php
