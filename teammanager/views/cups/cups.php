<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('cup_name'), lang('actions'));

	foreach($cups as $cup){
		$y = ($cup['year_start'] == $cup['year_end'])? $cup['year_start'] : $cup['year_start']."-".$cup['year_end'] ;
		if($y){
			$y = " (".$y.")";
		}
		$a = ($cup['archived'])? " - ARCHIVED" : "" ;
		$m = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_cup'.AMP.'cup='.$cup['id'].'"><strong>'.$cup['name'].$y.$a.'</strong></a>';
		$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_cup'.AMP.'cup='.$cup['id'].'">'.lang('edit').'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_cup'.AMP.'cup='.$cup['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($m, $e.", ".$d);
	}

	if(count($cups) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_cups'), 'colspan' => 2, 'class' => 'no_players'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_cup')?>
<?=form_submit(array('name' => 'add_cup', 'value' => lang('add_cup'), 'class' => 'submit'))?>
<?=form_close()?>