<?php
/** This file is part of TKGlobalTasks Plugin project
 *
 * @desc Functions for saving/reading options of plugin
 * @package TKGlobalTasks
 * @version 0.1a
 * @author Ravil Sarvaritdinov <ra9oaj@gmail.com>
 * @copyright 2017 Tehnokom.su
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 * @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 * @link https://github.com/tehnokom/alfo-tk-global-tasks-plugin
 */

function tkgt_add_options()
{
    add_settings_section('tkgt_general_section',
        '',
        '',
        'tkgi_settings');

    add_settings_field('tkgt_tasks_slug',
        _x('Tasks Slug', 'Admin Settings', 'tkgt'),
        'tkgt_get_tasks_slug',
        'tkgi_settings',
        'tkgt_general_section'
    );

    add_settings_field('tkgt_post_types',
        _x('Types of Posts', 'Admin Settings', 'tkgt'),
        'tkgt_get_post_type',
        'tkgi_settings',
        'tkgt_general_section'
    );

    register_setting('tkgt_settings_group',
        'tkgt_settings',
        array('sanitize_callback' => 'tkgt_check_options')
    );
}

function tkgt_get_post_type()
{
    $enabled_slugs = get_option('tkgt_settings')['enabled_for'];
    ?>
    <p>
        <?php echo _x('Select the types of posts for which the task plan will be available',
            'Admin Settings', 'tkgt') ?>:
    </p>
    <ul class="tkgt_slugs">
    <?php
    $post_slugs = get_post_types();
    foreach ($post_slugs as $slug) {
        $checked = in_array($slug, $enabled_slugs) ? 'checked="on"' : '';
        ?>
        <li>
            <input id="tkgt_slug_<?php echo $slug ?>" name="tkgt_settings[enabled_for][<?php echo $slug ?>]"
                type="checkbox" <?php echo $checked ?>>
            <label for="tkgt_slug_<?php echo $slug ?>"><?php echo $slug ?></label>
        </li>
        <?php
    }
    ?>
    </ul>
    <?php
}

function tkgt_get_tasks_slug()
{
    $cur_slug = get_option('tkgt_settings')['slug'];
    $cur_slug = empty($cur_slug) ? '/tasks' : $cur_slug;
    ?>
    <code><?php echo home_url() ?>/%permalink%</code>
    <input id="tkgt_tasks_slug" name="tkgt_settings[slug]" type="text" value="<?php echo esc_url($cur_slug) ?>">
<?php
}

function tkgt_check_options($options)
{
    $valid = array();

    foreach ($options as $opt => $item) {
        if($opt === 'enabled_for' && is_array($item)) {
            $valid[$opt] = array_keys($item);
        } else {
            $valid[$opt] = esc_url_raw($item);
        }
    }

    return $valid;
}