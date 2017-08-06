<?php
global $tkgt_task, $post;

$task_parent_stage = tkgt_get_parent_stage();
$task_parent_direction = tkgt_get_parent_direction();

tkgt_before_header();

get_header();
?>
    <div class="tkgt-block">
        <?php tkgt_get_breadcrumbs(); ?>
    </div>
    <div class="tkgt-card">
        <div class="tkgt-header">
            <div>
                <div style="width: 25px; height: 25px; border-radius: 5px; background: #dfdfdf;"></div>
            </div>
            <div><?php echo __('Task', 'tkgt') ?>
                #<?php echo TK_GProject::postToId($post->ID) . '-' . $tkgt_task->internal_id ?>
            </div>
            <div></div>
        </div>
        <div class="tkgt-body">
            <div>
            </div>
            <div>
                <div class="title"><h4><?php echo __('Direction', 'tkgt') ?>:</h4></div>
                <div>
                    <div class="value"> <?php tkgt_the_parent_direction() ?></div>
                </div>
            </div>
            <div>
                <div class="title"><h4><?php echo __('Stage', 'tkgt') ?>:</h4></div>
                <div>
                    <div class="value"> <?php tkgt_the_parent_stage() ?></div>
                </div>
            </div>
        </div>
        <div class="desc">
            <div><h4><?php echo __('Description', 'tkgt') ?></h4></div>
            <div><?php tkgt_the_description() ?></div>
        </div>
        <div class="tkgt-body last">
            <div>
                <div class="title"><h4><?php echo __('Author') ?>:</h4></div>
                <div>
                    <div class="value"> <?php echo $tkgt_task->creation_date ?></div>
                </div>
                <div class="title"><h4><?php echo __('Creation date', 'tkgt') ?>:</h4></div>
                <div>
                    <div class="value disabled"> <?php echo tkgt_the_creation_date() ?></div>
                </div>
            </div>
            <div>
                <div class="title"><h4><?php echo __('Type of task', 'tkgt') ?>:</h4></div>
                <div>
                    <div class="value"><?php tkgt_the_task_type() ?></div>
                </div>
                <div class="title"><h4><?php echo __('Start date', 'tkgt') ?></h4></div>
                <div>
                    <div class="value"> <?php echo $tkgt_task->start_date ?></div>
                </div>

            </div>
            <div>
                <div class="title"><h4><?php echo __('Periodicity', 'tkgt') ?></h4></div>
                <div>
                    <div class="value"></div>
                </div>
                <div class="title"><h4><?php echo __('End date', 'tkgt') ?></h4></div>
                <div>
                    <div class="value"> <?php echo $tkgt_task->end_date ?></div>
                </div>
            </div>
        </div>
        <div class="messages">
            <h4><?php echo __('Working messages', 'tkgt') ?></h4>
            <?php tkgt_comments() ?>
        </div>
    </div>
<?php
get_footer();
