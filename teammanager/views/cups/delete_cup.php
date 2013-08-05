<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_cup'.AMP.'cup='.$cup_id)?>

<h3><?=lang('delete_cup_question')?> (&quot;<?=$cup_name?>&quot;)</h3>
<p class="notice"><?=lang('action_can_not_be_undone')?></p>
<p>
	<?=form_submit(array('name' => 'submit', 'value' => lang('delete_cup'), 'class' => 'submit'))?>
</p>
<?=form_close()?>