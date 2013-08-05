<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_cup'.AMP.'cup='.$cup_id)?>


<p><label for="name"><?=lang('name')?>*:</label><br />
<?=form_input("name", $name, 'id="name" style="width: 200px"')?></p>


<p><label for="year_start"><?=lang('year_start')?>:</label><br />
<?=form_input("year_start", $year_start, 'id="year_start" style="width: 45px" placeholder="'.date("Y").'"')?></p>


<p><label for="year_end"><?=lang('year_end')?>:</label><br />
<?=form_input("year_end", $year_end, 'id="year_end" style="width: 45px" placeholder="'.(date("Y")+1).'"')?></p>


<p><label for="archived"><?=lang('archived')?>:</label><br />
<?=form_checkbox("archived", "archived", (bool)$archived, 'id="archived"')?></p>


<?=form_submit(array('name' => 'submit', 'value' => lang('edit_cup'), 'class' => 'submit'))?>
<?=form_close()?>