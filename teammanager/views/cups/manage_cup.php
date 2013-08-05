<h2><?=$cup_name?></h2>

<div class="spacer"></div>

<h3><?=lang('cup_rounds')?></h3>

<?php

	// teams

	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('round_name'), lang('actions'));

	foreach($rounds as $round){
		$r = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_round'.AMP.'cup='.$cup_id.AMP.'round='.$round['id'].'">'.$round['name'].'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_round_from_cup'.AMP.'cup='.$cup_id.AMP.'round='.$round['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($r, $d);
	}

	if(count($rounds) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_rounds_in_cup'), 'colspan' => 2, 'class' => 'no_rounds_in_cup'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_round_to_cup'.AMP.'cup='.$cup_id)?>
<p><?=lang('add_round_name')?>: <?=form_input('name', "", 'id="name" placeholder="'.lang('semi_finals').'" style="width: 200px;"');?></p>
<p><?=form_submit(array('name' => 'add_round_to_cup', 'value' => lang('add_round_to_cup'), 'class' => 'submit'))?></p>
<?=form_close()?>