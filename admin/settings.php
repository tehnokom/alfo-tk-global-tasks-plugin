<?php
/** This file is part of TKGlobalTasks Plugin project
 *
 * @desc Plugin settings page
 * @package TKGlobalTasks
 * @version 0.1a
 * @author Ravil Sarvaritdinov <ra9oaj@gmail.com>
 * @copyright 2017 Tehnokom.su
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 * @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 * @link https://github.com/tehnokom/alfo-tk-global-tasks-plugin
 */

?>
<div>
    <h2><?php echo _x('Tasks Settings', 'Admin menu', 'tkgt') ?></h2>
    <form method="POST" action="options.php">
        <?php
        settings_fields('tkgt_settings_group');
        do_settings_sections('tkgi_settings');
        submit_button();
        ?>
    </form>
</div>
