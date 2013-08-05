<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_league')?>


<p><label for="name"><?=lang('name')?>*:</label><br />
<?=form_input("name", $name, 'id="name" style="width: 200px"')?></p>


<p><label for="year_start"><?=lang('year_start')?>:</label><br />
<?=form_input("year_start", $year_start, 'id="year_start" style="width: 45px" placeholder="'.date("Y").'"')?></p>


<p><label for="year_end"><?=lang('year_end')?>:</label><br />
<?=form_input("year_end", $year_end, 'id="year_end" style="width: 45px" placeholder="'.(date("Y")+1).'"')?></p>


<p><label for="promotion_places"><?=lang('promotion_places')?>:</label><br />
<?=form_input("promotion_places", $promotion_places, 'id="promotion_places" style="width: 30px" placeholder="1"')?></p>


<p><label for="promotion_playoff_places"><?=lang('promotion_playoff_places')?>:</label><br />
<?=form_input("promotion_playoff_places", $promotion_playoff_places, 'id="promotion_playoff_places" style="width: 30px" placeholder="0"')?></p>


<p><label for="relegation_places"><?=lang('relegation_places')?>:</label><br />
<?=form_input("relegation_places", $relegation_places, 'id="relegation_places" style="width: 30px" placeholder="1"')?></p>


<p><label for="relegation_playoff_places"><?=lang('relegation_playoff_places')?>:</label><br />
<?=form_input("relegation_playoff_places", $relegation_playoff_places, 'id="relegation_playoff_places" style="width: 30px" placeholder="0"')?></p>



<?=form_submit(array('name' => 'submit', 'value' => lang('add_league'), 'class' => 'submit'))?>
<?=form_close()?>