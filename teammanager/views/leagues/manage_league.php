<h2><?=$league_name?></h2>

<div class="spacer"></div>

<h3><?=lang('teams_in_league')?></h3>

<?php

	// teams

	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('teams_in_league'), lang('actions'));

	foreach($teams as $team){
		if($team['managed']){
			$t = "<strong>".$team['name']."</strong>";
		}else{
			$t = $team['name'];
		}
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_team_from_league'.AMP.'league='.$league_id.AMP.'team='.$team['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($t, $d);
	}

	if(count($teams) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_teams_in_league'), 'colspan' => 2, 'class' => 'no_teams_in_league'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_team_to_league'.AMP.'league='.$league_id)?>
<p><?=lang('team_to_add')?>: <?php

$options = array();

$d = "";

foreach($exc_teams as $team){
	if(!$d) $d = $team['id'];
	$options[$team["id"]] = $team["name"];
}

echo form_dropdown('team', $options, $d, 'id="team"');

?></p>
<p><?=form_submit(array('name' => 'add_team_to_league', 'value' => lang('add_team_to_league'), 'class' => 'submit'))?></p>
<?=form_close()?>


<div class="spacer"></div>

<h3><?=lang('matches_in_league')?></h3>

<?php

	// matches

	$this->table->set_template($cp_table_template);
	//$this->table->set_heading(lang('date'), lang('matches_in_league'), lang('pre_match'), lang('post_match'), lang('match_stats'), lang('actions'));
	$this->table->set_heading(lang('date'), lang('matches_in_league'), lang('post_match'), lang('actions'));

	foreach($matches as $match){
		$date = date("d/m/Y", $match['kick_off']);
		$t = $match['home_team_name']." ".lang('versus')." ".$match['away_team_name'];
		$pre = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=pre_match'.AMP.'match='.$match['id'].AMP.'league='.$league_id.'">'.lang('pre_match').'</a>';
		$post = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=post_match'.AMP.'match='.$match['id'].AMP.'league='.$league_id.'">'.lang('post_match').'</a>';
		$stats = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=match_stats'.AMP.'match='.$match['id'].AMP.'league='.$league_id.'">'.lang('match_stats').'</a>';
		$e = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_match'.AMP.'match='.$match['id'].AMP.'league='.$league_id.'">'.lang('edit').'</a>';
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_match'.AMP.'match='.$match['id'].AMP.'league='.$league_id.'">'.lang('delete').'</a>';
		//$this->table->add_row($date, $t, $pre, $post, $stats, $e.", ".$d);
		$this->table->add_row($date, $t, $post, $e.", ".$d);
	}

	if(count($matches) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_matches_in_league'), 'colspan' => 6, 'class' => 'no_matches_in_league'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_match'.AMP.'league='.$league_id)?>
<p><?=form_submit(array('name' => 'add_match_to_league', 'value' => lang('add_match_to_league'), 'class' => 'submit'))?></p>
<?=form_close()?>


<div class="spacer"></div>

<h3><?=lang('deductions')?></h3>

<?php

	// deductions

	$this->table->set_template($cp_table_template);
	$this->table->set_heading(lang('team'), lang('deduction'), lang('actions'));

	foreach($deds as $ded){
		$t = $ded['team'];
		$p = $ded['points'];
		$d = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_deduction_from_league'.AMP.'league='.$league_id.AMP.'deduction='.$ded['id'].'">'.lang('delete').'</a>';
		$this->table->add_row($t, $p, $d);
	}

	if(count($deds) == 0){
		// no rows
		$this->table->add_row(array('data' => lang('no_deductions'), 'colspan' => 3, 'class' => 'no_deductions'));
	}
	
echo $this->table->generate();

?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_deduction_to_league'.AMP.'league='.$league_id)?>
<p><?=lang('team_to_deduct')?>: <?php

$options = array();

$d = "";

foreach($teams as $team){
	if(!$d) $d = $team['id'];
	$options[$team["id"]] = $team["name"];
}

echo form_dropdown('team', $options, $d, 'id="team"');

?></p>
<p><?=lang('points_to_deduct')?>: <?=form_input("points", $points, 'id="points" style="width: 30px" placeholder="10"')?></p>
<p><?=form_submit(array('name' => 'add_deduction_to_league', 'value' => lang('add_deduction_to_league'), 'class' => 'submit'))?></p>
<?=form_close()?>