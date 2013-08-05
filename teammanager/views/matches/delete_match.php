<?=form_open($form_url)?>

<h3><?=lang('delete_match_question')?> (&quot;<?=$match['home_team_name']." ".lang('versus')." ".$match['away_team_name']?>&quot;)</h3>
<p class="notice"><?=lang('action_can_not_be_undone')?></p>
<p>
	<?=form_submit(array('name' => 'submit', 'value' => lang('delete_match'), 'class' => 'submit'))?>
</p>
<?=form_close()?>