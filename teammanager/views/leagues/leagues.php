<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('league_name'), lang('actions'));

	foreach($leagues as $league){
		$y = ($league['year_start'] == $league['year_end'])? $league['year_start'] : $league['year_start']."-".$league['year_end'] ;
		if($y){
			$y = " (".$y.")";
		}
		$a = ($league['archived'])? " - ARCHIVED" : "" ;
		$m = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.AMP.'league='.$league['id'].'"><strong>'.$league['name'].$y.$a.'</strong></a>';
		$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_league'.AMP.'league='.$league['id'].'">'.lang('edit').'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_league'.AMP.'league='.$league['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($m, $e.", ".$d);
	}

	if(count($leagues) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_leagues'), 'colspan' => 2, 'class' => 'no_leagues'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_league')?>
<?=form_submit(array('name' => 'add_league', 'value' => lang('add_league'), 'class' => 'submit'))?>
<?=form_close()?>