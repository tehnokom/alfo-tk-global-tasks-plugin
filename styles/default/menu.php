<?php
global $wp_query;
?>
    <ul class="tkgt-menu">
        <?php foreach ($tkgt_menu_items as $menu_item) {
            $current = $menu_item['slug'] === $wp_query->query_vars['tkgt_page'] ? 'class="current selected"' : ''; ?>
            <li <?php echo $current ?> >
                <a href="<?php echo $menu_item['href'] ?>"><?php echo $menu_item['title'] ?></a>
            </li>
        <?php } ?>
    </ul>
<?php
