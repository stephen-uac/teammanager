<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teammanager_lib{
	
	public function __construct(){
		$this->EE =& get_instance();
		//set a global object
		$this->EE->teammanager = $this;
	}
	
	// settings
	
	function __parse_settings(){
		$settings = array();
		
		// get all from db and return them
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_settings');

		foreach($query->result_array() as $row)
		{
			$settings[$row['name']][$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'value' => $row['value']);
		}
		$query->free_result();
		return $settings;
	}
	
	
	function __get_setting_value($setting_name){
		$settings = array();
		
		// get all from db and return them
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_settings');

		foreach($query->result_array() as $row)
		{
			if($setting_name == $row['name']) return $row['value'];
		}
		return "";
	}
	
	// utility functions
	
	public function __format_date($date){
		list($d, $m, $y, $h, $t, $s) = array(0, 0, 0, 0, 0, 0);
		if(strpos($date, " ") > 0){
			list($date, $time) = explode(" ", $date);
			if(strpos($date, "/") > 0){
				list($d, $m, $y) = explode("/", $date);
			}
			if(strpos($time, ":") > 0){
				list($h, $t, $s) = explode(":", $time);
			}
			$date = date('Y-m-d H:i:s', mktime($h, $t, $s, $m, $d, $y));
		}else{
			if(strpos($date, "/") > 0){
				list($d, $m, $y) = explode("/", $date);
			}
			$date = date('Y-m-d H:i:s', mktime(0, 0, 0, $m, $d, $y));
		}
		return $date;
	}
	
	public function __format_query($field, $values){
		$a = "";
		$o = "";
		$vs = explode("|", $values);
		foreach($vs as $v){
			if(strpos($v, "not") !== FALSE){
				$v = substr($v, 4);
				$a .= "AND ".$field." != '".$v."' ";
			}else{
				$o .= "OR ".$field." = '".$v."' ";
			}
		}
		$a = ($a)? "(1=1 ".$a.")" : "";
		$o = ($o)? "(1=0 ".$o.")" : "";
		if($a) $this->EE->db->where($a);
		if($o) $this->EE->db->where($o);
	}
	
	public function __format_file($field, $array){
		$this->EE->load->library('file_field');
		$r = array();
		foreach($array as $rowk => $rowv){
			$t = $rowv;
			$t[$field] = $this->EE->file_field->parse_field($this->EE->file_field->format_data($rowv[$field."_file"], $rowv[$field."_dir"]));
			$t[$field] = $t[$field]['url'];
			$r[] = $t;
		}
		return $r;
	}
	
	public function __set_keys($field, $array){
		$r = array();
		foreach($array as $rowk => $rowv){
			$t = array();
			foreach($rowv as $k=>$v){
				$k = $field."_".$k;
				$t[$k] = $v;
			}
			$r[] = $t;
		}
		return $r;
	}
	
	public function __make_league_table($array){
		$r = array();
		foreach($array as $rowk => $rowv){
			$t = $rowv;
			$t["table"] = $this->__make_league_table_output($rowv["id"]);
			$r[] = $t;
		}
		return $r;
	}
	
	public function __make_league_table_output($id){
		// get settings
		$this->settings = $this->__parse_settings();
		// get all teams
		$teams = array();
		$this->EE->db->where('league', $id);
		$query = $this->EE->db->get('tm_league_teams');
		foreach($query->result_array() as $t){
			$team = $this->__get_team($t['team'], 0, 0, false);
			
			$team['played'] = 0;
			$team['won'] = 0;
			$team['drawn'] = 0;
			$team['lost'] = 0;
			$team['goals_for'] = 0;
			$team['goals_against'] = 0;
			$team['goal_diff'] = 0;
			$team['points'] = 0;
			
			// get only games played
			$this->EE->db->where('played', true);
			$this->EE->db->where('league', $id);
			$this->EE->db->where('postponed', 0);
			$this->EE->db->where("(home_team = '".$t['team']."' OR away_team = '".$t['team']."')");
            $this->EE->db->join("tm_matches", "tm_league_matches.match = tm_matches.id");
			$matches = $this->EE->db->get('tm_league_matches');
			
			foreach($matches->result_array() as $match){
				
				if($match['home_team'] == $t['team']){
					// its the home team
					$team['goals_for'] += $match['home_full_time_score'];
					$team['goals_against'] += $match['away_full_time_score'];
					
					$win = ($match['home_full_time_score'] > $match['away_full_time_score']) || ($match['home_penalty_score'] > $match['away_penalty_score']);;
					$draw = ($match['home_full_time_score'] == $match['away_full_time_score']) && ($match['home_penalty_score'] == $match['away_penalty_score']);
					$loss = ($match['home_full_time_score'] < $match['away_full_time_score']) || ($match['home_penalty_score'] < $match['away_penalty_score']);;
						
				}elseif($match['away_team'] == $t['team']){
					// its the away team
					$team['goals_for'] += $match['away_full_time_score'];
					$team['goals_against'] += $match['home_full_time_score'];
					
					$win = ($match['away_full_time_score'] > $match['home_full_time_score']) || ($match['away_penalty_score'] > $match['home_penalty_score']);
					$draw = ($match['away_full_time_score'] == $match['home_full_time_score']) && ($match['away_penalty_score'] == $match['home_penalty_score']);
					$loss = ($match['away_full_time_score'] < $match['home_full_time_score']) ||  ($match['away_penalty_score'] < $match['home_penalty_score']);
					
				}else{
					continue;
				}
				
				$team['played']++;
				
				if($win){
					$team['won']++;
					$team['points'] += (int)$this->__get_setting_value('points_per_win');
				}elseif($draw){
					$team['drawn']++;
					$team['points'] += (int)$this->__get_setting_value('points_per_draw');
				}else{
					$team['lost']++;
					$team['points'] += (int)$this->__get_setting_value('points_per_loss');
				}
				
			}
			
			// demo data
			$team['goal_diff'] = $team['goals_for'] - $team['goals_against'];
			$teams[] = $team;
		}
		
		// Sort the multidimensional array
		usort($teams, array($this,"__team_sort"));
		
		// draw table
		$this->EE->load->library('table');
		
		$this->EE->lang->loadfile('teammanager');
		
		$cp_table_template['table_open'] = '<table border="0" cellpadding="4" cellspacing="0" class="league" id="league-'.$id.'">';
		
		$this->EE->table->set_template($cp_table_template);
		
		$this->EE->table->set_heading($this->EE->lang->line('team'), $this->EE->lang->line('played'), $this->EE->lang->line('won'), $this->EE->lang->line('drawn'), $this->EE->lang->line('lost'), $this->EE->lang->line('goals_for'), $this->EE->lang->line('goals_against'), $this->EE->lang->line('goal_diff'), $this->EE->lang->line('points'));
		
		$place = 1;
		$league = $this->__get_league($id);
		
		
		foreach($teams as $team){
			$class = "";
			$prom = $league['promotion_places'];
			$prom_pl = $prom+$league['promotion_playoff_places'];
			$rel = count($teams) - ($league['relegation_places']-1);
			$rel_pl =  $rel - $league['relegation_playoff_places'];
			// top of table
			if($place == 1){
				$class .= "champion-spot ";
			}
			if($place <= $prom){
				$class .= "promotion-place ";
			}
			if($place > $prom && $place <= $prom_pl){
				$class .= "promotion-playoff-place ";
			}
			// bottom of table
			if($place >= $rel){
				$class .= "relegation-place ";
			}
			if($place < $rel && $place >= $rel_pl){
				$class .= "relegation-playoff-place ";
			}
			// out team
			if($team['is_managed_team'] == true){
				$class .= "my-team ";
			}
			
			$data = array(
				array("data"=>$team['name'], "class"=>$class),
				array("data"=>$team['played'], "class"=>$class),
				array("data"=>$team['won'], "class"=>$class),
				array("data"=>$team['drawn'], "class"=>$class),
				array("data"=>$team['lost'], "class"=>$class),
				array("data"=>$team['goals_for'], "class"=>$class),
				array("data"=>$team['goals_against'], "class"=>$class),
				array("data"=>$team['goal_diff'], "class"=>$class),
				array("data"=>$team['points'], "class"=>$class));
			$this->EE->table->add_row($data);
			$place++;
		}
		
		return $this->EE->table->generate();
	}
	
	function __team_sort($a,$b) {
		if($a['points'] == $b['points']){
			if($a['goal_diff'] == $b['goal_diff']){
				if($a['goals_for'] == $b['goals_for']){
					if($a['goals_against'] == $b['goals_against']){
						return $a['name']>$b['name'];
					}
					return $a['goals_against']<$b['goals_against'];
				}
				return $a['goals_for']<$b['goals_for'];
			}
			return $a['goal_diff']<$b['goal_diff'];
		}
		return $a['points']<$b['points'];
	}
	
	function __match_sort_asc($a,$b) {
		return $a['kick_off']>$b['kick_off'];
	}
	
	function __match_sort_desc($a,$b) {
		return $a['kick_off']<$b['kick_off'];
	}
	
	
	// get from db
	
	public function __get_players($staff = false, $at_club = true){
		$this->EE->db->where('is_player', (int)!$staff);
		$this->EE->db->where('at_club', (int)$at_club);
		$query = $this->EE->db->get('tm_players');
		$teams = $query->result_array();
		return $teams;
	}
	
	public function __get_teams($fixtures_order = "asc", $results_order = "desc", $fixtures_limit, $results_limit){
		$query = $this->EE->db->get('tm_teams');
		$teams = $query->result_array();
		
		foreach($teams as &$team){
			$team = $this->__get_team($team['id'], $fixtures_order, $results_order);
			// slice arrays here
			if($fixtures_limit) $team['league_fixtures'] = array_slice($team['league_fixtures'], 0, $fixtures_limit, true);
			// league results
			if($results_limit) $team['league_results'] = array_slice($team['league_results'], 0, $results_limit, true);
			// cup fixtures
			if($fixtures_limit) $team['cup_fixtures'] = array_slice($team['cup_fixtures'], 0, $fixtures_limit, true);
			// cup results
			if($results_limit) $team['cup_results'] = array_slice($team['cup_results'], 0, $results_limit, true);
			// friendly fixtures
			if($fixtures_limit) $team['friendly_fixtures'] = array_slice($team['friendly_fixtures'], 0, $fixtures_limit, true);
			// friendly results
			if($results_limit) $team['friendly_results'] = array_slice($team['friendly_results'], 0, $results_limit, true);
			
		}
		return $teams;
	}
	
	public function __get_team($id, $fixtures_order = "asc", $results_order = "desc", $get_all = true){
		$this->EE->db->where('id', $id);
		$query = $this->EE->db->get('tm_teams');
		$team = $query->result_array();
		$team = $team[0];
		if($get_all){
			// league fixtures
			$team['league_fixtures'] = $this->__get_fixtures("league", $fixtures_order, 0, 0, $team['id']);
			// league results
			$team['league_results'] = $this->__get_results("league", $results_order, 0, 0, $team['id']);
			// cup fixtures
			$team['cup_fixtures'] = $this->__get_fixtures("cup", $fixtures_order, 0, 0, $team['id']);
			// cup results
			$team['cup_results'] = $this->__get_results("cup", $results_order, 0, 0, $team['id']);
			// friendly fixtures
			$team['friendly_fixtures'] = $this->__get_fixtures("friendly", $fixtures_order, 0, 0, $team['id']);
			// friendly results
			$team['friendly_results'] = $this->__get_results("friendly", $results_order, 0, 0, $team['id']);
		}
		return $team;
	}
	
	public function __get_my_team_id(){
		$this->EE->db->where('is_managed_team', "1");
		$query = $this->EE->db->get('tm_teams');
		$team = $query->result_array();
		return $team[0]['id'];
	}
	
	public function __get_match($id){
		$this->settings = $this->__parse_settings();
		$this->EE->db->where('id', $id);
		$query = $this->EE->db->get('tm_matches');
		$match = $query->result_array();
		$match[0]['match_id'] = $id;
		$t = $this->__get_team($match[0]['home_team'], 0, 0, false);
		$match[0]['home_team_name'] = $t['name'];
		$t = $this->__get_team($match[0]['away_team'], 0, 0, false);
		$match[0]['away_team_name'] = $t['name'];
		$match[0]['kick_off'] = strtotime($match[0]['kick_off']);
		$match[0]['home_half_time_score'] = (int)$match[0]['home_half_time_score'];
		$match[0]['away_half_time_score'] = (int)$match[0]['away_half_time_score'];
		$match[0]['home_full_time_score'] = (int)$match[0]['home_full_time_score'];
		$match[0]['away_full_time_score'] = (int)$match[0]['away_full_time_score'];
		$match[0]['home_penalty_score'] = (int)$match[0]['home_penalty_score'];
		$match[0]['away_penalty_score'] = (int)$match[0]['away_penalty_score'];
		$match[0]['home_squad_name'] = $this->settings['squad_type'][$match[0]['home_squad']]['value'];
		$match[0]['away_squad_name'] = $this->settings['squad_type'][$match[0]['away_squad']]['value'];
		$match[0]['penalties'] = (bool)$match[0]['penalties'];
		$match[0]['aet'] = (bool)$match[0]['aet'];
		$match[0]['venue'] = $match[0]['venue'];
		$match[0]['report'] = $match[0]['report'];
		$match[0]['postponed'] = $match[0]['postponed'];
		$match[0]['played'] = $match[0]['played'];
		// get stats for match
		$match[0]['home_stats'] = array();
		$match[0]['away_stats'] = array();
		// return info
		return $match[0];
	}
	
	public function __get_leagues(){
		$query = $this->EE->db->get('tm_leagues');
		$leagues = $query->result_array();
		return $leagues;
	}
	
	public function __get_league($id){
		$this->EE->db->where('id', $id);
		$query = $this->EE->db->get('tm_leagues');
		$league = $query->result_array();
		return $league[0];
	}
	
	public function __get_cups(){
		$query = $this->EE->db->get('tm_cups');
		$cups = $query->result_array();
		foreach($cups as &$cup){
			$cup = $this->__get_cup($cup['id']);
		}
		return $cups;
	}
	
	public function __get_cup($id){
		$this->EE->db->where('id', $id);
		$query = $this->EE->db->get('tm_cups');
		$cup = $query->result_array();
		// get rounds
		$this->EE->db->where('cup', $id);
		$query = $this->EE->db->get('tm_cup_rounds');
		foreach($query->result_array() as $round){
			$r = $this->__get_round($round['id']);
			$cup[0]['rounds'][] = $r;
		}
		if(array_key_exists('rounds', $cup[0])){
			$cup[0]['rounds'] = $this->__set_keys("round", $cup[0]['rounds']);
		}else{
			$cup[0]['rounds'] = array();
		}
		return $cup[0];
	}
	
	public function __get_round($id, $order="asc"){
		$this->EE->db->where('id', $id);
		$query = $this->EE->db->get('tm_cup_rounds');
		$round = $query->result_array();
		$round[0]['fixtures'] = $this->__get_fixtures("cup", $order, $round[0]['cup'], $id);
		$round[0]['results'] = $this->__get_results("cup", $order, $round[0]['cup'], $id);
		return $round[0];
	}
	
	public function __get_fixtures($type="friendly_matches", $order="asc", $id=0, $round_id=0, $team_id=0){
		
		$r = array();
		
		if($type=="league"){
			// league
			$this->EE->db->where('played', false);
			if($id) $this->EE->db->where('league', $id);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
            $this->EE->db->join("tm_matches", "tm_league_matches.match = tm_matches.id");
			$matches = $this->EE->db->get('tm_league_matches');
			foreach($matches->result_array() as $match){
				// a game we want
				$r[] = $this->__get_match($match['id']);
			}
			
		}elseif($type=="cup"){
			// cup
			$this->EE->db->where('played', false);
			if($id) $this->EE->db->where('cup', $id);
			if($round_id) $this->EE->db->where('round', $round_id);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
            $this->EE->db->join("tm_matches", "tm_cup_matches.match = tm_matches.id");
			$matches = $this->EE->db->get('tm_cup_matches');
			foreach($matches->result_array() as $match){
				// a game we want
				$r[] = $this->__get_match($match['id']);
			}

		}elseif($type="friendly"){
			// friendly
			$this->EE->db->where('played', false);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
			$matches = $this->EE->db->get('tm_matches');
			foreach($matches->result_array() as $match){
				$this->EE->db->where('match', $match['id']);
				$l_query = $this->EE->db->get('tm_league_matches');
				$this->EE->db->where('match', $match['id']);
				$c_query = $this->EE->db->get('tm_cup_matches');
				// if the match isnt a cup of league match...
				if ($l_query->num_rows() == 0 && $c_query->num_rows() == 0){
					// a game we want
					$r[] = $this->__get_match($match['id']);
					
				}
			}
		}
		usort($r, array($this,"__match_sort_".$order));
		return $r;
	}
	
	public function __get_results($type="friendly_matches", $order="asc",$id=0, $round_id=0, $team_id=0){
		
		$r = array();
		
		if($type=="league"){
			// league
			$this->EE->db->where('played', true);
			if($id) $this->EE->db->where('league', $id);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
            $this->EE->db->join("tm_matches", "tm_league_matches.match = tm_matches.id");
			$matches = $this->EE->db->get('tm_league_matches');
			foreach($matches->result_array() as $match){
				// a game we want
				$r[] = $this->__get_match($match['id']);
			}
			
		}elseif($type=="cup"){
			// cup
			$this->EE->db->where('played', true);
			if($id) $this->EE->db->where('cup', $id);
			if($round_id) $this->EE->db->where('round', $round_id);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
            $this->EE->db->join("tm_matches", "tm_cup_matches.match = tm_matches.id");
			$matches = $this->EE->db->get('tm_cup_matches'); 
			foreach($matches->result_array() as $match){
				// a game we want
				$r[] = $this->__get_match($match['id']);
			}

		}elseif($type="friendly"){
			// friendly
			$this->EE->db->where('played', true);
			if($team_id) $this->EE->db->where("(home_team = '".$team_id."' OR away_team = '".$team_id."')");
			$matches = $this->EE->db->get('tm_matches');
			foreach($matches->result_array() as $match){
				$this->EE->db->where('match', $match['id']);
				$l_query = $this->EE->db->get('tm_league_matches');
				$this->EE->db->where('match', $match['id']);
				$c_query = $this->EE->db->get('tm_cup_matches');
				// if the match isnt a cup of league match...
				if ($l_query->num_rows() == 0 && $c_query->num_rows() == 0){
					// a game we want
					$r[] = $this->__get_match($match['id']);
				}
			}
		}
		usort($r, array($this,"__match_sort_".$order));
		return $r;
	}
	
}