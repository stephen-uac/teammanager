<h3><?=lang('friendly_matches')?></h3>

<?php

	// matches

	$this->table->set_template($cp_table_template);
	//$this->table->set_heading(lang('date'), lang('friendly_matches'), lang('pre_match'), lang('post_match'), lang('match_stats'), lang('actions'));
	$this->table->set_heading(lang('date'), lang('friendly_matches'), lang('post_match'), lang('actions'));

	foreach($matches as $match){
		$date = date("d/m/Y", $match['kick_off']);
		$t = $match['home_team_name']." ".lang('versus')." ".$match['away_team_name'];
		$pre = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=pre_match'.AMP.'match='.$match['id'].'">'.lang('pre_match').'</a>';
		$post = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=post_match'.AMP.'match='.$match['id'].'">'.lang('post_match').'</a>';
		$stats = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=match_stats'.AMP.'match='.$match['id'].'">'.lang('match_stats').'</a>';
		$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_match'.AMP.'match='.$match['id'].'">'.lang('edit').'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_match'.AMP.'match='.$match['id'].'">'.lang('delete').'</a>';
		//$this->table->add_row($date, $t, $pre, $post, $stats, $e.", ".$d);
		$this->table->add_row($date, $t, $post, $e.", ".$d);
	}

	if(count($matches) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_friendly_matches'), 'colspan' => 6, 'class' => 'no_friendly_matches'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_match')?>
<p><?=form_submit(array('name' => 'add_friendly_match', 'value' => lang('add_friendly_match'), 'class' => 'submit'))?></p>
<?=form_close()?>
