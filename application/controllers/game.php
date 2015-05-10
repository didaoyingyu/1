<?php
/*
  This is the main controler of the Game
  The flow of the game is as follows
  1. index function - entry point of the game
  2. show login view
  3. login function
  4. deck selection
  5. game mode selection
  6. game view
  7. game data/summary view
 */

class game extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('card');
		$this->load->helper('language');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
	}

	function index() {
		if (!$this->ion_auth->logged_in()) {
			$this->load->view('login');
		} else {
			if ($this->ion_auth->is_admin()) {
				$this->load->view('admin_view');
			} else {
				$this->load->view('game_view');
			}
		}
	}

	/* login a user to the system */
	function login() {
		if (!$this->ion_auth->logged_in()) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$remember = FALSE;
			$logged_in = $this->ion_auth->login($username, $password, $remember);
			if (!$logged_in) {
				$data = array('message' => 'Login Failed');
				$this->load->view('login', $data);
			}
		} else {
			$logged_in = true;
		}
		if ($logged_in) {
			redirect('');
		}
	}

	/* logout */
	function logout() {
		if ($this->ion_auth->logged_in()) {
			$this->ion_auth->logout();
		}
		redirect('');
	}

	/* show the game */
	function game_view() {
		/* security check */
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		/* load the game view */
		$this->load->view('game_view');
	}

	function review_log() {
		/* security check */
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$data['users'] = $this->card->get_users();
		/* load review log control */
		$this->load->view('review_log_control', $data);
	}

	function quick_review_log_scroll()
	{
		function filter($log) {
			$interval = strtotime($log['itp']) - strtotime($log['utp']);
			$days = floor($interval / 86400);
			return array($log['itp'], $log['question'], $log['answer'],  $log['ans_userInput'],  $log['deck_name'],
				$log['before_history'],  $log['before_rank'], $log['ans'], $log['reason'],
				"$days, ".gmdate("H:i:s", $interval % 86400),  $log['after_history'],
				$log['test_history'], $log['after_rank']);
		}

		$userid = $_POST['user_id'];
		$scroll_limit = $_POST['scroll_limit'];
		$this->load->model('card');
		$logs = $this->card->get_quick_review_log_limit_scroll($userid,$scroll_limit);
		echo json_encode(array_map("filter", $logs));
	}

	function quick_review_log($user_Id) {
		$this->load->model('card');
		$user = $this->ion_auth->user($user_Id)->row();
		$data['user'] = $user->first_name.' '.$user->last_name;
		$data['user_Id'] = $user_Id;
		$data['all_user'] = $this->card->get_users();
		$this->load->view('quick_review_log', $data);
	}

	/* ajax library test function */
	function ajax_test() {
		$this->load->view('ajax_test');
	}

	/* load the cards for a given deck for a given user */
	function load_cards($user_id, $deck_id) {
		$cardArray = $this->card->load_cards($user_id, $deck_id);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}

	function load_cards_re($user_id, $deck_id) {
		$cardArray = $this->card->load_cards_re($user_id, $deck_id);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}

	function load_cards_quick($user_id, $deck_id) {
		$deckArray = $this->card->load_decks($userId);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($deckArray));
	}

	/* Load plus mode cards Ashvin Patel 19/jun/2014 */
	function load_plus_mode_cards() {
		$user_id = $this->input->post('user_id');
		$deck_id = $this->input->post('deck_id');
		$cards = $this->input->post('cards');
		if ($user_id != '' && $cards != '') {
			$cardArray = $this->card->load_plus_mode_cards($user_id, $deck_id, $cards);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($cardArray));
		}
	}

	function load_plus_mode_more_cards() {
		$user_id = $this->input->post('user_id');
		$deck_id = $this->input->post('deck_id');
		$cards = $this->input->post('cards');
		$multiplier = $this->input->post('elapsedTimeMultiplier');
		if ($user_id != '' && $deck_id != '' && $cards != '') {
			$cardArray = $this->card->load_more_cards($user_id, $deck_id, $cards, $multiplier);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($cardArray));
		}
	}

	/* load the card form multiple decks */
	function load_cards_md($user_id) {
		$deck_id_arr = json_decode($this->input->post('data'));
		$cardArray = $this->card->load_cards_md($user_id, $deck_id_arr);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}

	/*"fix change bug new cards marked wrong showing as - "*/
	function load_cards_md_play($user_id) {
		$deck_id_arr = json_decode($this->input->post('data'));
		$cardArray = $this->card->load_cards_md_play($user_id, $deck_id_arr);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}
	
	/*"fix change bug new cards marked wrong showing as - "*/
	function load_cards_md_re($user_id) {
		$deck_id_arr = json_decode($this->input->post('data'));
		$cardArray = $this->card->load_cards_md_re($user_id, $deck_id_arr);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}

	/*"add ability for user to create new decks and edit personal decks"*/
	function load_cards_md_sound($user_id) {
		$deck_id_arr = json_decode($this->input->post('data'));
		$cardArray = $this->card->load_cards_md_sound($user_id, $deck_id_arr);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($cardArray));
	}

	/* load the decks in the db */
	function load_decks($userId) {
		//echo "hello";
		//die();
		$deckArray = $this->card->load_decks($userId);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($deckArray));
	}

	/* Load Decks for plus mode parameters Ashvin Patel 19/jun/2014 */
	function load_plus_decks($userId) {
		$deckArray = $this->card->load_plus_decks($userId);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($deckArray));
	}

	function load_card_data() {
		$card_ids = $this->input->post('card_ids');
		$user_id = $this->input->post('user_id');
		//print_r($card_ids);
		if (!empty($card_ids)) {
			$deckArray = $this->card->loadCardData($card_ids, $user_id);
			//print_r($deckArray);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($deckArray));
		}
	}

	function load_plus_decks_more($userId) {
		$deckArray = $this->card->load_plus_decks_more($userId);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($deckArray));
	}

	/* load the decks in the db */
	function load_decks_id($userId){
		$deckArray = $this->card->load_decks($userId);
		$this->output->set_content_type('application/json');
			foreach ($deckArray as $dec_res) {
				$ids[] = $dec_res->deck_id;
			}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($ids));
	}

	/* load the decks with sound in the db */
	/*"add ability for user to create new decks and edit personal decks"*/
	function load_decks_sound_id($userId) {
		$ids = $this->card->load_cards_sound($userId);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($ids));
	}

	/* save card info back to the DB */
	function save_user_card() {
		/* Old Buggy Code - $data = json_decode($this->input->post('data'),TRUE,512); */
		$data = json_decode($this->input->post('data'), TRUE);
		$this->output->set_content_type('text/html');
		$this->card->save_user_card($data['record_id'], $data['history'], $data['test_history'], $data['rank'], $data['last_time'], $data['last_shown'], $data['wrong_twice_or_more_count'], $data['last_date']);
	}

	function save_user_card_re() {
		/* Old Buggy Code - $data = json_decode($this->input->post('data'),TRUE,512); */
		$data = json_decode($this->input->post('data'), TRUE);
		$this->output->set_content_type('text/html');
		$this->card->save_user_card_re($data['record_id'], $data['history'], $data['test_history'], $data['rank'], $data['last_time'], $data['last_shown'], $data['wrong_twice_or_more_count'], $data['last_date']);
	}

	/* edit review mode parameters */
	function edit_rm_params() {
		$this->form_validation->set_rules('maxNoShowTime', 'Min No Show Time', 'required|numeric');
		$this->form_validation->set_rules('minRepeatTime', 'Min Repeat Time', 'required|numeric');
		$this->form_validation->set_rules('rankInc', 'Rank Increment', 'required|numeric');
		$this->form_validation->set_rules('rankDesc', 'Rank Decrement', 'required|numeric');
		$this->form_validation->set_rules('correctCountForInc', 'Correct Count per increment', 'required|numeric');
		$this->form_validation->set_rules('wrongCountForDesc', 'Wrong Count per decrement', 'required|numeric');
		$this->form_validation->set_rules('avgExceedRankDesc', 'Average Exeed Rand Decrement', 'required|numeric');
		$this->form_validation->set_rules('avgExceedPercentage', 'Average Exceed presentage', 'required|numeric');
		$this->form_validation->set_rules('Q_AudioLoopResetInterval', 'Question Audio Loop Reset Interval', 'required|numeric');
		$this->form_validation->set_rules('A_AudioLoopResetInterval', 'Answer Audio Loop Reset Interval', 'required|numeric');
		if ($this->form_validation->run() == FALSE) {
			$rmParams = $this->card->load_rm_params();
			$rmParamArr = array();
			foreach ($rmParams as $row) {
				/* echo $row->param_name; */
				$rmParamArr[$row->param_name] = $row->value;
			}
			$this->load->view('review_param', $rmParamArr);
		} else {
			/* save data and lod form, give sucess msg as well */
			$data = array(
				'maxNoShowTime' => $this->input->post('maxNoShowTime'),
				'minRepeatTime' => $this->input->post('minRepeatTime'),
				'rankInc' => $this->input->post('rankInc'),
				'rankDesc' => $this->input->post('rankDesc'),
				'correctCountForInc' => $this->input->post('correctCountForInc'),
				'wrongCountForDesc' => $this->input->post('wrongCountForDesc'),
				'avgExceedRankDesc' => $this->input->post('avgExceedRankDesc'),
				'avgExceedPercentage' => $this->input->post('avgExceedPercentage'),
				'Q_AudioLoopResetInterval' => $this->input->post('Q_AudioLoopResetInterval'),
				'A_AudioLoopResetInterval' => $this->input->post('A_AudioLoopResetInterval')
			);
			$this->card->save_rm_params($data);
			$rmParams = $this->card->load_rm_params();
			$rmParamArr = array();
			$rmParamArr['sucess'] = "<p>Date Saved Sucessfully!</p>";
			foreach ($rmParams as $row) {
				/* echo $row->param_name; */
				$rmParamArr[$row->param_name] = $row->value;
			}
			$this->load->view('review_param', $rmParamArr);
		}
	}

	function edit_sm_params() {
		$this->form_validation->set_rules('maxNoShowTime', 'Min No Show Time', 'required|numeric');
		$this->form_validation->set_rules('minRepeatTime', 'Min Repeat Time', 'required|numeric');
		$this->form_validation->set_rules('rankInc', 'Rank Increment', 'required|numeric');
		$this->form_validation->set_rules('rankDesc', 'Rank Decrement', 'required|numeric');
		$this->form_validation->set_rules('correctCountForInc', 'Correct Count per increment', 'required|numeric');
		$this->form_validation->set_rules('wrongCountForDesc', 'Wrong Count per decrement', 'required|numeric');
		$this->form_validation->set_rules('avgExceedRankDesc', 'Average Exeed Rand Decrement', 'required|numeric');
		$this->form_validation->set_rules('avgExceedPercentage', 'Average Exceed presentage', 'required|numeric');
		$this->form_validation->set_rules('elapsedTimeMultiplier', 'Elapsed Time Multiplier', 'required|numeric');
		$this->form_validation->set_rules('variableOk', 'Variable Ok', 'required|numeric');
		if ($this->form_validation->run() == FALSE) {
			$smParams = $this->card->load_sm_params();
			$smParamArr = array();
			foreach ($smParams as $row) {
				/* echo $row->param_name; */
				$smParamArr[$row->param_name] = $row->value;
			}
			$this->load->view('supervised_param', $smParamArr);
		} else {
			/* save data and lod form, give sucess msg as well */
			$data = array(
				'maxNoShowTime' => $this->input->post('maxNoShowTime'),
				'minRepeatTime' => $this->input->post('minRepeatTime'),
				'rankInc' => $this->input->post('rankInc'),
				'rankDesc' => $this->input->post('rankDesc'),
				'correctCountForInc' => $this->input->post('correctCountForInc'),
				'wrongCountForDesc' => $this->input->post('wrongCountForDesc'),
				'avgExceedRankDesc' => $this->input->post('avgExceedRankDesc'),
				'avgExceedPercentage' => $this->input->post('avgExceedPercentage'),
				'elapsedTimeMultiplier' => $this->input->post('elapsedTimeMultiplier'),
				'variableOk' => $this->input->post('variableOk')
			);
			$this->card->save_sm_params($data);
			$smParams = $this->card->load_sm_params();
			$smParamArr = array();
			$smParamArr['sucess'] = "<p>Date Saved Sucessfully!</p>";
			foreach ($smParams as $row) {
				/* echo $row->param_name; */
				$smParamArr[$row->param_name] = $row->value;
			}
			$this->load->view('supervised_param', $smParamArr);
		}
	}

	/* load review mode params */
	function load_rm_params() {
		//   $mode=$_GET['mode'];
		//  $mode=var_dump($lang);
		//   echo $mode;
		$rmParamArray = $this->card->load_rm_params();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($rmParamArray));
	}

	function load_stst_params() {
		$rmParamArray = $this->card->load_stst_params();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($rmParamArray));
	}

	/* Load Supervied plus mode parameters Ashvin Patel 19/jun/2014 */
	function load_stst_plus_params() {
		$rmParamArray = $this->card->load_stst_plus_params();
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($rmParamArray));
	}

	/* delete decks */
	function delete_decks($deckId) {
		/* check whether the admin */
		if (!$this->ion_auth->is_admin()) {
			/* redirect(''); */
		}
	
		if (isset($deckId)) {
			if ($deckId >= 0) {
				$this->db->where('deck_id', $deckId);
				$this->db->delete('user_card');
				$this->db->where('deck_id', $deckId);
				$this->db->delete('card_deck');
			}
		}
		/* load all decks */
		$allDecksArr = $this->card->load_decks(-1);
		$allDecks = array();
		$allDecks['allDecks'] = $allDecksArr;
		$this->load->view('delete_decks', $allDecks);
	}

	/*"add ability for user to create new decks and edit personal decks"*/	/* check user modify card deck */
	function user_delete_decks1($deckId,$userId) {
		
		/* check whether the admin */
		if (!$this->ion_auth->is_admin()) {
			/* redirect(''); */
		}
	
		if (isset($deckId)) {
			if ($deckId >= 0) {
				$this->db->where('deck_id', $deckId);
				$this->db->delete('user_card');
				$this->db->where('deck_id', $deckId);
				$this->db->delete('card_deck');
			}
		}
		/* load all decks */
		$allDecksArr = $this->card->load_decks_modify($userId);
		$allDecks = array();
		$allDecks['allDecks'] = $allDecksArr;
		$this->load->view('delete_decks', $allDecks);
	}

	function user_delete_decks($deckId,$userId) {
		
		/* check whether the admin */
		if (!$this->ion_auth->is_admin()) {
			/* redirect(''); */
		}
	
		if (isset($deckId)) {
			if ($deckId >= 0) {
				$this->db->where('deck_id', $deckId);
				$this->db->delete('user_card');
				$this->db->where('deck_id', $deckId);
				$this->db->delete('card_deck');
			}
		}
		/* load all decks */
		$allDecksArr = $this->card->load_decks($userId);
		$allDecks = array();
		$allDecks['allDecks'] = $allDecksArr;
		$this->load->view('delete_decks', $allDecks);
	}

	function update_cards() {
		/* check whether the admin */
		if (!$this->ion_auth->logged_in()) {
			echo 'You are not the admin';
		} else {
			$errormsg = '';
			$user = $this->ion_auth->user()->row();
			$decks = $this->input->post('cards');
			if (!isset($decks['deck_name']) || ($decks['deck_name'] == '')) {
				$errormsg.=" Deck Name not entered ";
			}
			if (isset($decks['items'])) {
			

				foreach ($decks['items'] AS $item) {
				
	
					if ($item['action'] != 'delete') {
						if (!isset($item['question']) || $item['question'] == '') {
							$errormsg.=" One Question not entered ";
						}
						if (!isset($item['answer']) || $item['answer'] == '') {
							$errormsg.=" One Answer not entered ";
						}
					}
				}
			} else {
				$errormsg = " No questions or answers in Table ";
			}
			
			if (!$errormsg) {
				$this->load->model('card');
				
				
				$cards = $this->card->updateCardsInDeck($decks, $user->id);
				if ($cards != 1) {
					echo "error";
				}
				else {
					echo 'Cards successfully updated';
				}
			} else {
				echo $errormsg;
			}
		}
		/* load the game view */
	}

	function add_cards() {
		/* check whether the admin */
		if (!$this->ion_auth->logged_in()) {
			echo 'You are not the admin';
		} else {
			$errormsg = "";
			$user = $this->ion_auth->user()->row();
			$decks = $this->input->post('cards');
			if (!isset($decks['deck_name']) || ($decks['deck_name'] == '')) {
				$errormsg.=" Deck Name not entered ";
			}
			if (isset($decks['items'])) {
				foreach ($decks['items'] AS $item) {
					if (!isset($item['question']) || $item['question'] == '') {
						$errormsg.=" One Question not entered ";
					}
					if (!isset($item['answer']) || $item['answer'] == '') {
						$errormsg.=" One Answer not entered ";
					}
				}
			} else {
				$errormsg .= " No questions or answers in Table ";
			}
			if ($errormsg == "") {
				$this->load->model('card');
				$cards = $this->card->addCardsInDeck($decks, $user->id);
				if ($cards != 1)  {
					echo "error";
				}
			} else {
				echo $errormsg;
			}
		}
		/* load the game view */
	}

	function edit_decks($deckId) {
		/* check whether the admin */
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		/* load the game view */
		$this->load->model('card');
		$cards = $this->card->getCompleteCardSet($deckId);
		$cards['complete_cards'] = $cards;
		// $cards=$this->object_to_array($cards);
		if (count($cards) > 0) {
			$cards['deck_name'] = $cards[0]->deck_name;
			$cards['deck_id'] = $cards[0]->deck_id;
		}
		//  print_r($cards->result_array());
		$cards["allCards"] = $cards;
		// $data['title'] = 'a';
		//  $data['body'] = 'b';
		$this->load->view('edit_decks', $cards);
//			$allDecksArr = $this->card->load_decks(-1);
//			$allDecks = array();
//			$allDecks['allDecks'] = $allDecksArr;
//			$this->load->view('edit_decks');
	}

	function object_to_array($object) {
		if (is_object($object)) {
			// Gets the properties of the given object with get_object_vars function
			$object = get_object_vars($object);
		}
		return (is_array($object));
	}

	function upload_sound() {
		$status = "";
		$msg = "";
		if (empty($_POST['id']) || empty($_POST['type']) || empty($_POST['data'])) {
			if (empty($_POST['id'])) {
				$empty = 'id';
			} elseif (empty($_POST['type'])) {
				$empty = 'type';
			} elseif (empty($_POST['data'])) {
				$empty = 'data';
			}
			$status = "error";
			$msg = $empty." not passed";
		} else {
			$name_id = $_POST['id'];
			$name_type = $_POST['type'];
			$data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
			$decodedData = base64_decode($data);
			$file_element_name = $name_type . '_' . $name_id . ".mp3";
		}
		if ($status != "error") {
			$fp = fopen('./sound-files/'.$file_element_name, 'wb');
			$fwrite = fwrite($fp, $decodedData);
			if ($fwrite == true) {
				$status = "success";
				$msg = "File successfully uploaded_-_-0909//^%*(" . $file_element_name;
			}
			fclose($fp);
		}
		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	function upload_sound_from_cerate() {
		$status = "";
		$msg = "";
		$file_element_name = "file_name_" . $id;
		if ($status != "error") {
			$config['upload_path'] = './sound-files/';
			$config['allowed_types'] = 'mp3';
			$config['max_size'] = 1024 * 8;
			$config['encrypt_name'] = FALSE;
			$this->load->library('upload', $config);
			if (!($this->upload->do_upload($file_element_name))) {
				$status = 'error';
				$msg = $this->upload->display_errors('', '');
			} else {
				//	  $new_file_name = date("dmyhis")."_". rand( 100 , 999 )."_". rand( 100 , 999 );
				$data = $this->upload->data();
				// $file_id = $this->files_model->insert_file($new_file_name, $_POST['id']);
				//   if($file_id)
				//   {
				$status = "success";
				$msg = "File successfully uploaded_-_-0909//^%*(" . $data['file_name'];
				//	}
				//	else
				//	 {
//			unlink($data['full_path']);
//			$status = "error";
//			$msg = "Something went wrong when saving the file, please try again.";
				//	 }
			}
			@unlink($_FILES[$file_element_name]);
		}
		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	function quick_with_sound($userId) {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}

		$this->load->view('quick_with_sound');
	}

	function quick_reverse_with_sound($userId) {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}

		$this->load->view('quick_reverse_with_sound');
	}


	function quick_reverse_with_sound_show_all($userId) {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}

		$this->load->view('quick_reverse_with_sound_show_all');
	}

	function rw_with_sound() {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$this->load->view('rw_with_sound');
	}

	function rw_input() {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$this->load->view('rw_input');
	}

	function self_test() {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$this->load->view('self_test');
	}

	function reverse_with_sound() {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$this->load->view('reverse_with_sound');
	}

	function reverse_with_sound_show_all($userId,$deckId) {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$data['userId'] = $userId;
		$data['deckId'] = $deckId;
		$this->load->view('reverse_with_sound_show_all',$data);
	}

	/* Load Supervised plus test mode Ashvin Patel 19/jun/2014 */
	function supervised_mode_plus() {
		if (!$this->ion_auth->logged_in()) {
			redirect('');
		}
		$this->load->view('supervised_mode_plus');
	}

	function deleteFileOnEdit() {
		$file = $_POST['upload_file'];
		$id = $_POST['id'];
		$type = $_POST['type'];
		$base_url = base_url();
		$path_to_file = "./sound-files/" . $file;
		$this->load->model('card');
		$cards = $this->card->updateCardUrlInDeck($id, $type);
		if ($cards == 1) {
			unlink($path_to_file);
			echo "File deleted successfully";
		} else {
			echo "error";
		}
	}
	function deleteFileOnEditSlow() {
		$file = $_POST['upload_file'];
		$id = $_POST['id'];
		$type = $_POST['type'];
		$base_url = base_url();
		$path_to_file = "./sound-files/" . $file;
		$this->load->model('card');
		$cards = $this->card->updateCardUrlInDeckSlow($id, $type);
		if ($cards == 1) {
			unlink($path_to_file);
			echo "File deleted successfully";
		} else {
			echo "error";
		}
	}

	function deleteFile() {
		$file = $_POST['upload_file'];
		$base_url = base_url();
		$path_to_file = "./sound-files/" . $file;
		if (!unlink($path_to_file)) {
			echo "error";
		}
		else {
			echo "File deleted successfully";
		}
	}

	function enter_password() {
		$this->load->view('enter_password');
	}

	/* Count all for user ASHVIN PATEL 21/JUN/2014 */
	/* function countAllCards($userId){
	  $this->card->getAllCardsCount($userId);
	  } */

	/* Count all for user ASHVIN PATEL 21/JUN/2014 */
	function userGroups($userId) {
		$this->card->getUserDeck($userId);
	}

	function deck_report($user_Id) {
		$this->load->model('card');
		$cards = array();
		$user = $this->ion_auth->user($user_Id)->row();
		$cards['user'] = $user->first_name.' '.$user->last_name;
		$cards['all_user'] = $this->card->get_users();
		$cards["allCards"] = $this->card->getCompleteGameSessions($user_Id);
		//  $cards['complete_cards']=$cards;
		// $cards=$this->object_to_array($cards);
		//  print_r($cards->result_array());
		//   $cards["allCards"]=$cards;
		// $data['title'] = 'a';
		//  $data['body'] = 'b';
		
		$this->load->view('deck_report', $cards);
	}

	function load_stst_plus_previous_result() {
		if ($this->input->post('userid')) {
			$user_Id = $this->input->post('userid');
			$this->load->model('card');
			$cards = array();
			$cards["allCards"] = $this->card->getCompleteGameSessions($user_Id);
			$cards['cardCount'] = $this->card->neverTestedCards($user_Id);
			$this->load->view('deck_report_plus_mode', $cards);
		}
	}

	function error() {
		$this->load->model('card');
		$username = $_GET['username'];
		$cards = array();
		$cards["allCards"] = $this->card->getAllErrors($username);
		$this->load->view('error', $cards);
	}

	function error_self_test() {
		$this->load->model('card');
		$username = $_GET['username'];
		$cards = array();
		$cards["allCards"] = $this->card->getAllErrorsSelft($username);
		$this->load->view('error_self_test', $cards);
	}
}
?>