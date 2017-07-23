<ul class="tkgt-breadcrumbs">
    <?php
    foreach ((array)$tkgt_path_items as $item) {
        ?>
        <li>
            <a href="<?php echo $item['href'] ?>"><?php echo $item['title'] ?></a>
        </li>
        <?php
    }
    ?>
</ul>
