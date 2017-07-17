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

require_once(ABSPATH . 'wp-admin/includes/template.php');

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

    add_settings_field('tkgt_subpages_slugs',
        _x('Subpages slugs', 'Admin Settings', 'tkgt'),
        'tkgt_get_subpages_slugs',
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
    $enabled_slugs = TK_GTask::taskSettings(true)->enabled_for;
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
    $cur_slug = TK_GTask::taskSettings(true)->slug;
    $cur_slug = empty($cur_slug) ? '/tasks' : $cur_slug;
    ?>
    <code><?php echo home_url() ?>/%permalink%</code>
    <input id="tkgt_tasks_slug" name="tkgt_settings[slug]" type="text" value="<?php echo esc_url($cur_slug) ?>">
    <?php
}

function tkgt_get_subpages_slugs()
{
    $cur_slugs = TK_GTask::taskSettings()['subpages'];

    ?>
    <p>
        <?php echo _x('Specify the slugs for the respective subpages (including delimiters)',
            'Admin Settings', 'tkgt') ?>:
    </p>
    <div style="display: table-row">
        <div style="display: table-cell">
            <label for="tkgt_actions_slug"><?php echo __('Actions subpage', 'tkgt') ?></label>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <input id="tkgt_actions_slug" name="tkgt_settings[subpages][actions]" type="text"
                   value="<?php echo esc_url($cur_slugs['actions']) ?>">
        </div>
    </div>
    <div style="display: table-row">
        <div style="display: table-cell">
            <label for="tkgt_suggestions_slug"><?php echo __('Suggestions subpage', 'tkgt') ?></label>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <input id="tkgt_suggestions_slug" name="tkgt_settings[subpages][suggestions]" type="text"
                   value="<?php echo esc_url($cur_slugs['suggestions']) ?>">
        </div>
    </div>
    <div style="display: table-row">
        <div style="display: table-cell">
            <label for="tkgt_trash_slug"><?php echo __('Trash subpage', 'tkgt') ?></label>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <input id="tkgt_trash_slug" name="tkgt_settings[subpages][trash]" type="text"
                   value="<?php echo esc_url($cur_slugs['trash']) ?>">
        </div>
    </div>
    <?php
}

function tkgt_check_options($options)
{
    $valid = array();
    file_put_contents(__FILE__ . '.log', serialize($options) . "\r\n", FILE_APPEND);
    foreach ($options as $opt => $item) {
        if ($opt === 'enabled_for' && is_array($item)) {
            $valid[$opt] = array_keys($item);
        } else if ($opt === 'subpages' && is_array($item)) {
            $valid[$opt] = $item;
        } else {
            $valid[$opt] = esc_url_raw($item);
        }
    }

    return $valid;
}