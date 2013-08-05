<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teammanager_mcp {
	
	function Teammanager_mcp(){
	
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->EE->load->library('teammanager_lib');
		
		$this->links = array(
							lang('dashboard')			=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=index',
							lang('manage_teams')		=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=teams',
							lang('manage_players')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=players',
							lang('manage_leagues')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=leagues',
							lang('manage_cups')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=cups',
							lang('manage_friendly_matches')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=friendly_matches'/*,
							lang('settings')	=> BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=settings'*/ // TODO -- settings
						);
		
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">.spacer { height: 25px; }</style>');
		
		$this->EE->cp->set_right_nav($this->links);
		
		$this->settings = $this->EE->teammanager->__parse_settings();
		
	}

	// --------------------------------------------------------------------

	function index(){
		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('dashboard'));

		$vars = array('links' => $this->links);

		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	function teams(){
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_teams'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$teams = array();
		
		// get all teams, highlight our team
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_teams');

		foreach($query->result_array() as $row)
		{
			$teams[] = array('id' => $row['id'], 'name' => $row['name'], 'managed' => $row['is_managed_team']);
		}
		
		$vars['teams'] = $teams;
		
		return $this->EE->load->view('teams/teams', $vars, TRUE);
		
	}
	
	function add_team(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_team'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$vars['error'] = "";
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('team_name');
		
		if($name){
			
			// check if it already exists
			$this->EE->db->where("name", $name);
			$query = $this->EE->db->get('tm_teams');
			if ($query->num_rows() > 0){
				// error	
				$vars['error'] = lang("team_exists");
			}else{
				// if not, insert 
				$data = array(
				    'name' => $name				
				);
				
				$this->EE->db->insert('tm_teams', $data);
				// redirect back to teams
				return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=teams');
			}
			
		}else{
			
			if($post_submit){
				// show error
				$vars['error'] = lang("team_form_error");
			}
			
			return $this->EE->load->view('teams/add_team', $vars, TRUE);
		}
		
		
	}
	
	function delete_team(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_team'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$team_id = $_GET['team'];
		$team_id = $this->EE->db->escape_str($team_id);
		$post_submit = $this->EE->input->post('submit');
		
		$vars['team_id'] = $team_id;
		
		if(!$post_submit){
			// get team name
			$query = $this->EE->db->get_where('tm_teams', array('id' => $team_id));
			$row = $query->result_array();
			$vars['team_name'] = $row[0]['name'];
		}else{
			// posted, delete and return
			$this->EE->db->delete('tm_teams', array('id' => $team_id));
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=teams');
		}
		
		return $this->EE->load->view('teams/delete_team', $vars, TRUE);
		
	}
	
	function edit_team(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_team'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$team_id = $_GET['team'];
		$team_id = $this->EE->db->escape_str($team_id);
		$post_submit = $this->EE->input->post('submit');
		
		$vars['team_id'] = $team_id;
		$vars['error'] = "";
		
		// get team name
		$query = $this->EE->db->get_where('tm_teams', array('id' => $team_id));
		$row = $query->result_array();
		$vars['team_name'] = $row[0]['name'];
		
		if($post_submit){
			$name = $this->EE->input->post('team_name');
			if($name){
				// check if it already exists
				$this->EE->db->where("name", $name);
				$query = $this->EE->db->get('tm_teams');
				if ($query->num_rows() > 0){
					// error	
					$vars['error'] = lang("team_exists");
				}else{
					// if not, insert 
					$data = array(
					    'name' => $name			
					);
					$this->EE->db->where('id', $team_id);
					$this->EE->db->update('tm_teams', $data);
					// redirect back to teams
					return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=teams');
				}
			}else{
				
				// show error, name doesnt exist
				$vars['error'] = lang("team_form_error");
				
			}
		}
		
		return $this->EE->load->view('teams/edit_team', $vars, TRUE);
		
	}
	
	function players(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_players'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$players = array();
		
		// get all players
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_players');
		
		foreach($query->result_array() as $row)
		{
			$players[] = array('id' => $row['id'], 'name' => $row['name'], 'position' => $row['position']);
		}
		
		$vars['players'] = $players;
		
		return $this->EE->load->view('players/players', $vars, TRUE);
		
	}
	
	function add_player(){
	
		$this->EE->load->helper('form');
		$this->EE->load->library('javascript');
		$this->EE->load->library('file_field');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_player'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		$vars['file_field'] = $this->EE->file_field->field("photograph", $data = '', $allowed_file_dirs = 'all', $content_type = 'all');
		$vars['sponsor_file_field'] = $this->EE->file_field->field("sponsor_logo", $data = '', $allowed_file_dirs = 'all', $content_type = 'all');
		
		$post_submit = $this->EE->input->post('submit');
		
		$this->EE->file_field->browser();
		
		$name = $this->EE->input->post('player_name');
		$is_player = (bool)($this->EE->input->post('is_player'));
		$squad = $this->EE->input->post('squad');
		$squad_number = $this->EE->input->post('squad_number');
		$position = $this->EE->input->post('position');
		$dob = $this->EE->input->post('dob');
		$photo_file = $this->EE->input->post('photograph_hidden');
		$photo_dir = $this->EE->input->post('photograph_hidden_dir');
		$profile = $this->EE->input->post('profile');
		$sponsor = $this->EE->input->post('sponsor');
		$sponsor_link = $this->EE->input->post('sponsor_link');
		$at_club = (bool)($this->EE->input->post('at_club'));
		$sponsor_logo_file = $this->EE->input->post('sponsor_logo_hidden');
		$sponsor_logo_dir = $this->EE->input->post('sponsor_logo_hidden_dir');
		
		$vars['name'] = '';
		$vars['is_player'] = true;
		$vars['squad'] = '';
		$vars['squad_number'] = '';
		$vars['position'] = '';
		$vars['dob'] = '';
		$vars['picture_file'] = '';
		$vars['picture_dir'] = '';
		$vars['profile'] = '';
		$vars['sponsor'] = '';
		$vars['sponsor_link'] = 'http://';
		$vars['at_club'] = true;
		$vars['sponsor_file'] = '';
		$vars['sponsor_dir'] = '';
		
		if($post_submit && $name && $position){
			// enough fields present
			
			if($dob && strpos($dob, "/") !== FALSE){
				// format date
				list($d, $m, $y) = explode("/", $dob);
				$dob = $y."-".$m."-".$d;
			}
			
			$data = array(
			    'name' => $name,
			    'is_player' => $is_player,
			    'squad' => $squad,
			    'squad_number' => $squad_number,
			    'position' => $position,
			    'dob' => $dob,
			    'picture_file' => $photo_file,
			    'picture_dir' => $photo_dir,
			    'profile' => $profile,
			    'sponsor' => $sponsor,
			    'sponsor_link' => $sponsor_link,
			    'at_club' => $at_club,
			    'sponsor_file' => $sponsor_logo_file,
			    'sponsor_dir' => $sponsor_logo_dir
			);	
			$this->EE->db->insert('tm_players', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=players');
			
		}else{
			
			if($post_submit){
				// player form error
				$vars['error'] = lang("player_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['is_player'] = $is_player;
				$vars['squad'] = $squad;
				$vars['squad_number'] = $squad_number;
				$vars['position'] = $position;
				$vars['dob'] = $dob;
				$vars['picture_file'] = $photo_file;
				$vars['picture_dir'] = $photo_dir;
				$vars['profile'] = $profile;
				$vars['sponsor'] = $sponsor;
				$vars['sponsor_link'] = $sponsor_link;
				$vars['at_club'] = $at_club;
				$vars['sponsor_file'] = $sponsor_logo_file;
				$vars['sponsor_dir'] = $sponsor_logo_dir;
				
				// format photo
				$this->EE->file_field->browser();
				$f = $this->EE->file_field->format_data($vars['picture_file'], $vars['picture_dir']);
				$vars['file_field'] = $this->EE->file_field->field("photograph", $f, $allowed_file_dirs = 'all', $content_type = 'all');
				$f = $this->EE->file_field->format_data($vars['sponsor_file'], $vars['sponsor_dir']);
				$vars['sponsor_file_field'] = $this->EE->file_field->field("sponsor_logo", $f, $allowed_file_dirs = 'all', $content_type = 'all');
			}
			
			$this->EE->javascript->output(array(
			    '$("#is_player").click(function(){
				   $("#squad_number_field").toggle();
				});
				')
			);
			
			$this->EE->javascript->compile();
			
			return $this->EE->load->view('players/add_player', $vars, TRUE);	
			
		}
		
	}
	
	function edit_player(){
	
		$vars = array();
		$player_id = $_GET['player'];
		$player_id = $this->EE->db->escape_str($player_id);
		$vars['player_id'] = $player_id;
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('player_name');
		$is_player = (bool)($this->EE->input->post('is_player'));
		$squad = $this->EE->input->post('squad');
		$squad_number = $this->EE->input->post('squad_number');
		$position = $this->EE->input->post('position');
		$dob = $this->EE->input->post('dob');
		$photo_file = $this->EE->input->post('photograph_hidden');
		$photo_dir = $this->EE->input->post('photograph_hidden_dir');
		$profile = $this->EE->input->post('profile');
		$sponsor = $this->EE->input->post('sponsor');
		$sponsor_link = $this->EE->input->post('sponsor_link');
		$at_club = (bool)($this->EE->input->post('at_club'));
		$sponsor_logo_file = $this->EE->input->post('sponsor_logo_hidden');
		$sponsor_logo_dir = $this->EE->input->post('sponsor_logo_hidden_dir');
		
		if($post_submit && $name && $position){
			// enough fields present
			
			if($dob && strpos($dob, "/") !== FALSE){
				// format date
				list($d, $m, $y) = explode("/", $dob);
				$dob = $y."-".$m."-".$d;
			}
			
			$data = array(
			    'name' => $name,
			    'is_player' => $is_player,
			    'squad' => $squad,
			    'squad_number' => $squad_number,
			    'position' => $position,
			    'dob' => $dob,
			    'picture_file' => $photo_file,
			    'picture_dir' => $photo_dir,
			    'profile' => $profile,
			    'sponsor' => $sponsor,
			    'sponsor_link' => $sponsor_link,
			    'at_club' => $at_club,
			    'sponsor_file' => $sponsor_logo_file,
			    'sponsor_dir' => $sponsor_logo_dir
			);
			
			//update
			$this->EE->db->where('id', $player_id);
			$this->EE->db->update('tm_players', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=players');
		}else{
		
			$this->EE->load->helper('form');
			$this->EE->load->library('javascript');
			$this->EE->load->library('file_field');
			
			$this->EE->javascript->output(array(
			    '$("#is_player").click(function(){
				   $("#squad_number_field").toggle();
				});
				'));
			
			$this->EE->javascript->compile();
			
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_player'));
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
			
			$query = $this->EE->db->get_where('tm_players', array('id' => $player_id));
			$row = $query->result_array();
			$vars['name'] = $row[0]['name'];
			$vars['is_player'] = $row[0]['is_player'];
			$vars['squad'] = $row[0]['squad'];
			$vars['squad_number'] = $row[0]['squad_number'];
			$vars['position'] = $row[0]['position'];
			$vars['dob'] = $row[0]['dob'];
			$vars['picture_file'] = $row[0]['picture_file'];
			$vars['picture_dir'] = $row[0]['picture_dir'];
			$vars['profile'] = $row[0]['profile'];
			$vars['sponsor'] = $row[0]['sponsor'];
			$vars['sponsor_link'] = $row[0]['sponsor_link'];
			$vars['at_club'] = $row[0]['at_club'];
			$vars['sponsor_file'] = $row[0]['sponsor_file'];
			$vars['sponsor_dir'] = $row[0]['sponsor_dir'];
			
			// format dob
			if($vars['dob'] != "0000-00-00"){
				$vars['dob'] = strtotime($vars['dob']);
				$vars['dob'] = date("d/m/Y", $vars['dob']);
			}else{
				$vars['dob'] = "";
			}
			// format photo
			$this->EE->file_field->browser();
			$f = $this->EE->file_field->format_data($vars['picture_file'], $vars['picture_dir']);
			$vars['file_field'] = $this->EE->file_field->field("photograph", $f, $allowed_file_dirs = 'all', $content_type = 'all');
			$f = $this->EE->file_field->format_data($vars['sponsor_file'], $vars['sponsor_dir']);
			$vars['sponsor_file_field'] = $this->EE->file_field->field("sponsor_logo", $f, $allowed_file_dirs = 'all', $content_type = 'all');
			
			
			if($post_submit){
				// set error
				$vars['error'] = lang("player_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['is_player'] = $is_player;
				$vars['squad'] = $squad;
				$vars['squad_number'] = $squad_number;
				$vars['position'] = $position;
				$vars['dob'] = $dob;
				$vars['picture_file'] = $photo_file;
				$vars['picture_dir'] = $photo_dir;
				$vars['profile'] = $profile;
				$vars['sponsor'] = $sponsor;
				$vars['sponsor_link'] = $sponsor_link;
				$vars['at_club'] = $at_club;
				$vars['sponsor_file'] = $sponsor_logo_file;
				$vars['sponsor_dir'] = $sponsor_logo_dir;
				
				// format photo
				$this->EE->file_field->browser();
				$f = $this->EE->file_field->format_data($vars['picture_file'], $vars['picture_dir']);
				$vars['file_field'] = $this->EE->file_field->field("photograph", $f, $allowed_file_dirs = 'all', $content_type = 'all');
				$f = $this->EE->file_field->format_data($vars['sponsor_file'], $vars['sponsor_dir']);
				$vars['sponsor_file_field'] = $this->EE->file_field->field("sponsor_logo", $f, $allowed_file_dirs = 'all', $content_type = 'all');
				
			}
			
		}
		
		return $this->EE->load->view('players/edit_player', $vars, TRUE);
		
	}
	
	function delete_player(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_player'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$player_id = $_GET['player'];
		$player_id = $this->EE->db->escape_str($player_id);
		$post_submit = $this->EE->input->post('submit');
		
		$vars['player_id'] = $player_id;
		
		if(!$post_submit){
			// get team name
			$query = $this->EE->db->get_where('tm_players', array('id' => $player_id));
			$row = $query->result_array();
			$vars['player_name'] = $row[0]['name'];
		}else{
			// posted, delete and return
			$this->EE->db->delete('tm_players', array('id' => $player_id));
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=players');
		}
		
		return $this->EE->load->view('players/delete_player', $vars, TRUE);
		
	}
	
	function leagues(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_leagues'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$leagues = array();
		
		// get all leagues
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_leagues');
		
		foreach($query->result_array() as $row){
			$leagues[] = array('id' => $row['id'], 'name' => $row['name'], 'year_start' => $row['year_start'], 'year_end' => $row['year_end'], 'archived' => $row['archived']);
		}
		
		$vars['leagues'] = $leagues;
		
		return $this->EE->load->view('leagues/leagues', $vars, TRUE);
		
	}
		
	function add_league(){
	
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_league'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('name');
		$year_start = $this->EE->input->post('year_start');
		$year_end = $this->EE->input->post('year_end');
		$promotion_places = (int)$this->EE->input->post('promotion_places');
		$promotion_playoff_places = (int)$this->EE->input->post('promotion_playoff_places');
		$relegation_places = (int)$this->EE->input->post('relegation_places');
		$relegation_playoff_places = (int)$this->EE->input->post('relegation_playoff_places');
		
		$vars['name'] = '';
		$vars['year_start'] = '';
		$vars['year_end'] = '';
		$vars['promotion_places'] = '';
		$vars['promotion_playoff_places'] = '';
		$vars['relegation_places'] = '';
		$vars['relegation_playoff_places'] = '';
		
		if($post_submit && $name){
			// enough fields present
			$data = array(
			    'name' => $name,
				'year_start' => $year_start,
				'year_end' => $year_end,
				'promotion_places' => $promotion_places,
				'promotion_playoff_places' => $promotion_playoff_places,
				'relegation_places' => $relegation_places,
				'relegation_playoff_places' => $relegation_playoff_places
			);	
			$this->EE->db->insert('tm_leagues', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=leagues');
			
		}else{
			
			if($post_submit){
				// set error
				$vars['error'] = lang("league_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['year_start'] = $year_start;
				$vars['year_end'] = $year_end;
				$vars['promotion_places'] = $promotion_places;
				$vars['promotion_playoff_places'] = $promotion_playoff_places;
				$vars['relegation_places'] = $relegation_places;
				$vars['relegation_playoff_places'] = $relegation_playoff_places;
			}
			
			return $this->EE->load->view('leagues/add_league', $vars, TRUE);	
			
		}
		
	}
		
	function edit_league(){
	
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_league'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		$vars['league_id'] = $league_id;
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('name');
		$year_start = $this->EE->input->post('year_start');
		$year_end = $this->EE->input->post('year_end');
		$promotion_places = (int)$this->EE->input->post('promotion_places');
		$promotion_playoff_places = (int)$this->EE->input->post('promotion_playoff_places');
		$relegation_places = (int)$this->EE->input->post('relegation_places');
		$relegation_playoff_places = (int)$this->EE->input->post('relegation_playoff_places');
		$archived = ($this->EE->input->post('archived') == "archived");
		
		// get default from db		
		$query = $this->EE->db->get_where('tm_leagues', array('id' => $league_id));
		$row = $query->result_array();
		$vars['name'] = $row[0]['name'];
		$vars['year_start'] = $row[0]['year_start'];
		$vars['year_end'] = $row[0]['year_end'];
		$vars['promotion_places'] = $row[0]['promotion_places'];
		$vars['promotion_playoff_places'] = $row[0]['promotion_playoff_places'];
		$vars['relegation_places'] = $row[0]['relegation_places'];
		$vars['relegation_playoff_places'] = $row[0]['relegation_playoff_places'];
		$vars['archived'] = $row[0]['archived'];
		
		if($post_submit && $name){
			// enough fields present
			$data = array(
			    'name' => $name,
				'year_start' => $year_start,
				'year_end' => $year_end,
				'promotion_places' => $promotion_places,
				'promotion_playoff_places' => $promotion_playoff_places,
				'relegation_places' => $relegation_places,
				'relegation_playoff_places' => $relegation_playoff_places,
				'archived' => $archived
			);
			// update
			$this->EE->db->where('id', $league_id);
			$this->EE->db->update('tm_leagues', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=leagues');
			
		}else{
			
			if($post_submit){
				// set error
				$vars['error'] = lang("league_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['year_start'] = $year_start;
				$vars['year_end'] = $year_end;
				$vars['promotion_places'] = $promotion_places;
				$vars['promotion_playoff_places'] = $promotion_playoff_places;
				$vars['relegation_places'] = $relegation_places;
				$vars['relegation_playoff_places'] = $relegation_playoff_places;
				$vars['archived'] = $archived;
			}
			
			return $this->EE->load->view('leagues/edit_league', $vars, TRUE);	
			
		}
		
	}
	
	function delete_league(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_league'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		$post_submit = $this->EE->input->post('submit');
		
		$vars['league_id'] = $league_id;
		
		if(!$post_submit){
			// get team name
			$query = $this->EE->db->get_where('tm_leagues', array('id' => $league_id));
			$row = $query->result_array();
			$vars['league_name'] = $row[0]['name'];
		}else{
			// posted, delete and return
			$this->EE->db->delete('tm_leagues', array('id' => $league_id));
			// delete links to matches
			$query = $this->EE->db->get_where('tm_league_matches', array('league' => $league_id));
			foreach($query->result_array() as $row){
				$this->delete_match($row['match']);
			}
			// delete other links
			$this->EE->db->delete('tm_league_teams', array('league' => $league_id));
			$this->EE->db->delete('tm_league_deductions', array('league' => $league_id));
			// redirect back
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=leagues');
		}
		
		return $this->EE->load->view('leagues/delete_league', $vars, TRUE);
		
	}
	
	function manage_league(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_league'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		$query = $this->EE->db->get_where('tm_leagues', array('id' => $league_id));
		$row = $query->result_array();
		$vars['league_name'] = $row[0]['name'];
		$vars['league_id'] = $league_id;
		$vars['teams'] = array();
		$vars['exc_teams'] = array();
		$vars['matches'] = array();
		$vars['deds'] = array();
		$vars['points'] = "";
		
		// get teams in this league
		$this->EE->db->order_by("id", "asc");
		$this->EE->db->where('league', $league_id);
		$query = $this->EE->db->get('tm_league_teams');
		
		foreach($query->result_array() as $row){
			$team = $this->EE->teammanager->__get_team($row['team']);
			$vars['teams'][] = array('id' => $team['id'], 'name' => $team['name'], 'managed' => $team['is_managed_team']);
		}
		
		// get teams not in this league
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_teams');
		
		foreach($query->result_array() as $row){
			$this->EE->db->where('league', $league_id);
			$this->EE->db->where('team', $row['id']);
			$results = $this->EE->db->get('tm_league_teams');
			if ($results->num_rows() == 0){
				// its not in this league
				$vars['exc_teams'][] =  array('id' => $row['id'], 'name' => $row['name'], 'managed' => $row['is_managed_team']);
			}
			
		}
		
		// get matches in this league
		$this->EE->db->order_by("kick_off", "asc");
		$this->EE->db->where('league', $league_id);
        $this->EE->db->join("tm_matches", "tm_league_matches.match = tm_matches.id");
		$query = $this->EE->db->get('tm_league_matches');
		
		foreach($query->result_array() as $row){
			$match = $this->EE->teammanager->__get_match($row['match']);
			$vars['matches'][] =  $match;
		}
		
		// get deductions for this league
		$this->EE->db->order_by("id", "asc");
		$this->EE->db->where('league', $league_id);
		$query = $this->EE->db->get('tm_league_deductions');
		
		foreach($query->result_array() as $row){
			// get team
			$this->EE->db->where('id', $row['team']);
			$results = $this->EE->db->get('tm_teams');
			$team = $results->result_array();
			$team = $team[0];
			// set to var
			$vars['deds'][] = array('id'=>$row['id'], 'team'=>$team['name'], 'points'=>$row['points_deducted']);
		}
		
		return $this->EE->load->view('leagues/manage_league', $vars, TRUE);
		
	}
	
	function add_team_to_league(){
		// get team
		$team = (int)$this->EE->input->post('team');
		
		// get league
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		
		// add
		$data = array(
		    'league' => $league_id,
			'team' => $team
		);	
		if($team) $this->EE->db->insert('tm_league_teams', $data);
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.AMP.'league='.$league_id);
	}
	
	function delete_team_from_league(){
		// get team
		$team = $_GET['team'];
		$team = $this->EE->db->escape_str($team);
		
		// get league
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		
		// delete
		if($team) $this->EE->db->delete('tm_league_teams', array('league' => $league_id, 'team' => $team));
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.AMP.'league='.$league_id);
	}
	
	function add_deduction_to_league(){
		// get team
		$team = (int)$this->EE->input->post('team');
		
		// get points
		$points = (int)$this->EE->input->post('points');
		
		// get league
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		
		// add
		$data = array(
		    'league' => $league_id,
			'team' => $team,
			'points_deducted' => $points
		);	
		$this->EE->db->insert('tm_league_deductions', $data);
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.AMP.'league='.$league_id);
		
	}
	
	function delete_deduction_from_league(){
		// get team
		$ded = $_GET['deduction'];
		$ded = $this->EE->db->escape_str($ded);
		
		// get league
		$league_id = $_GET['league'];
		$league_id = $this->EE->db->escape_str($league_id);
		
		// delete
		$this->EE->db->delete('tm_league_deductions', array('id' => $ded));
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.AMP.'league='.$league_id);
		
	}
	
	function cups(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_cups'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$cups = array();
		
		// get all cups
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_cups');
		
		foreach($query->result_array() as $row){
			$cups[] = array('id' => $row['id'], 'name' => $row['name'], 'year_start' => $row['year_start'], 'year_end' => $row['year_end'], 'archived' => $row['archived']);
		}
		
		$vars['cups'] = $cups;
		
		return $this->EE->load->view('cups/cups', $vars, TRUE);
		
	}
		
	function add_cup(){
	
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_cup'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('name');
		$year_start = $this->EE->input->post('year_start');
		$year_end = $this->EE->input->post('year_end');
		
		$vars['name'] = '';
		$vars['year_start'] = '';
		$vars['year_end'] = '';
		
		if($post_submit && $name){
			// enough fields present
			$data = array(
			    'name' => $name,
				'year_start' => $year_start,
				'year_end' => $year_end
			);	
			$this->EE->db->insert('tm_cups', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=cups');
			
		}else{
			
			if($post_submit){
				// set error
				$vars['error'] = lang("cup_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['year_start'] = $year_start;
				$vars['year_end'] = $year_end;
			}
			
			return $this->EE->load->view('cups/add_cup', $vars, TRUE);	
			
		}
		
	}
		
	function edit_cup(){
	
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_cup'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		$vars['cup_id'] = $cup_id;
		$vars['error'] = "";
		$vars['settings'] = $this->settings;
		
		$post_submit = $this->EE->input->post('submit');
		
		$name = $this->EE->input->post('name');
		$year_start = $this->EE->input->post('year_start');
		$year_end = $this->EE->input->post('year_end');
		$archived = ($this->EE->input->post('archived') == "archived");
		
		// get default from db		
		$query = $this->EE->db->get_where('tm_cups', array('id' => $cup_id));
		$row = $query->result_array();
		$vars['name'] = $row[0]['name'];
		$vars['year_start'] = $row[0]['year_start'];
		$vars['year_end'] = $row[0]['year_end'];
		$vars['archived'] = $row[0]['archived'];
		
		if($post_submit && $name){
			// enough fields present
			$data = array(
			    'name' => $name,
				'year_start' => $year_start,
				'year_end' => $year_end,
				'archived' => $archived
			);
			// update
			$this->EE->db->where('id', $cup_id);
			$this->EE->db->update('tm_cups', $data);
			// redirect back to teams
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=cups');
			
		}else{
			
			if($post_submit){
				// set error
				$vars['error'] = lang("cup_form_error");
				
				// set all fields from post values
				$vars['name'] = $name;
				$vars['year_start'] = $year_start;
				$vars['year_end'] = $year_end;
				$vars['archived'] = $archived;
			}
			
			return $this->EE->load->view('cups/edit_cup', $vars, TRUE);	
			
		}
		
	}
	
	function delete_cup(){
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_cup'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		$post_submit = $this->EE->input->post('submit');
		
		$vars['cup_id'] = $cup_id;
		
		if(!$post_submit){
			// get team name
			$query = $this->EE->db->get_where('tm_cups', array('id' => $cup_id));
			$row = $query->result_array();
			$vars['cup_name'] = $row[0]['name'];
		}else{
			// posted, delete and return
			$this->EE->db->delete('tm_cups', array('id' => $cup_id));
			// delete links to matches
			$query = $this->EE->db->get_where('tm_cup_matches', array('cup' => $cup_id));
			foreach($query->result_array() as $row){
				$this->delete_match($row['match']);
			}
			// delete other links
			$this->EE->db->delete('tm_cup_rounds', array('cup' => $cup_id));
			$this->EE->db->delete('tm_cup_teams', array('cup' => $cup_id));
			// redirect back
			return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=cups');
		}
		
		return $this->EE->load->view('cups/delete_cup', $vars, TRUE);
		
	}
	
	function manage_cup(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_cup'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		$query = $this->EE->db->get_where('tm_cups', array('id' => $cup_id));
		$row = $query->result_array();
		$vars['cup_id'] = $cup_id;
		$vars['cup_name'] = $row[0]['name'];
		$vars['rounds'] = array();
		
		// get all rounds for this cup
		$this->EE->db->where('cup', $cup_id);
		$query = $this->EE->db->get('tm_cup_rounds');
		
		foreach($query->result_array() as $row){
			$vars['rounds'][] = $row;
		}
		
		return $this->EE->load->view('cups/manage_cup', $vars, TRUE);
	}
	
	function add_round_to_cup(){
		// get round
		$name = $this->EE->input->post('name');
		
		// get cup
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		
		// add
		$data = array(
		    'cup' => $cup_id,
			'name' => $name
		);	
		if($name) $this->EE->db->insert('tm_cup_rounds', $data);
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_cup'.AMP.'cup='.$cup_id);
	}
	
	function delete_round_from_cup(){
		// get round
		$round = $_GET['round'];
		$round = $this->EE->db->escape_str($round);
		
		// get cup
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		
		// delete
		$this->EE->db->delete('tm_cup_rounds', array('id' => $round));
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_cup'.AMP.'cup='.$cup_id);
		
	}
	
	function manage_round(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_round'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars = array();
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		$query = $this->EE->db->get_where('tm_cups', array('id' => $cup_id));
		$row = $query->result_array();
		$vars['cup_name'] = $row[0]['name'];
		$vars['cup_id'] = $cup_id;
		$round_id = $_GET['round'];
		$round_id = $this->EE->db->escape_str($round_id);
		$query = $this->EE->db->get_where('tm_cup_rounds', array('id' => $round_id));
		$row = $query->result_array();
		$vars['round_name'] = $row[0]['name'];
		$vars['round_id'] = $round_id;
		
		$vars['teams'] = array();
		$vars['exc_teams'] = array();
		$vars['matches'] = array();
		
		// get teams in this cup
		$this->EE->db->order_by("id", "asc");
		$this->EE->db->where('cup', $cup_id);
		$this->EE->db->where('round', $round_id);
		$query = $this->EE->db->get('tm_cup_teams');
		
		foreach($query->result_array() as $row){
			$team = $this->EE->teammanager->__get_team($row['team']);
			$vars['teams'][] = array('id' => $team['id'], 'name' => $team['name'], 'managed' => $team['is_managed_team']);
		}
		
		// get teams not in this cup
		$this->EE->db->order_by("id", "asc");
		$query = $this->EE->db->get('tm_teams');
		
		foreach($query->result_array() as $row){
			$this->EE->db->where('cup', $cup_id);
			$this->EE->db->where('round', $round_id);
			$this->EE->db->where('team', $row['id']);
			$results = $this->EE->db->get('tm_cup_teams');
			if ($results->num_rows() == 0){
				// its not in this league
				$vars['exc_teams'][] =  array('id' => $row['id'], 'name' => $row['name'], 'managed' => $row['is_managed_team']);
			}
			
		}
		
		// get matches in this cup
		$this->EE->db->order_by("kick_off", "asc");
		$this->EE->db->where('round', $round_id);
		$this->EE->db->where('cup', $cup_id);
        $this->EE->db->join("tm_matches", "tm_cup_matches.match = tm_matches.id");
		$query = $this->EE->db->get('tm_cup_matches');
		
		foreach($query->result_array() as $row){
			$match = $this->EE->teammanager->__get_match($row['match']);
			$vars['matches'][] =  $match;
		}
		
		return $this->EE->load->view('cups/manage_round', $vars, TRUE);
		
	}
	
	function add_team_to_cup(){
		// get team
		$team = (int)$this->EE->input->post('team');
		
		// get cup
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		
		// get round
		$round_id = $_GET['round'];
		$round_id = $this->EE->db->escape_str($round_id);
		
		// add
		$data = array(
		    'cup' => $cup_id,
		    'round' => $round_id,
			'team' => $team
		);	
		$this->EE->db->insert('tm_cup_teams', $data);
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_round'.AMP.'cup='.$cup_id.AMP.'round='.$round_id);
	}
	
	function delete_team_from_cup(){
		// get team
		$team = $_GET['team'];
		$team = $this->EE->db->escape_str($team);
		
		// get cup
		$cup_id = $_GET['cup'];
		$cup_id = $this->EE->db->escape_str($cup_id);
		
		// get round
		$round_id = $_GET['round'];
		$round_id = $this->EE->db->escape_str($round_id);
		
		// delete
		$this->EE->db->delete('tm_cup_teams', array('cup' => $cup_id, 'round' => $round_id, 'team' => $team));
		
		// redirect back
		return $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_round'.AMP.'cup='.$cup_id.AMP.'round='.$round_id);
	}
	
	function friendly_matches(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('manage_friendly_matches'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		$matches = array();
		
		// get all matches that arent in cups/leagues
		$this->EE->db->order_by("kick_off", "asc");
		$query = $this->EE->db->get('tm_matches');
		
		foreach($query->result_array() as $row){
			
			$this->EE->db->where('match', $row['id']);
			$l_query = $this->EE->db->get('tm_league_matches');
			$this->EE->db->where('match', $row['id']);
			$c_query = $this->EE->db->get('tm_cup_matches');
			
			if ($l_query->num_rows() == 0 && $c_query->num_rows() == 0){
				// its not in either competition type
				$matches[] = $this->EE->teammanager->__get_match($row['id']);
			}
			
		}
		
		$vars['matches'] = $matches;
		
		return $this->EE->load->view('friendly_matches/friendly_matches', $vars, TRUE);
		
	}
		
	
	function settings(){
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('settings'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));

		$vars = array();
		
		return $this->EE->load->view('settings', $vars, TRUE);
		
	}
	
	
	/* MATCH MANAGEMENT */
	
	function add_match(){
		
		$this->EE->load->helper('form');
		
		$vars = array();
		$vars['error'] = '';
		$vars['settings'] = $this->settings;
		
		$referer = $this->__get_referer_info();
		
		$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=add_match'.$referer['url_info'];
		
		// find out if its league, cup or friendly
		if($referer['type']=="league"){
			// its a league
			$title = $this->EE->lang->line("add_a_match")." (".$referer['league']['name'].")";
		}elseif($referer['type']=="cup"){
			// its a cup
			$title = $this->EE->lang->line("add_a_match")." (".$referer['cup']['name']." - ".$referer['round']['name'].")";
		}else{
			// its a friendly
			$title = $this->EE->lang->line("add_a_match")." (".$this->EE->lang->line("friendly").")";
		}
		
		$this->EE->cp->set_variable('cp_page_title', $title);
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$vars['home_team'] = '';
		$vars['away_team'] = '';
		$vars['home_team_squad'] = '';
		$vars['away_team_squad'] = '';
		$vars['kick_off'] = '';
		$vars['venue'] = '';
		$vars['teams'] = $referer['teams'];
		
		// if a form has been posted
		$post_submit = $this->EE->input->post('submit');
		
		if($post_submit){
			$home_team = $this->EE->input->post('home_team');
			$vars['home_team'] = $home_team;
			$away_team = $this->EE->input->post('away_team');
			$vars['away_team'] = $away_team;
			$home_team_squad = $this->EE->input->post('home_team_squad');
			$vars['home_team_squad'] = $home_team_squad;
			$away_team_squad = $this->EE->input->post('away_team_squad');
			$vars['away_team_squad'] = $away_team_squad;
			$kick_off = $this->EE->input->post('kick_off');
			$vars['kick_off'] = $kick_off;
			$kick_off = $this->EE->teammanager->__format_date($kick_off);
			$venue = $this->EE->input->post('venue');
			$vars['venue'] = $venue;
			// check we have date
			if($kick_off){
				// insert into matches
				$data = array(
						'home_team' => $home_team,
						'away_team' => $away_team,
						'home_squad' => $home_team_squad,
						'away_squad' => $away_team_squad,
						'kick_off' => $kick_off,
						'venue' => $venue
					);
				$this->EE->db->insert('tm_matches', $data);
				$match_id = $this->EE->db->insert_id();
				
				if($referer['type']=="league"){
					// insert into matches/links where appropriate
					$data = array(
							'league' => $referer['league_id'],
							'match' => $match_id
						);
					$this->EE->db->insert("tm_league_matches", $data);
				}elseif($referer['type']=="cup"){
					// insert into matches/links where appropriate
					$data = array(
							'cup' => $referer['cup_id'],
							'round' => $referer['round_id'],
							'match' => $match_id
						);
					$this->EE->db->insert("tm_cup_matches", $data);
				}
				// redirect back
				return $this->EE->functions->redirect($referer['manage_url']);
			}else{
				// show error
				$vars['error'] = $this->EE->lang->line('invalid_date');
			}
		}
		
		// display form
		return $this->EE->load->view('matches/add_match', $vars, TRUE);
		
	}
	
	function pre_match(){
		
		$this->EE->load->helper('form');
		
		$match_id = $_GET['match'];
		$match_id = $this->EE->db->escape_str($match_id);
		
		$vars = array();
		$vars['error'] = '';
		$vars['settings'] = $this->settings;
		
		$referer = $this->__get_referer_info();
		
		$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=pre_match'.AMP.'match='.$match_id.$referer['url_info'];
		
		// lineups
		
		// display form
		return $this->EE->load->view('matches/pre_match', $vars, TRUE);
	}
	
	function post_match(){
		
		$this->EE->load->helper('form');
		$this->EE->load->library('table');
		$this->EE->load->library('javascript');
		
		$match_id = $_GET['match'];
		$match_id = $this->EE->db->escape_str($match_id);
		
		$vars = array();
		
		$referer = $this->__get_referer_info();
		
		$vars = $this->EE->teammanager->__get_match($match_id);
		$vars['error'] = '';
		$vars['settings'] = $this->settings;
		$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=post_match'.AMP.'match='.$match_id.$referer['url_info'];
		
		
		// if a form has been posted
		$post_submit = $this->EE->input->post('submit');
		
		if($post_submit){
			$home_half_time_score = (int)$this->EE->input->post('home_half_time_score');
			$away_half_time_score = (int)$this->EE->input->post('away_half_time_score');
			$home_full_time_score = (int)$this->EE->input->post('home_full_time_score');
			$away_full_time_score = (int)$this->EE->input->post('away_full_time_score');
			$penalties = (int)(bool)$this->EE->input->post('penalties');
			$home_penalty_score = (int)$this->EE->input->post('home_penalty_score');
			$away_penalty_score = (int)$this->EE->input->post('away_penalty_score');
			$aet = (int)(bool)$this->EE->input->post('aet');
			$officials = $this->EE->input->post('officials');
			$attendance = (int)$this->EE->input->post('attendance');
			$report = $this->EE->input->post('report');
			$played = ($this->EE->input->post('played') == "played");
			// insert into matches
			$data = array(
					'home_half_time_score' => $home_half_time_score,
					'away_half_time_score' => $away_half_time_score,
					'home_full_time_score' => $home_full_time_score,
					'away_full_time_score' => $away_full_time_score,
					'penalties' => $penalties,
					'home_penalty_score' => $home_penalty_score,
					'away_penalty_score' => $away_penalty_score,
					'aet' => $aet,
					'officials' => $officials,
					'attendance' => $attendance,
					'played' => $played,
					'report' => $report
				);
			$this->EE->db->where('id', $match_id);
			$this->EE->db->update('tm_matches', $data);
			// redirect back
			return $this->EE->functions->redirect($referer['manage_url']);
		}
		
		$title = $this->EE->lang->line("post_match")." (".$vars['home_team_name']." ".lang('versus')." ".$vars['away_team_name'].")";
		$this->EE->cp->set_variable('cp_page_title', $title);
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$this->EE->javascript->output(array(
		    '
		    $p = $("#penalties");
		    
		    $p.click(function(){
			   doCheck();
			});
			
			function doCheck(){		
			    $p = $("#penalties");	
				if($p.is(":checked")){
				   $p.parent().parent().next().show();
				}else{
				   $p.parent().parent().next().hide();
				}
			}
			
			doCheck();
			
			')
		);
		
		$this->EE->javascript->compile();
		
		// display form
		return $this->EE->load->view('matches/post_match', $vars, TRUE);
	}
	
	function match_stats(){
		
		$this->EE->load->helper('form');
		
		$match_id = $_GET['match'];
		$match_id = $this->EE->db->escape_str($match_id);
		
		$vars = array();
		$vars['error'] = '';
		$vars['settings'] = $this->settings;
		
		$referer = $this->__get_referer_info();
		
		$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=pre_match'.AMP.'match='.$match_id.$referer['url_info'];
		
		// stats
		
		// display form
		return $this->EE->load->view('matches/match_stats', $vars, TRUE);
	}
	
	function edit_match(){
		
		$this->EE->load->helper('form');
		
		$match_id = $_GET['match'];
		$match_id = $this->EE->db->escape_str($match_id);
		
		$vars = array();
		$vars['error'] = '';
		$vars['settings'] = $this->settings;
		
		$referer = $this->__get_referer_info();
		
		$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=edit_match'.AMP.'match='.$match_id.$referer['url_info'];
		
		if($referer['type']=="league"){
			// its a league
			$title = $this->EE->lang->line("edit_a_match")." (".$referer['league']['name'].")";
		}elseif($referer['type']=="cup"){
			// its a cup
			$title = $this->EE->lang->line("edit_a_match")." (".$referer['cup']['name']." - ".$referer['round']['name'].")";
		}else{
			// its a friendly
			$title = $this->EE->lang->line("edit_a_match")." (".$this->EE->lang->line("friendly").")";
		}
		$this->EE->cp->set_variable('cp_page_title', $title);
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
		
		$match = $this->EE->teammanager->__get_match($match_id);
		$vars['home_team'] = $match['home_team'];
		$vars['away_team'] = $match['away_team'];
		$vars['home_team_squad'] = $match['home_squad'];
		$vars['away_team_squad'] = $match['away_squad'];
		$vars['kick_off'] = date("d/m/Y H:i:s", $match['kick_off']);
		$vars['venue'] = $match['venue'];
		$vars['postponed'] = $match['postponed'];
		$vars['teams'] = $referer['teams'];
		
		// if a form has been posted
		$post_submit = $this->EE->input->post('submit');
		
		if($post_submit){
			$home_team = $this->EE->input->post('home_team');
			$vars['home_team'] = $home_team;
			$away_team = $this->EE->input->post('away_team');
			$vars['away_team'] = $away_team;
			$home_team_squad = $this->EE->input->post('home_team_squad');
			$vars['home_team_squad'] = $home_team_squad;
			$away_team_squad = $this->EE->input->post('away_team_squad');
			$vars['away_team_squad'] = $away_team_squad;
			$kick_off = $this->EE->input->post('kick_off');
			$vars['kick_off'] = $kick_off;
			$kick_off = $this->EE->teammanager->__format_date($kick_off);
			$venue = $this->EE->input->post('venue');
			$vars['venue'] = $venue;
			$postponed = (bool)$this->EE->input->post('postponed');
			$vars['postponed'] = $postponed;
			// check date
			if($kick_off){
				// update matches
				$data = array(
					'home_team' => $home_team,
					'away_team' => $away_team,
					'home_squad' => $home_team_squad,
					'away_squad' => $away_team_squad,
					'kick_off' => $kick_off,
					'venue' => $venue,
					'postponed' => (int)$postponed
				);
				$this->EE->db->where('id', $match_id);
				$this->EE->db->update('tm_matches', $data);
					
				// redirect back
				return $this->EE->functions->redirect($referer['manage_url']);
			}else{
				// show error
				$vars['error'] = $this->EE->lang->line('invalid_date');
			}
		}
		
		// display form
		return $this->EE->load->view('matches/edit_match', $vars, TRUE);
	}
	
	function delete_match($match_id = null){
		// see if its a call from another function or from url
		if($match_id){
			$show = false;
		}else{
			$show = true;
			$match_id = $_GET['match'];
			$match_id = $this->EE->db->escape_str($match_id);
			$vars = array();
			$vars['match'] = $this->EE->teammanager->__get_match($match_id);
			
			$referer = $this->__get_referer_info();
			
			$vars['form_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=delete_match'.AMP.'match='.$match_id.$referer['url_info'];
			
			if($referer['type']=="league"){
				// its a league
				$title = $this->EE->lang->line("edit_a_match")." (".$referer['league']['name'].")";
			}elseif($referer['type']=="cup"){
				// its a cup
				$title = $this->EE->lang->line("edit_a_match")." (".$referer['cup']['name']." - ".$referer['round']['name'].")";
			}else{
				// its a friendly
				$title = $this->EE->lang->line("edit_a_match")." (".$this->EE->lang->line("friendly").")";
			}
			
		}
		
		$post_submit = $this->EE->input->post('submit');
		
		if(!$show || $post_submit){
			
			// delete stats, etc
			$this->EE->db->delete('tm_sarting_lineups', array('match' => $match_id));
			$this->EE->db->delete('tm_stats', array('match' => $match_id));
			// delete other stuff
			$this->EE->db->delete('tm_league_matches', array('match' => $match_id));
			$this->EE->db->delete('tm_cup_matches', array('match' => $match_id));
			$this->EE->db->delete('tm_matches', array('id' => $match_id));
			
			if($show){
				// redirect back
				return $this->EE->functions->redirect($referer['manage_url']);
				
			}
		}
		
		if($show){
			$this->EE->cp->set_variable('cp_page_title', lang("delete_match"));
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager', $this->EE->lang->line('teammanager_module_name'));
			return $this->EE->load->view('matches/delete_match', $vars, TRUE);	
		}
	}
	
	function __get_referer_info(){
		$info = array();
		
		if(array_key_exists("league", $_GET) && $_GET['league']){
			// basic info
			$info['type'] = "league";
			// specific id
			$league_id = $_GET['league'];
			$league_id = $this->EE->db->escape_str($league_id);
			$info['league_id'] = $league_id;
			$info['league'] = $this->EE->teammanager->__get_league($league_id);
			// manage url
			$info['url_info'] = AMP."league=".$league_id;
			$info['manage_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_league'.$info['url_info'];
			// links table
			$info['links_table'] = "tm_league_matches";
			
			// teams for league
			$this->EE->db->order_by("id", "asc");
			$this->EE->db->where('league', $league_id);
			$query = $this->EE->db->get('tm_league_teams');
			foreach($query->result_array() as $row){
				$this->EE->db->where('id', $row['team']);
				$query = $this->EE->db->get('tm_teams');
				$team = $query->result_array();
				$team = $team[0];
				$info['teams'][] = array('id' => $team['id'], 'name' => $team['name'], 'managed' => $team['is_managed_team']);
			}
			
		}elseif(array_key_exists("cup", $_GET) && $_GET['cup']){
			$info['type'] = "cup";
			// specific id
			$cup_id = $_GET['cup'];
			$cup_id = $this->EE->db->escape_str($cup_id);
			$round_id = $_GET['round'];
			$round_id = $this->EE->db->escape_str($round_id);
			$info['cup_id'] = $cup_id;
			$info['round_id'] = $round_id;
			$info['cup'] = $this->EE->teammanager->__get_cup($cup_id);
			$info['round'] = $this->EE->teammanager->__get_round($round_id);
			// manage url
			$info['url_info'] = AMP."cup=".$cup_id.AMP."round=".$round_id;
			$info['manage_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=manage_round'.$info['url_info'];
			// links table
			$info['links_table'] = "tm_cup_matches";
			
			// teams for cup
			$this->EE->db->order_by("id", "asc");
			$this->EE->db->where('cup', $cup_id);
			$this->EE->db->where('round', $round_id);
			$query = $this->EE->db->get('tm_cup_teams');
			foreach($query->result_array() as $row){
				$this->EE->db->where('id', $row['team']);
				$query = $this->EE->db->get('tm_teams');
				$team = $query->result_array();
				$team = $team[0];
				$info['teams'][] = array('id' => $team['id'], 'name' => $team['name'], 'managed' => $team['is_managed_team']);
			}
			
		}else{
			$info['type'] = "friendly_match";
			// manage url
			$info['url_info'] = "";
			$info['manage_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=teammanager'.AMP.'method=friendly_matches'.$info['url_info'];
			$info['links_table'] = "";
			
			// teams
			$this->EE->db->order_by("id", "asc");
			$query = $this->EE->db->get('tm_teams');
			foreach($query->result_array() as $team){
				$info['teams'][] = array('id' => $team['id'], 'name' => $team['name'], 'managed' => $team['is_managed_team']);
			}
		}
		
		return $info;
	}
	
}