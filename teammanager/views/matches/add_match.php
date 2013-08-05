<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open($form_url)?>

<p><label for="home_team"><?=lang('home_team')?>*:</label><br />
<?php
$options = array();

$d = $home_team;

foreach($teams as $team){
	if(!$d) $d = $team['id'];
	$options[$team["id"]] = $team["name"];
}

echo form_dropdown('home_team', $options, $d, 'id="home_team"');

?> <?php

$options = array();
$d = $home_team_squad;

foreach($settings["squad_type"] as $squad){
	if($d == "") $d = $squad["id"];
	$options[$squad["id"]] = $squad["value"];
}

echo form_dropdown('home_team_squad', $options, $d, 'id="home_team_squad"');

?></p>


<p><label for="away_team"><?=lang('away_team')?>*:</label><br />
<?php
$options = array();

$d = $away_team;

foreach($teams as $team){
	if(!$d) $d = $team['id'];
	$options[$team["id"]] = $team["name"];
}

echo form_dropdown('away_team', $options, $d, 'id="away_team"');

?> <?php

$options = array();
$d = $away_team_squad;

foreach($settings["squad_type"] as $squad){
	if($d == "") $d = $squad["id"];
	$options[$squad["id"]] = $squad["value"];
}

echo form_dropdown('away_team_squad', $options, $d, 'id="away_team_squad"');

?></p>


<p><label for="kick_off"><?=lang('kick_off')?>*:</label><br />
<?=form_input("kick_off", $kick_off, 'id="kick_off" style="width: 200px" placeholder="dd/mm/yyyy hh:mm:ss"')?></p>
<?php // TODO: date picker here ?>


<p><label for="venue"><?=lang('venue')?>:</label><br />
<?=form_input("venue", $venue, 'id="venue" style="width: 200px"')?></p>

<?=form_submit(array('name' => 'submit', 'value' => lang('add_match'), 'class' => 'submit'))?>
<?=form_close()?>