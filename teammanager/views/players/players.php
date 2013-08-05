<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('player_name'), lang('actions'));

	foreach($players as $player){
		$n = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_player'.AMP.'player='.$player['id'].'">'.$player['name']." (".$player['position'].")".'</a>';
		$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_player'.AMP.'player='.$player['id'].'">'.lang('edit').'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_player'.AMP.'player='.$player['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($n, $e.", ".$d);
	}
	
	if(count($players) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_players'), 'colspan' => 2, 'class' => 'no_players'));
	}

echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_player')?>
<?=form_submit(array('name' => 'add_player', 'value' => lang('add_player'), 'class' => 'submit'))?>
<?=form_close()?>