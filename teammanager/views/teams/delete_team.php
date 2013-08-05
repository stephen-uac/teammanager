<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_team'.AMP.'team='.$team_id)?>

<h3><?=lang('delete_team_question')?> (&quot;<?=$team_name?>&quot;)</h3>
<p class="notice"><?=lang('action_can_not_be_undone')?></p>
<p>
	<?=form_submit(array('name' => 'submit', 'value' => lang('delete_team'), 'class' => 'submit'))?>
</p>
<?=form_close()?>