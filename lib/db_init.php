<?php

function tkgt_check_version()
{
    $cur_version = '0.1';
    $installed_version = tkgt_prepare_version(get_option('tkgt_db_version'));

    if (empty($installed_version)) {
        tkgt_db_install($cur_version);
    } elseif (floatval($cur_version) > floatval($installed_version)) {
        tkgt_upgrade_log("Start upgrading DB from {$installed_version} to {$cur_version}");
        tkgt_db_update($installed_version, $cur_version);
    }
}

function tkgt_prepare_version($ver)
{
    return (preg_replace('/([a-zA-Z]+)/', '', $ver));
}

function tkgt_upgrade_log($msg, $type = 'i')
{
    $prefix = date("[Y-m-d H:i:s]:");
    $type_str = 'Info';

    switch ($type) {
        case 'w':
            $type_str = 'Warning';
            break;

        case 'e':
            $type_str = 'Error';
            break;

        default:
            break;
    }

    file_put_contents(TKGT_ROOT . 'upgrade.log', "{$prefix} {$type_str}: {$msg}\r\n", FILE_APPEND);
}

function tkgt_db_install($cur_version)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'tkgt_tasks_links';
    $charset_collate = " DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $sql = "CREATE TABLE {$table_name} (
				  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `parent_id` bigint(20) unsigned NOT NULL,
				  `child_id` bigint(20) unsigned NOT NULL,
				  `create_date` timestamp DEFAULT NOW(),
				  PRIMARY KEY (`id`),
				  INDEX `parent_id` (`parent_id`),
				  INDEX `child_id` (`child_id`),
				  UNIQUE `link` (`parent_id`, `child_id`)
				){$charset_collate};";

        $wpdb->query($sql);
    }

    $table_name = $wpdb->prefix . 'tkgt_tasks';
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $sql = "CREATE TABLE {$table_name} ( `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT , 
                  `post_id` BIGINT(20) UNSIGNED NOT NULL ,
                  `task_type_id` BIGINT(20) UNSIGNED NOT NULL, 
                  `status` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' ,  
                  `creation_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                  `last_update` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
                  `last_update_user` BIGINT(20) UNSIGNED NULL DEFAULT NULL , 
                  `start_date` DATE NULL DEFAULT NULL , 
                  `end_date` DATETIME NULL DEFAULT NULL , 
                  `actual_end_date` DATETIME NULL DEFAULT NULL ,
                  `internal_id` INTEGER UNSIGNED NOT NULL DEFAULT '1',
                  `serial_number` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),  
                  UNIQUE `task_type` (`post_id`, `task_type_id`),
                  INDEX `status` (`status`),
                  INDEX `serial_number` (`serial_number`),
                  INDEX `internal_id` (`internal_id`),
                  UNIQUE `unique_internal` (`post_id`, `internal_id`)
                  ){$charset_collate};";

        $wpdb->query($sql);
    }

    $table_name = $wpdb->prefix . 'tkgt_tasks_types';
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $sql = "CREATE TABLE `{$wpdb->prefix}tkgt_tasks_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `post_type` varchar(256) DEFAULT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `status` TINYINT(2) NOT NULL DEFAULT 0,
  `creation_date` datetime NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update_user` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `post_type` (`post_type`, `type`)
){$charset_collate}";

        $wpdb->query($sql);
    }

    $res = $wpdb->get_results("SHOW TRIGGERS WHERE `Trigger` LIKE 'tkgt_internal_id'", ARRAY_A);
    if (is_array($res) && !count($res)) {
        $sql = "CREATE TRIGGER `tkgt_internal_id` BEFORE INSERT ON `{$wpdb->prefix}tkgt_tasks` FOR EACH ROW 
SET NEW.internal_id = (SELECT COUNT(1) FROM {$wpdb->prefix}tkgt_tasks 
WHERE post_id = NEW.post_id) + 1";

        $wpdb->query($sql);
    }

    $res = $wpdb->get_results("SHOW TRIGGERS WHERE `Trigger` LIKE 'tkgt_create%'", ARRAY_A);
    if (is_array($res) && !count($res)) {
        $sql = "CREATE TRIGGER `tkgt_create_new_task` BEFORE INSERT ON `{$wpdb->prefix}tkgt_tasks_links` FOR EACH ROW 
UPDATE {$wpdb->prefix}tkgt_tasks 
SET serial_number = (SELECT COUNT(1) FROM {$wpdb->prefix}tkgt_tasks_links 
                     WHERE parent_id = NEW.parent_id) + 1
WHERE id = NEW.child_id;";

        $wpdb->query($sql);
    }

    update_option('tkgt_db_version', $cur_version);
}

function tkgt_db_update($installed_version, $cur_version)
{
    global $wpdb;

    $slug = TK_GProject::slug;

    $patches = array();

    if (!empty($patches[$installed_version])) {
        tkgt_upgrade_log("	Patching DB {$installed_version} => {$patches[$installed_version]['ver_after']}");

        if ($patches[$installed_version]['sql'] == 'none') {
            update_option('tkgt_db_version', $patches[$installed_version]['ver_after']);
        } else {
            $result = false;

            foreach ($patches[$installed_version]['sql'] as $path) {
                tkgt_upgrade_log("		SQL: {$path}");

                $result = $wpdb->query($path);

                if (!$result) {
                    if (!empty($wpdb->last_error)) {
                        //ошибка - не прошел патч SQL
                        tkgt_upgrade_log("Error during patch installation!", 'e');
                        tkgt_upgrade_log("SQL messages text: {$wpdb->last_error}", 'e');
                        return;
                    }

                    $result = true; //не критичная ошибка
                    tkgt_upgrade_log("The patch is not changed. Maybe there is nothing to fix or fixes have been made earlier.", 'w');
                } else {
                    tkgt_upgrade_log("		SQL: ОК");
                }
            }

            if ($result) {
                tkgt_upgrade_log("	End patching DB {$installed_version} => {$cur_version}");
                update_option('tkgt_db_version', $patches[$installed_version]['ver_after']); //обновились до следующей версии
                $new_version = tkgt_prepare_version(get_option('tkgt_db_version'));

                if (floatval($new_version) < floatval($cur_version)) {
                    tkgt_db_update($new_version, $cur_version);
                } else {
                    tkgt_upgrade_log("End upgrading DB from {$installed_version} to {$cur_version}");
                }
            }
        }
    } else {
        tkgt_upgrade_log("You can not upgrade from version {$installed_version}!", 'e');
    }
}
