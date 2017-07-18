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

    add_settings_field('tkgt_fullpage_url',
        _x('URL of Detailed page', 'Admin Settings', 'tkgt'),
        'tkgt_get_fullpage_url',
        'tkgi_settings',
        'tkgt_general_section'
    );

    add_settings_field('tkgt_post_types',
        _x('Types of Posts', 'Admin Settings', 'tkgt'),
        'tkgt_get_post_type',
        'tkgi_settings',
        'tkgt_general_section'
    );

    add_settings_field('tkgt_uri_slugs',
        _x('Slugs for types of posts', 'Admin Settings', 'tkgt'),
        'tkgt_get_fullpage_slug',
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
    $enabled_slugs = TK_GTaskPage::taskSettings(true)->enabled_for;
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
                       type="checkbox" <?php echo $checked ?> >
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
    $cur_slug = TK_GTaskPage::taskSettings(true)->slug;
    $cur_slug = empty($cur_slug) ? '/tasks' : $cur_slug;
    ?>
    <code><?php echo home_url() ?>/%permalink%</code>
    <input id="tkgt_tasks_slug" name="tkgt_settings[slug]" type="text" value="<?php echo $cur_slug ?>"
           required="required">
    <?php
}

function tkgt_get_fullpage_url()
{
    $errors = TK_GTaskPage::taskSettings(true)->fullpage_url_errors;
    $cur_url = empty($errors) ? TK_GTaskPage::taskSettings(true)->fullpage_url
        : TK_GTaskPage::taskSettings(true)->fullpage_url_bad;
    ?>
    <p>
        <?php echo _x('Here you can change the URL for a detailed task page', 'Admin Settings', 'tkgt') ?>:
    </p>
    <?php
    if (!empty($errors)) {
        ?>
        <p style="color: red;">
           <code style="color: red;"><?php echo _x('Warning!', 'Admin Settings', 'tkgt') ?></code>
            <br>
            <?php echo _x('Errors in configuration. To maintain the functionality, the default value will be used',
                'Admin Settings', 'tkgt') . ': <code style="color: green;">tasks/%post_id%/%task_id%</code>' ?>
            <br>
            <code style="color: red;"><?php echo _x('Errors', 'Admin Settings', 'tkgt') ?>:</code>
            <br>
            <?php foreach ($errors as $key => $error) {
                $line_num = $key + 1;
                echo "$line_num. $error<br>";
            }?>
        </p>
        <?php
    }
    ?>
    <code><?php echo home_url() ?>/</code>
    <input id="tkgt_fullpage_url" name="tkgt_settings[fullpage_url]" type="text"
           value="<?php echo $cur_url ?>">
    <p>
        <code>%task_id%</code> - <?php echo _x('Global Task ID (number)', 'Admin Settings', 'tkgt') ?>,
        <code>%task_iid%</code> - <?php echo _x('Internal Task ID within the parent post (number)',
            'Admin Settings', 'tkgt') ?>,
        <code>%task_post_slug%</code>
        - <?php echo _x('Post type identifier (string), values are taken from the setting',
                'Admin Settings', 'tkgt') . ' <b>"' . _x('Slugs for types of posts', 'Admin Settings', 'tkgt') . '"</b>' ?>
        ,
        <code>%post_id%</code> - <?php echo _x('Global ID of the post (number)', 'Admin Settings', 'tkgt') ?>,
        <code>%post_iid%</code>
        - <?php echo _x('The internal number of the post (number) must be used together with <b>%post_type%</b> or <b>%task_post_slug%</b>, in most cases equal to',
                'Admin Settings', 'tkgt') . ' <b>%post_id%</b>' ?>,
        <code>%post_type%</code> - <?php echo _x('Type of post (string)', 'Admin Settings', 'tkgt') ?>
    </p>
    <?php
}

