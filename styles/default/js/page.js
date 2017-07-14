var $j = jQuery.noConflict();

$j(function(){
    //Аккордион в «Задачах»
    //=================================================================
    $j('.s_tasks__item').each(function(){
        var	$e = $j(this),
            $trigger = $e.children('.s_tasks__item__title');

        $trigger.click(function(){
            $e.toggleClass('open');
        });
    });
});