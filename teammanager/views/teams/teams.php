<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('team_name'), lang('actions'));

	foreach($teams as $team){
		if($team['managed'] == true){
			$l = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_team'.AMP.'team='.$team['id'].'"><strong>'.$team['name'].'</strong></a>';
			$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_team'.AMP.'team='.$team['id'].'">'.lang('edit').'</a>';
			$this->table->add_row($l, $e);
		}else{
			$l = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_team'.AMP.'team='.$team['id'].'">'.lang('edit').'</a>, <a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_team'.AMP.'team='.$team['id'].'">'.lang('delete').'</a>';
			$this->table->add_row($team['name'], $l);
		}
	}

echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_team')?>
<?=form_submit(array('name' => 'add_team', 'value' => lang('add_team'), 'class' => 'submit'))?>
<?=form_close()?>