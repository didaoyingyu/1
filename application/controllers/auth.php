<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		$this->load->helper('url');
		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
						$this->load->library('mongo_db') :
						$this->load->database();
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
		$this->load->helper('language');
	}

	//redirect if needed, otherwise display the user list
	function index() {
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect('/', 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user) {
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}
			$this->_render_page('auth/index', $this->data);
		}
	}

	//log the user in
	function login() {
		$this->data['title'] = "Login";
		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == true) {
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');
			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('/', 'refresh');
			} else {
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else {
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);
			$this->_render_page('auth/login', $this->data);
		}
	}

	function check_password() {
		if ($this->ion_auth->is_admin()) {
			$user = $this->ion_auth->user()->row();
			$email = $user->email;
			if ($this->ion_auth->login($email, $this->input->post('pass'))) {
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				echo 'correct';
			} else {
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				echo 'no'; //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else {
			echo 'not admin';
		}
	}

	function check_userid() {
		$user = $this->input->post('user');
		$flag = 0;
		if ($this->ion_auth->is_admin()) {
			$query = $this->db->get('users');
			foreach ($query->result() as $row) {
				if ($row->email == $user) {
					$flag = 1;
					$id = $row->id;
				}
			}
			if ($flag == 0) {
				echo "Please check username again";
			} else if ($flag == 1) {
				echo $id;
			}
		} else {
			echo 'not admin';
		}
	}

	function check_useridselft() {
		$user = $this->input->post('user');
		$flag = 0;
			$query = $this->db->get('users');
			foreach ($query->result() as $row) {
				if ($row->email == $user) {
					$flag = 1;
					$id = $row->id;
				}
			}
			if ($flag == 0) {
				echo "Please check username again";
			} else if ($flag == 1) {
				echo $id;
			}
		
	}

	function check_username() {
		$user = $this->input->post('user');
		$flag = 0;
		if ($this->ion_auth->is_admin()) {
			$query = $this->db->get('users');
			foreach ($query->result() as $row) {
				if ($row->email == $user) {
					$flag = 1;
					$body = "You are playing as " . $row->username . ". Your email id is " . $row->email;
				}
			}
			if ($flag == 0) {
				echo "Please check username again";
			} else if ($flag == 1) {
				echo $body;
			}
		} else {
			echo 'not admin 1';
		}
	}


	function check_username_self() {
		$user = $this->input->post('user');
		$flag = 0;
			$query = $this->db->get('users');
			foreach ($query->result() as $row) {
				if ($row->email == $user) {
					$flag = 1;
					$body = "You are playing as " . $row->username . ". Your email id is " . $row->email;
				}
			}
			if ($flag == 0) {
				echo "Please check username again";
			} else if ($flag == 1) {
				echo $body;
			}

	}

	//log the user out
	function logout() {
		$this->data['title'] = "Logout";
		//log the user out
		$logout = $this->ion_auth->logout();
		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');
	}

	//change password
	function change_password() {
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$user = $this->ion_auth->user()->row();
		if ($this->form_validation->run() == false) {
			//display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = array(
				'name' => 'old',
				'id' => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array(
				'name' => 'new',
				'id' => 'new',
				'type' => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			);
			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id' => 'new_confirm',
				'type' => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			);
			$this->data['user_id'] = array(
				'name' => 'user_id',
				'id' => 'user_id',
				'type' => 'hidden',
				'value' => $user->id,
			);
			//render
			$this->_render_page('auth/change_password', $this->data);
		} else {
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));
			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
			if ($change) {
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	//forgot password
	function forgot_password() {
		$this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
		if ($this->form_validation->run() == false) {
			//setup the input
			$this->data['email'] = array('name' => 'email',
				'id' => 'email',
			);
			if ($this->config->item('identity', 'ion_auth') == 'username') {
				$this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
			} else {
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}
			//set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->_render_page('auth/forgot_password', $this->data);
		} else {
			// get identity for that email
			$config_tables = $this->config->item('tables', 'ion_auth');
			$identity = $this->db->where('email', $this->input->post('email'))->limit('1')->get($config_tables['users'])->row();
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
			if ($forgotten) {
				//if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code = NULL) {
		if (!$code) {
			show_404();
		}
		$user = $this->ion_auth->forgotten_password_check($code);
		if ($user) {
			//if the code is valid then display the password reset form
			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');
			if ($this->form_validation->run() == false) {
				//display the form
				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id' => 'new',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				);
				$this->data['new_password_confirm'] = array(
					'name' => 'new_confirm',
					'id' => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				);
				$this->data['user_id'] = array(
					'name' => 'user_id',
					'id' => 'user_id',
					'type' => 'hidden',
					'value' => $user->id,
				);
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;
				//render
				$this->_render_page('auth/reset_password', $this->data);
			} else {
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {
					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);
					show_error($this->lang->line('error_csrf'));
				} else {
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};
					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));
					if ($change) {
						//if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						$this->logout();
					} else {
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		} else {
			//if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	//activate the user
	function activate($id, $code = false) {
		if ($code !== false) {
			$activation = $this->ion_auth->activate($id, $code);
		} else if ($this->ion_auth->is_admin()) {
			$activation = $this->ion_auth->activate($id);
		}
		if ($activation) {
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		} else {
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	//deactivate the user
	function deactivate($id = NULL) {
		$id = $this->config->item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');
		if ($this->form_validation->run() == FALSE) {
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();
			$this->_render_page('auth/deactivate_user', $this->data);
		} else {
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes') {
				// do we have a valid request?
				//if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
					show_error($this->lang->line('error_csrf'));
				}
				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
					$this->ion_auth->deactivate($id);
				}
			}
			//redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	//create a new user
	function create_user() {
		$this->data['title'] = "Create User";
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		if ($this->form_validation->run() == true) {
			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name')
			);
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {
			//check to see if we are creating the user
			//redirect them back to the admin page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		} else {
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);
			$this->_render_page('auth/create_user', $this->data);
		}
	}

	//edit a user
	function edit_user($id) {
		$this->data['title'] = "Edit User";
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}
		$user = $this->ion_auth->user($id)->row();
		$groups = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();
		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');
		//$this->form_validation->set_rules('deck', $this->lang->line('edit_user_validation_deck_label'), 'xss_clean');
		if (isset($_POST) && !empty($_POST)) {
			//echo $this->input->post('id');
			//echo "<br>";
			//echo $id;
			// echo $this->lang->line('error_csrf');
			//die();
			// do we have a valid request?
			//if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			/* if ($id != $this->input->post('id'))
			  {
			  show_error($this->lang->line('error_csrf'));
			  echo "hello";
			  } */
			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name')
			);
			//print_r($data); show value bt
			//die();
			//Update the groups user belongs to
			$groupData = $this->input->post('groups');
			if (isset($groupData) && !empty($groupData)) {
				$this->ion_auth->remove_from_group('', $id);
				foreach ($groupData as $grp) {
					$this->ion_auth->add_to_group($grp, $id);
				}
				//print_r($this->ion_auth->add_to_group($grp, $id));
				// die();
			}
			//update the password if it was posted
			if ($this->input->post('password')) {
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
				$data['password'] = $this->input->post('password');
			}
			if ($this->form_validation->run() === TRUE) {
				$deck_post = $this->input->post('deck');
				if (!empty($deck_post)) {
					$deck = implode(',', $this->input->post('deck'));
				} else {
					$deck = '';
				}
				$data['deck'] = $deck;
				//print_r($data);
				//die();
				$this->ion_auth->update($user->id, $data);
				//check to see if we are creating the user
				//redirect them back to the admin page
				$this->session->set_flashdata('message', "User Saved");
				redirect("auth", 'refresh');
			}
		}
		//display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();
		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		//pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;
		$this->data['first_name'] = array(
			'name' => 'first_name',
			'id' => 'first_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		);
		$this->data['last_name'] = array(
			'name' => 'last_name',
			'id' => 'last_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		);
		$this->data['password'] = array(
			'name' => 'password',
			'id' => 'password',
			'type' => 'password'
		);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id' => 'password_confirm',
			'type' => 'password'
		);
		/* load all decks */
		$this->load->model('card');
		$allDecksArr = $this->card->load_decks(-1);
		$this->data['allDecks'] = array();
		$this->data['allDecks'] = $allDecksArr;
		$this->data['Decks_value'] = $user->deck;
		$this->_render_page('auth/edit_user', $this->data);
	}

	// create a new group
	function create_group() {
		$this->data['title'] = $this->lang->line('create_group_title');
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');
		if ($this->form_validation->run() == TRUE) {
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		} else {
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['group_name'] = array(
				'name' => 'group_name',
				'id' => 'group_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			);
			$this->data['description'] = array(
				'name' => 'description',
				'id' => 'description',
				'type' => 'text',
				'value' => $this->form_validation->set_value('description'),
			);
			$this->_render_page('auth/create_group', $this->data);
		}
	}

	// create a new group1
	function create_groupu() {
		$this->data['title'] = $this->lang->line('create_group_title');
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}
		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');
		$this->form_validation->set_rules('deck', $this->lang->line('create_group_validation_deck_label'), 'xss_clean');
		if ($this->form_validation->run() == TRUE) {
			$deck_post = $this->input->post('deck');
			if (!empty($deck_post)) {
				$deck = implode(',', $this->input->post('deck'));
			} else {
				$deck = '';
			}
			//print_r($this->input->post('deck'));
//die();
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'), '', $deck);
			if ($new_group_id) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		} else {
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['group_name'] = array(
				'name' => 'group_name',
				'id' => 'group_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			);
			$this->data['description'] = array(
				'name' => 'description',
				'id' => 'description',
				'type' => 'text',
				'value' => $this->form_validation->set_value('description'),
			);
			/* load all decks */
			$this->load->model('card');
			$allDecksArr = $this->card->load_decks(-1);
			$this->data['allDecks'] = array();
			$this->data['allDecks'] = $allDecksArr;
			// $this->load->view('delete_decks',$allDecks);
			/* 			$this->data['SelectDeck'] = array(
			  'name'  => 'SelectDeck',
			  'id'	=> 'SelectDeck',
			  'type'  => 'text',
			  'value' => $this->form_validation->set_value('SelectDeck'),
			  ); */
			$this->_render_page('auth/create_groupu', $this->data);
		}
	}

	//edit a group
	function edit_group($id) {
		// bail if no group id given
		if (!$id || empty($id)) {
			redirect('auth', 'refresh');
		}
		$this->data['title'] = $this->lang->line('edit_group_title');
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}
		$group = $this->ion_auth->group($id)->row();
		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');
		$this->form_validation->set_rules('deck', $this->lang->line('create_group_validation_deck_label'), 'xss_clean');
		if (isset($_POST) && !empty($_POST)) {
			if ($this->form_validation->run() === TRUE) {
				$deck = implode(',', $this->input->post('deck'));
				//print_r($group->deck);
				//die();
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description'], $deck);
				if ($group_update) {
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				} else {
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth", 'refresh');
			}
		}
		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		//pass the user to the view
		$this->data['group'] = $group;
		$this->data['group_name'] = array(
			'name' => 'group_name',
			'id' => 'group_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('group_name', $group->name),
		);
		$this->data['group_description'] = array(
			'name' => 'group_description',
			'id' => 'group_description',
			'type' => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		);
		$this->load->model('card');
		$allDecksArr = $this->card->load_decks(-1);
		$this->data['allDecks'] = array();
		$this->data['allDecks'] = $allDecksArr;
		$this->data['Decks_value'] = $group->deck;
		$this->_render_page('auth/edit_group', $this->data);
	}

	function _get_csrf_nonce() {
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);
		return array($key => $value);
	}

	function _valid_csrf_nonce() {
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
				$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function _render_page($view, $data = null, $render = false) {
		$this->viewdata = (empty($data)) ? $this->data : $data;
		$view_html = $this->load->view($view, $this->viewdata, $render);
		if (!$render)
			return $view_html;
	}

	function supervisedModeSave() {
		$gameArray = $this->input->post('data');
		$user_id = $this->input->post('user_id');
		// $user = $this->ion_auth->user()->row();
		// $user_Id = $user->id;
		//print_r($gameArray['deck']);
		$gameArray['game_date'] = date("d-m-Y");
		$decks = $gameArray['deck'];
		unset($gameArray['deck']);
		$gameSave = array();
		$gameSave = $gameArray;
		$decksArray = array();
		$html = '';
		$cardArray = array();
		$this->load->model('card');
		$user_Id = $gameSave['user_id'];
		$cardDetailArray = $this->card->getCardDetailsCount($user_Id);
		$gameSave['cardCompleteCount'] = $cardDetailArray[0]->card_count;
		
		//$wrong_total = 0;
	
		$cardCompleteCorrectCount = 0;
		$change_minus = 0;
		$prevx = 0;
		$change_plus = 0;
		$prex = 0;
		$prexx = 0;
		$current_true_prex = 0;
		$currect_true_prexx = 0;
		$total_cards = 0;
		$new_card_count = 0;
		$new_card_correct_count = 0;
		$correct_to_date_card_count = 0;
		$cardCount = $this->card->getAllCardsCount($user_id);
		$gameSave['TotalCardCount'] = $cardCount;
		$html1 = '';
		foreach ($decks AS $deck) {
			if (!in_array($deck['deck_id'], $decksArray)) {
				//  $gameSave['deck'][$deck['deck_id']]=$deck['deck_id'];
				//   $gameSave['deck'][$deck['deck_id']]['card_id']=$deck['card_id'];
				// $gameSave['deck'][$deck['deck_id']]['ans']=$deck['ans'];
				//$gameSave['deck']=$deck['deck_id'].','.$deck['ans'];
				$decksArray[] = $deck['deck_id'];
			}
			if (!in_array($deck['card_id'], $cardArray)) {
				
				$cardArray[] = $deck['card_id'];
				$isCorrectArray = array();
				if ($deck['ans'] == 'true') {
					
					$card_id = $deck['card_id'];
					$html1.=" AND c.card_id!=$card_id ";
					$correct_to_date_card_count++;
					$isCorrectArray = $this->card->isAllAnswersCorrectPre($deck['card_id'], $user_Id);
				
				if ($isCorrectArray[0]->total_count == 0) {
						$cardCompleteCorrectCount++;
					}
					$previousTimeStatus = $this->card->isPreviousTimeWrong($deck['card_id'], $user_Id);
					
					if (count($previousTimeStatus) > 0)
					{
						if ($previousTimeStatus[0]->ans == 'false') {
							$previousTwoTimeStatus = $this->card->isPreviousTwoTimeWrong($deck['card_id'], $user_Id);
							if (count($previousTwoTimeStatus) > 0) {
								if ($previousTwoTimeStatus[0]->ans == 'false') {
									$currect_true_prexx++;
									$prexx++;
								} else {
									$current_true_prex++;
									$prex++;
								}
							} else {
								$current_true_prex++;
								$prex++;
							}
							$change_plus++;
						}
					} else {
						$new_card_count++;
						$new_card_correct_count++;
						$change_plus++;
					}
				} 
				else
				{
				
					$previousTimeStatus = $this->card->isPreviousTimeWrong($deck['card_id'], $user_Id);
					
					if (count($previousTimeStatus) > 0) 
					{
						if ($previousTimeStatus[0]->ans == 'true') 
						{
							$change_minus++;
						} 
						else 
						{
							$previousTwoTimeStatus = $this->card->isPreviousTwoTimeWrong($deck['card_id'], $user_Id);
							if (count($previousTwoTimeStatus) > 0) 
							{
								if ($previousTwoTimeStatus[0]->ans == 'false') 
								{
									$prexx++;
								}
								else
								{
									$prex++;
								}
							} 
							
							else 
							{
								$prex++;
							}
						}
					} 
					else
					{
						
						$new_card_count++;
						
						
						
						if($new_card_count <= 0)
						{
							$change_minus++;	
						}
						else
						{
							$change_minus = 0;
						}
						
					}
				}
			}
		}
		$getCorrectOnly = $this->card->getCorrectOnlyCount($user_Id, $html1);
		$cards['cardCount'] = $this->card->neverTestedCards($user_Id);
		// Below code comment by Vishal 
                /*$correct_to_date_card_count = $cards['cardCount']['O'];
		$correct_to_date_card_count = $correct_to_date_card_count + $getCorrectOnly[0]->total_count;*/
                // Code write by vishal start
                $correct_to_date_card_count = $cards['cardCount']['O'];
                $correct_to_date_card_count =  $correct_to_date_card_count;
		// Code write by vishal end
        $gameSave['correct_to_date_card_count'] = $correct_to_date_card_count;
		
//		$gameSave['wrong_total'] = $wrong_total;
		$gameSave['new_card_count'] = $new_card_count;
		$gameSave['new_card_correct_count'] = $new_card_correct_count;
		$gameSave['current_true_prex'] = $current_true_prex;
		$gameSave['current_true_prexx'] = $currect_true_prexx;
		$gameSave['prex'] = $prex;
		$gameSave['prexx'] = $prexx;
		$gameSave['change_plus'] = $change_plus;
		$gameSave['no_pre_wrong'] = $prevx;
		$gameSave['change_minus'] = $change_minus;
		$gameSave['cardCompleteCorrectCount'] = $cardCompleteCorrectCount;
		$deck_html = "";
		foreach ($decksArray AS $key => $option) {
			$query = $this->db->get_where('card_deck', array('deck_id' => $option));
			foreach ($query->result() as $row) {
				$html.=$row->deck_name;
				$html.=',';
			}
			$deck_id = $option;
		}
		
		
		$gameSave['decks_name'] = $html;
		$gameSave['game_date'] = date("Y-m-d H:i:s");
		$gameSave['cards'] = $decks;
		
		$cards = $this->card->saveReportDetailsSuperficialMode($gameSave);
		//if ($cards == 1) {
		//	echo 'success';
		//} else {
		//	echo 'Something went wrong';
		//}
		
		
	}
	
	
	function reviewModeSave() {
		$this->load->model('card');
		$gameArray = json_decode($this->input->post('data'), true);
		$decks = $gameArray['deck'];
		unset($gameArray['deck']);
		$gameSave = array();
		$gameSave = $gameArray;
		$decksArray = array();
		$html = '';
		$user = $this->ion_auth->user()->row();
		foreach ($decks AS $deck) {
			$deck['user_id'] = $user->id;
			if (isset($deck['reason'])) {
				$reason = str_replace('&gt;&gt;', '', $deck['reason']);
				$deck['reason'] = $reason;
			}
			if (!in_array($deck['deck_id'], $decksArray)) {
				$decksArray[] = $deck['deck_id'];
			}
		}
		$deck_html = "";
		foreach ($decksArray AS $key => $option) {
			$query = $this->db->get_where('card_deck', array('deck_id' => $option));
			foreach ($query->result() as $row) {
				$deck_html.=$row->deck_name;
				$deck_html.=',';
			}
			$deck_id = $option;
		}
		$gameSave['decks_name'] = $deck_html;
		$gameSave['game_date'] = date("Y-m-d H:i:s");
		$gameSave['cards'] = $decks;
		$cards = $this->card->saveReportDetailsReviewMode($gameSave, $user->id);
		if ($cards != 1) {
			echo "error";
		}
	}

	function save_quick_review_log() {
		$this->load->model('card');
		$log = json_decode($this->input->post('data'), true);
		$user = $this->ion_auth->user()->row();
		$log['user_id'] = $user->id;
		$review_log = $this->card->save_quick_review_log($log);
		if (!$review_log) {
			echo "error";
		}
	}
	function save_quick_review_log_self() {
		$this->load->model('card');
		$log = json_decode($this->input->post('data'), true);
		$user = $this->ion_auth->user()->row();
		$log['user_id'] = $user->id;
		$review_log = $this->card->save_quick_review_log_self($log);
		if (!$review_log) {
			echo "error";
		}
	}

	function save_quick_review_log_re() {
		$this->load->model('card');
		$log = json_decode($this->input->post('data'), true);
		$user = $this->ion_auth->user()->row();
		$log['user_id'] = $user->id;
		$review_log = $this->card->save_quick_review_log_re($log);
		if (!$review_log) {
			echo "error";
		}
	}

	function get_log_utp() {
		$this->load->model('card');
		$log = json_decode($this->input->post('data'), true);
		$user = $this->ion_auth->user()->row();
		$log['user_id'] = $user->id;
		$review_log = $this->card->get_log_utp($log);
		if ($review_log) {
			echo $review_log;
		}
	}
	

	function get_log_utp_re() {
		$this->load->model('card');
		$log = json_decode($this->input->post('data'), true);
		$user = $this->ion_auth->user()->row();
		$log['user_id'] = $user->id;
		$review_log = $this->card->get_log_utp_re($log);
		if ($review_log) {
			echo $review_log;
		}
	}
	
	function review_log_control() {
		$this->load->model('card');
		$log = $this->input->post('log');
		$review_log = $this->card->save_quick_review_control($log);
		$message = "success";
		return $message;
	}
}