function tkgt_get_fullpage_slug()
{
    $cur_slugs = TK_GTaskPage::taskSettings()['uri_slugs'];
    $posts_types = TK_GTaskPage::taskSettings(true)->enabled_for;
    ?>
    <p>
        <?php echo _x('Here you can determine the slug for a particular type of post. ' .
            'This slug will be used to identify the tasks of a certain type of post.', 'Admin Settings', 'tkgt') ?>
    </p>
    <?php
    if (!empty($posts_types)) {
        foreach ($posts_types as $post_type) {
            ?>
            <label for="tkgt_uri_slug_<?php echo $post_type ?>">
                <code><?php echo $post_type ?></code>
            </label>
            <input id="tkgt_uri_slug_<?php echo $post_type ?>" name="tkgt_settings[uri_slugs][<?php echo $post_type ?>]"
                   type="text" value="<?php echo $cur_slugs[$post_type] ?>"
                   style="margin-right: 15px" required="required">
            <?php
        }
    } else {
        ?>
        <h3><?php echo _x('This setting will be after you select at least one type of posts and save the settings.',
                'Admin Settings', 'tkgt') ?></h3>
        <?php
    }
}

function tkgt_get_subpages_slugs()
{
    $cur_slugs = TK_GTaskPage::taskSettings()['subpages'];

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
                   value="<?php echo $cur_slugs['actions'] ?>" required="required">
        </div>
    </div>
    <div style="display: table-row">
        <div style="display: table-cell">
            <label for="tkgt_suggestions_slug"><?php echo __('Suggestions subpage', 'tkgt') ?></label>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <input id="tkgt_suggestions_slug" name="tkgt_settings[subpages][suggestions]" type="text"
                   value="<?php echo $cur_slugs['suggestions'] ?>" required="required">
        </div>
    </div>
    <div style="display: table-row">
        <div style="display: table-cell">
            <label for="tkgt_trash_slug"><?php echo __('Trash subpage', 'tkgt') ?></label>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <input id="tkgt_trash_slug" name="tkgt_settings[subpages][trash]" type="text"
                   value="<?php echo $cur_slugs['trash'] ?>" required="required">
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
        } else if (($opt === 'subpages' || $opt === 'uri_slugs') && is_array($item)) {
            foreach ($item as $key => $val) {
                $valid[$opt][$key] = esc_url($val, array(''));
            }
        } else if ($opt === 'fullpage_url') {
            $errors = array();
            $post_id = boolval(substr_count($item, '%post_id%'));
            $post_type = boolval(substr_count($item, '%post_type%'))
                || boolval(substr_count($item, '%task_post_slug%'));
            $task_id = boolval(substr_count($item, '%task_id%'));

            if(!$task_id && !substr_count($item, '%task_iid%')) {
                $errors[] = _x('In the configuration, one of the identifiers must be present: ',
                        'Admin Settings', 'tkgt') .
                    ' <code>%task_id%</code>, <code>%task_iid%</code>';
            }

            if (!$task_id && !$post_id && !substr_count($item, '%post_iid%')) {
                $errors[] = _x('In the configuration, one of the identifiers must be present: ',
                        'Admin Settings', 'tkgt') .
                    ' <code>%post_id%</code>, <code>%post_iid%</code>';
            }

            if (!$task_id && !$post_id && substr_count($item, '%post_iid%') && !$post_type) {
                $errors[] = '<code>%post_iid%</code> ' . _x('must be used in conjunction with', 'tkgt') .
                    '<code>%post_type%</code> ' . __('or') . ' <code>%task_post_slug%</code>';
            }

            if (!empty($errors)) {
                $valid['fullpage_url_errors'] = $errors;
                $valid['fullpage_url_bad'] = $item;
                $valid['fullpage_url'] = 'tasks/%post_id%/%task_id%';
            } else {
                $valid['fullpage_url'] = esc_url($item, array(''));
            }
        } else {
            $valid[$opt] = esc_url($item, array(''));
        }
    }

    return $valid;
}