<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teammanager_upd {

	var $version = '1.0.5';

	function __construct(){
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->EE->load->library('teammanager_lib');
	}
	
	function install(){
	
		// load up database, insert module info
		
		$this->EE->load->dbforge();
	
		$data = array(
			'module_name' => 'Teammanager' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
		
		$this->EE->db->insert('modules', $data);
		
		// create player table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'varchar', 'constraint' => '250'),
			'position' => array('type' => 'varchar', 'constraint' => '250'),
			'dob' => array('type' => 'date'),
			'picture_file' => array('type' => 'text'),
			'picture_dir' => array('type' => 'int', 'constraint' => '10'),
			'sponsor' => array('type' => 'varchar', 'constraint' => '250'),
			'sponsor_link' => array('type' => 'varchar', 'constraint' => '500'),
			'is_player' => array('type' => 'boolean', 'default' => 1),
			'profile' => array('type' => 'text'),
			'squad' => array('type' => 'int', 'constraint' => '10'),
			'squad_number' => array('type' => 'varchar', 'constraint' => '3'),
			'at_club' => array('type' => 'boolean', 'default' => 1),
			'sponsor_file' => array('type' => 'text'),
			'sponsor_dir' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_players');
		
		unset($fields);
		
		// create teams table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'varchar', 'constraint' => '250'),
			'is_managed_team' => array('type' => 'boolean', 'default' => 0)
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_teams');
		
		unset($fields);
		
		$data = array(
		    'name' => $this->EE->config->config['site_name'], // use site name as default
		    'is_managed_team' => 1
		);
		
		$this->EE->db->insert('tm_teams', $data);
		
		unset($data);
		
		// create leagues table
		
		// TODO: season start DATE! not year! same with season end
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'varchar', 'constraint' => '250'),
			'year_start' => array('type' => 'varchar', 'constraint' => '4'),
			'year_end' => array('type' => 'varchar', 'constraint' => '4'),
			'promotion_places' => array('type' => 'int', 'constraint' => '4'),
			'promotion_playoff_places' => array('type' => 'int', 'constraint' => '4'),
			'relegation_places' => array('type' => 'int', 'constraint' => '4'),
			'relegation_playoff_places' => array('type' => 'int', 'constraint' => '4')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_leagues');
		
		unset($fields);
		
		// create league/team links table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'league' => array('type' => 'int', 'constraint' => '10'),
			'team' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_league_teams');
		
		unset($fields);
		
		// create league/match links table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'league' => array('type' => 'int', 'constraint' => '10'),
			'match' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_league_matches');
		
		unset($fields);
		
		// create league/deductions table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'league' => array('type' => 'int', 'constraint' => '10'),
			'team' => array('type' => 'int', 'constraint' => '10'),
			'points_deducted' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE)
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_league_deductions');
		
		unset($fields);
		
		// create cups table
		
		// TODO: season start DATE! not year! same with season end
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'varchar', 'constraint' => '250'),
			'year_start' => array('type' => 'varchar', 'constraint' => '4'),
			'year_end' => array('type' => 'varchar', 'constraint' => '4')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_cups');
		
		unset($fields);
		
		// create cup rounds table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'cup' => array('type' => 'int', 'constraint' => '10'),
			'name' => array('type' => 'varchar', 'constraint' => '250'),
			'round' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_cup_rounds');
		
		unset($fields);
		
		// create cup/team links table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'cup' => array('type' => 'int', 'constraint' => '10'),
			'round' => array('type' => 'int', 'constraint' => '10'),
			'team' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_cup_teams');
		
		unset($fields);
		
		// create cup/match links table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'cup' => array('type' => 'int', 'constraint' => '10'),
			'round' => array('type' => 'int', 'constraint' => '10'),
			'match' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_cup_matches');
		
		unset($fields);
		
		// create matches table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'home_team' => array('type' => 'int', 'constraint' => '10'),
			'away_team' => array('type' => 'int', 'constraint' => '10'),
			'kick_off' => array('type' => 'datetime'),
			'officials' => array('type' => 'text'),
			'attendance' => array('type' => 'int', 'constraint' => '10'),
			'venue' => array('type' => 'varchar', 'constraint' => '250'),
			'home_half_time_score' => array('type' => 'int', 'constraint' => '10'),
			'away_half_time_score' => array('type' => 'int', 'constraint' => '10'),
			'home_full_time_score' => array('type' => 'int', 'constraint' => '10'),
			'away_full_time_score' => array('type' => 'int', 'constraint' => '10'),
			'home_penalty_score' => array('type' => 'int', 'constraint' => '10'),
			'away_penalty_score' => array('type' => 'int', 'constraint' => '10'),
			'aet' => array('type' => 'boolean', 'default' => 0),
			'penalties' => array('type' => 'boolean', 'default' => 0),
			'report' => array('type' => 'text'),
			'home_squad' => array('type' => 'int', 'constraint' => '10'),
			'away_squad' => array('type' => 'int', 'constraint' => '10'),
			'postponed' => array('type' => 'boolean', 'default' => 0)
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_matches');
		
		unset($fields);
		
		// create starting lineups table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'player' => array('type' => 'int', 'constraint' => '10', 'default'=>'0'),
			'player_name' => array('type' => 'int', 'constraint' => '10'),
			'match' => array('type' => 'int', 'constraint' => '10'),
			'start_time' => array('type' => 'int', 'constraint' => '10'),
			'end_time' => array('type' => 'int', 'constraint' => '10'),
			'substitute' => array('type' => 'int', 'constraint' => '1')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_sarting_lineups');
		
		unset($fields);
		
		// create stats table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'player' => array('type' => 'int', 'constraint' => '10'),
			'match' => array('type' => 'int', 'constraint' => '10'),
			'stat_type' => array('type' => 'int', 'constraint' => '10'),
			'extra' => array('type' => 'int', 'constraint' => '10')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_stats');
		
		unset($fields);
		
		// create settings table
		
		$fields = array(
			'id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name' => array('type' => 'text'),
			'value' => array('type' => 'text')
		 );
	
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tm_settings');
		
		unset($fields);
		
		
		// PRESET 
		$data = array(
		    'name' => "preset",
		    'value' => "football"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// SQUAD TYPES
		$data = array(
		    'name' => "squad_type",
		    'value' => "1st Team"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "squad_type",
		    'value' => "Reserve Team"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "squad_type",
		    'value' => "Youth Team"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// GAME PARTS
		$data = array(
		    'name' => "game_parts",
		    'value' => "2"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "game_part_length",
		    'value' => "45"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "extra_time_parts",
		    'value' => "2"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "extra_time_part_length",
		    'value' => "15"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "has_extra_time",
		    'value' => "true"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "has_penalties",
		    'value' => "true"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// DISPLAY PREFS
		$data = array(
		    'name' => "show_half_time_score",
		    'value' => "true"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// TEAM
		$data = array(
		    'name' => "starting_team_player_count",
		    'value' => "11"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "max_substitutes_on_bench",
		    'value' => "10"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// STATS
		$data = array(
		    'name' => "stat_type",
		    'value' => "goal"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "stat_type",
		    'value' => "yellow_card"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "stat_type",
		    'value' => "red_card"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		
		
		// LEAGUE
		$data = array(
		    'name' => "points_per_win",
		    'value' => "3"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "points_per_draw",
		    'value' => "1"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);
		$data = array(
		    'name' => "points_per_loss",
		    'value' => "0"
		);
		$this->EE->db->insert('tm_settings', $data);
		unset($data);

		// todo: match stats
		// todo: dates & localisation
		
		
		// installed, return true
		return TRUE;
	
	}
	
	function uninstall(){
	
		 $this->EE->load->dbforge();
	
		 $this->EE->db->select('module_id');
		 $query = $this->EE->db->get_where('modules', array('module_name' => 'TeamManager'));
	
		 $this->EE->db->where('module_id', $query->row('module_id'));
		 $this->EE->db->delete('module_member_groups');
	
		 $this->EE->db->where('module_name', 'TeamManager');
		 $this->EE->db->delete('modules');
	
		 $this->EE->db->where('class', 'TeamManager');
		 $this->EE->db->delete('actions');
	
		 $this->EE->dbforge->drop_table('tm_players');
		 $this->EE->dbforge->drop_table('tm_teams');
		 $this->EE->dbforge->drop_table('tm_leagues');
		 $this->EE->dbforge->drop_table('tm_league_teams');
		 $this->EE->dbforge->drop_table('tm_league_matches');
		 $this->EE->dbforge->drop_table('tm_league_deductions');
		 $this->EE->dbforge->drop_table('tm_cups');
		 $this->EE->dbforge->drop_table('tm_cup_rounds');
		 $this->EE->dbforge->drop_table('tm_cup_teams');
		 $this->EE->dbforge->drop_table('tm_cup_matches');
		 $this->EE->dbforge->drop_table('tm_matches');
		 $this->EE->dbforge->drop_table('tm_sarting_lineups');
		 $this->EE->dbforge->drop_table('tm_stats');
		 $this->EE->dbforge->drop_table('tm_settings');
	
		 return TRUE;
		 
	}
	
	function update($current = ''){
		if($current == $this->version) { return TRUE; }
		
		$this->EE->load->dbforge();

		if (version_compare($current, '1.0.1', '<')){
			// update 1.0.0 to 1.0.1
			$fields = array('postponed' => array('type' => 'boolean', 'default' => 0));
			$this->EE->dbforge->add_column('tm_matches', $fields);
		}
		
		if (version_compare($current, '1.0.2', '<')){
			// update 1.0.1 to 1.0.2
			$fields = array('sponsor_file' => array('type' => 'text'),
							'sponsor_dir' => array('type' => 'int', 'constraint' => '10'));
			$this->EE->dbforge->add_column('tm_players', $fields);
		}

		
		if (version_compare($current, '1.0.3', '<')){
			// update 1.0.2 to 1.0.3
		}
		
		if (version_compare($current, '1.0.4', '<')){
			// update 1.0.3 to 1.0.4
			$fields = array('played' => array('type' => 'boolean', 'default' => 0));
			$this->EE->dbforge->add_column('tm_matches', $fields);
		}
		
		if (version_compare($current, '1.0.5', '<')){
			// update 1.0.4 to 1.0.5
			$fields = array('archived' => array('type' => 'boolean', 'default' => 0));
			$this->EE->dbforge->add_column('tm_leagues', $fields);
			$fields = array('archived' => array('type' => 'boolean', 'default' => 0));
			$this->EE->dbforge->add_column('tm_cups', $fields);
		}

		return TRUE;
		
	}
	
}