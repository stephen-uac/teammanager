<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open_multipart('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_player'.AMP.'player='.$player_id)?>

<p><label for="player_name"><?=lang('player_name')?>*:</label><br />
<?=form_input("player_name", $name, 'id="player_name" style="width: 200px"')?></p>


<p><label for="is_player"><?=lang('is_player')?>:</label><br />
<?php 
$data = array(
    'name'        => 'is_player',
    'id'          => 'is_player',
    'value'       => 'is_player',
    'checked'     => $is_player
    );
echo form_checkbox($data);
?></p>


<p><label for="squad"><?=lang('squad')?>:</label><br />
<?php

$options = array();
$d = $squad;

foreach($settings["squad_type"] as $squad){
	$options[$squad["id"]] = $squad["value"];
}

echo form_dropdown('squad', $options, $d, 'id="squad"');

?>


<?php $v = ($is_player)? : " display: none;" ; ?>
<p id="squad_number_field" style="<?php echo $v; ?>"><label for="squad_number"><?=lang('squad_number')?>:</label><br />
<?=form_input("squad_number", $squad_number, 'id="squad_number" style="width: 100px;"')?></p>


<p><label for="position"><?=lang('player_position')?>*:</label><br />
<?=form_input("position", $position, 'id="position" style="width: 200px"')?></p>


<p><label for="dob"><?=lang('dob')?>:</label><br />
<?=form_input("dob", $dob, 'id="dob" style="width: 200px" placeholder="dd/mm/yyyy"')?></p>

<div>
	<p><label for="photograph"><?=lang('photograph')?>:</label><br />
	<?=$file_field?></p>
</div>

<p><label for="profile"><?=lang('profile')?>:</label><br />
<?=form_textarea("profile", $profile, 'id="profile" style="width: 400px"')?></p>


<p><label for="sponsor"><?=lang('sponsor')?>:</label><br />
<?=form_input("sponsor", $sponsor, 'id="sponsor" style="width: 200px"')?></p>

<div>
	<p><label for="sponsor_logo"><?=lang('sponsor_logo')?>:</label><br />
	<?=$sponsor_file_field?></p>
</div>


<p><label for="sponsor_link"><?=lang('sponsor_link')?>:</label><br />
<?=form_input("sponsor_link", $sponsor_link, 'id="sponsor_link" style="width: 400px"')?></p>


<p><label for="at_club"><?=lang('at_club')?>:</label><br />
<?php 
$data = array(
    'name'        => 'at_club',
    'id'          => 'at_club',
    'value'       => 'at_club',
    'checked'     => $at_club
    );
echo form_checkbox($data);
?></p>


<br /><br />
<?=form_submit(array('name' => 'submit', 'value' => lang('edit_player'), 'class' => 'submit', 'onSubmit' => 'alert(test");'))?>
<?=form_close()?>