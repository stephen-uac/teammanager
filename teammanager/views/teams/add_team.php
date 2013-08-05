<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_team')?>

<label for="team_name"><?=lang('team_name')?>:</label><br />
<?=form_input("team_name", "", 'id="team_name" style="width: 200px"')?>
<br /><br />
<?=form_submit(array('name' => 'submit', 'value' => lang('add_team'), 'class' => 'submit'))?>
<?=form_close()?>