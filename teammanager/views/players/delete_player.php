<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_player'.AMP.'player='.$player_id)?>

<h3><?=lang('delete_player_question')?> (&quot;<?=$player_name?>&quot;)</h3>
<p class="notice"><?=lang('action_can_not_be_undone')?></p>
<p>
	<?=form_submit(array('name' => 'submit', 'value' => lang('delete_player'), 'class' => 'submit'))?>
</p>
<?=form_close()?>