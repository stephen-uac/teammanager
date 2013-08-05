<?php if($error){
?><p class="notice"><?=$error?></p><?php
}
?>

<?=form_open($form_url)?>

<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading("", $home_team_name, $away_team_name);
?>

<?php $l = lang("match_played"); ?>
<?php $f1 = form_checkbox("played", "played", (bool)$played, 'id="played"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>

<?php $l = lang("half_time_score"); ?>
<?php $f1 = form_input("home_half_time_score", $home_half_time_score, 'id="home_half_time_score" style="width: 35px" placeholder="0"')?>
<?php $f2 = form_input("away_half_time_score", $away_half_time_score, 'id="away_half_time_score" style="width: 35px" placeholder="0"')?>

<?php
$this->table->add_row($l, $f1, $f2);
?>

<?php $l = lang("full_time_score"); ?>
<?php $f1 = form_input("home_full_time_score", $home_full_time_score, 'id="home_full_time_score" style="width: 35px" placeholder="0"')?>
<?php $f2 = form_input("away_full_time_score", $away_full_time_score, 'id="away_full_time_score" style="width: 35px" placeholder="0"')?>

<?php
$this->table->add_row($l, $f1, $f2);
?>

<?php $l = lang("aet"); ?>
<?php $f1 = form_checkbox("aet", "aet", (bool)$aet, 'id="aet"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>

<?php $l = lang("penalties"); ?>
<?php $f1 = form_checkbox("penalties", "penalties", (bool)$penalties, 'id="penalties"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>

<?php $l = lang("penalty_score"); ?>
<?php $f1 = form_input("home_penalty_score", $home_penalty_score, 'id="home_penalty_score" style="width: 35px" placeholder="0"')?>
<?php $f2 = form_input("away_penalty_score", $away_penalty_score, 'id="away_penalty_score" style="width: 35px" placeholder="0"')?>

<?php
$this->table->add_row($l, $f1, $f2);
?>

<?php $l = lang("officials"); ?>
<?php $f1 = form_textarea("officials", $officials, 'id="officials" style="width: 400px; height:120px;"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>

<?php $l = lang("attendance"); ?>
<?php $f1 = form_input("attendance", $attendance, 'id="attendance" style="width: 100px;" placeholder="1234"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>

<?php $l = lang("report"); ?>
<?php $f1 = form_textarea("report", $report, 'id="report"')?>

<?php
$this->table->add_row($l, array("data"=>$f1,"colspan"=>"2"));
?>


<?=$this->table->generate();?>

<?=form_submit(array('name' => 'submit', 'value' => lang('update'), 'class' => 'submit'))?>
<?=form_close()?>