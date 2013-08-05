<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teammanager {
	
	function Teammanager(){
		
		$time_start = microtime(true);
		
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->EE->load->library('teammanager_lib');
		$this->settings = $this->EE->teammanager->__parse_settings();
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[init] Completed in $time seconds\n";
	}
	
	function teams(){
		
		$time_start = microtime(true);
		
		$id = $this->EE->TMPL->fetch_param('team_id');
		$fixtures_order = $this->EE->TMPL->fetch_param('fixtures_order');
		if($fixtures_order !== "desc") $fixtures_order = "asc";
		$fixtures_limit = $this->EE->TMPL->fetch_param('fixtures_limit');
		$results_order = $this->EE->TMPL->fetch_param('results_order');
		$results_limit = $this->EE->TMPL->fetch_param('results_limit');
		if($results_order !== "desc") $results_order = "asc";
		if($id){
			$id = str_replace("mine", $this->EE->teammanager->__get_my_team_id(), $id);
			// format id query
			$this->EE->teammanager->__format_query("id", $id);
		}
		// all teams
		$teams = $this->EE->teammanager->__get_teams($fixtures_order, $results_order, $fixtures_limit, $results_limit);
		$teams = $this->EE->teammanager->__set_keys("team", $teams);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[teams] Completed in $time seconds\n";
		
		return ($teams)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $teams) : "";
	}
	
	function squads(){
		
		$time_start = microtime(true);
		
		$id = $this->EE->TMPL->fetch_param('squad_id');
		if($id){
			// can only be single squad (no | or not )
			if(!array_key_exists($id, $this->settings['squad_type'])) return;
			$squads = array($this->settings['squad_type'][$id]);
		}else{
			// all teams
			$squads = $this->settings['squad_type'];
		}
		$squads = $this->EE->teammanager->__set_keys("squad", $squads);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[squads] Completed in $time seconds\n";
		
		return ($squads)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $squads) : "";
	}
	
	function staff(){
		
		$time_start = microtime(true);
		
		$id = $this->EE->TMPL->fetch_param('staff_id');
		$at_club = $this->EE->TMPL->fetch_param('at_club'); // default to true
		if($at_club !== "false") $at_club = true;
		if($id){
			// format id query
			$this->EE->teammanager->__format_query("id", $id);
		}
		// all teams
		$staff = $this->EE->teammanager->__get_players(true, $at_club);
		$staff = $this->EE->teammanager->__format_file("picture", $staff);
		$staff = $this->EE->teammanager->__set_keys("staff", $staff);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[staff] Completed in $time seconds\n";
		
		return ($staff)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $staff) : "";
	}
	
	function players(){
		
		$time_start = microtime(true);
		
		$id = $this->EE->TMPL->fetch_param('player_id');
		$squad = $this->EE->TMPL->fetch_param('squad');
		$at_club = $this->EE->TMPL->fetch_param('at_club'); // default to true
		if($at_club !== "false") $at_club = true;
		if($id){
			// format id query
			$this->EE->teammanager->__format_query("id", $id);
		}
		if($squad){
			// format squad id query
			$this->EE->teammanager->__format_query("squad", $squad);
		}
		$players = $this->EE->teammanager->__get_players(false, $at_club);
		$players = $this->EE->teammanager->__format_file("picture", $players);
		$players = $this->EE->teammanager->__set_keys("player", $players);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[players] Completed in $time seconds\n";
		
		return ($players)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $players) : "";
	}
	
	function leagues(){
		
		$time_start = microtime(true);
		
		$limit = $this->EE->TMPL->fetch_param('limit');
		$name = $this->EE->TMPL->fetch_param('league_name');
		$start_year = $this->EE->TMPL->fetch_param('start_year');
		$end_year = $this->EE->TMPL->fetch_param('end_year');
		$this->EE->teammanager->__format_query("archived", false);
		if($name){
			$this->EE->teammanager->__format_query("name", $name);
		}
		if($start_year){
			$this->EE->teammanager->__format_query("year_start", $start_year);
		}
		if($end_year){
			$this->EE->teammanager->__format_query("year_end", $end_year);
		}
		$leagues = $this->EE->teammanager->__get_leagues();
		// for each league, create a table
		$leagues = $this->EE->teammanager->__make_league_table($leagues);
		// set keys
		$leagues = $this->EE->teammanager->__set_keys("league", $leagues);
		// return
		if($limit) $leagues = array_slice($leagues, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[leagues] Completed in $time seconds\n";
		
		return ($leagues)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $leagues) : "";
	}
	
	function league_fixtures(){
		
		$time_start = microtime(true);
		
		$name = $this->EE->TMPL->fetch_param('league_name');
		$limit = $this->EE->TMPL->fetch_param('limit');
		$order = $this->EE->TMPL->fetch_param('order');
		$start_year = $this->EE->TMPL->fetch_param('start_year');
		$end_year = $this->EE->TMPL->fetch_param('end_year');
		if(!$order || ($order != "asc" && $order != "desc")) $order = "asc";
		if($name){
			$this->EE->teammanager->__format_query("name", $name);
		}
		if($start_year){
			$this->EE->teammanager->__format_query("year_start", $start_year);
		}
		if($end_year){
			$this->EE->teammanager->__format_query("year_end", $end_year);
		}
		$leagues = $this->EE->teammanager->__get_leagues();
		$fixtures = array();
		foreach($leagues as $league){
			$fixtures = $this->EE->teammanager->__get_fixtures("league", $order, $league['id']);
		}
		if($limit) $fixtures = array_slice($fixtures, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[league_fixtures] Completed in $time seconds\n";
		
		return ($fixtures)? $fixtures = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $fixtures) : "";		
	}
	
	function league_results(){
		
		$time_start = microtime(true);
		
		$name = $this->EE->TMPL->fetch_param('league_name');
		$limit = $this->EE->TMPL->fetch_param('limit');
		$order = $this->EE->TMPL->fetch_param('order');
		if(!$order || ($order != "asc" && $order != "desc")) $order = "desc";
		if($name){
			$this->EE->teammanager->__format_query("name", $name);
		}
		$leagues = $this->EE->teammanager->__get_leagues();
		$results = array();
		foreach($leagues as $league){
			$results = $this->EE->teammanager->__get_results("league", $order, $league['id']);
		}
		if($limit) $results = array_slice($results, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[league_results] Completed in $time seconds\n";
		
		return ($results)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $results) : "";
	}
	
	function cups(){
		
		$time_start = microtime(true);
		
		$limit = $this->EE->TMPL->fetch_param('limit');
		$name = $this->EE->TMPL->fetch_param('cup_name');
		$start_year = $this->EE->TMPL->fetch_param('start_year');
		$end_year = $this->EE->TMPL->fetch_param('end_year');
		$this->EE->teammanager->__format_query("archived", false);
		if($name){
			$this->EE->teammanager->__format_query("name", $name);
		}
		if($start_year){
			$this->EE->teammanager->__format_query("year_start", $start_year);
		}
		if($end_year){
			$this->EE->teammanager->__format_query("year_end", $end_year);
		}
		$cups = $this->EE->teammanager->__get_cups();
		// set keys
		$cups = $this->EE->teammanager->__set_keys("cup", $cups);
		// return
		if($cups) $cups = array_slice($cups, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[cups] Completed in $time seconds\n";
		
		return ($cups)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $cups) : "";
	}
	
	function friendly_fixtures(){
		
		$time_start = microtime(true);
		
		$limit = $this->EE->TMPL->fetch_param('limit');
		$order = $this->EE->TMPL->fetch_param('order');
		if(!$order || ($order != "asc" && $order != "desc")) $order = "asc";
		$fixtures = $this->EE->teammanager->__get_fixtures("friendly", $order);
		if($limit) $fixtures = array_slice($fixtures, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[friendly_fixtures] Completed in $time seconds\n";
		
		return ($fixtures)? $fixtures = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $fixtures) : "";
	}
	
	function friendly_results(){
		
		$time_start = microtime(true);
		
		$limit = $this->EE->TMPL->fetch_param('limit');
		$order = $this->EE->TMPL->fetch_param('order');
		if(!$order || ($order != "asc" && $order != "desc")) $order = "desc";
		$results = $this->EE->teammanager->__get_results("friendly", $order);
		if($limit) $results = array_slice($results, 0, $limit, true);
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[friendly_results] Completed in $time seconds\n";
		
		return ($results)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $results) : "";
	}
	
	function matches(){
		
		$time_start = microtime(true);
		
		$id = $this->EE->TMPL->fetch_param('match_id');
		$match = array($this->EE->teammanager->__get_match($id));
		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(array_key_exists("debug",$_GET) && $_GET['debug']) echo "[matches] Completed in $time seconds\n";
		
		return ($match)? $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $match) : "";	
	}
	
}