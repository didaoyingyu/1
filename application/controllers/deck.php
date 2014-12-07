<?php

class Deck extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('card');
	}

	function create_deck() {
		$this->load->view('/deck/create_deck', array('error' => ' '));
	}

	function index() {
		$this->load->view('new_deck', array('error' => ' '));
	}

	function new_deck() {
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'txt';
		$config['max_size'] = '1000';
		$this->load->library('upload', $config);
		$deckName = $this->input->post('deck_name', FALSE);
		$deckNameTst = $deckName . "";
		$deckNameTst = preg_replace('/\s+/', '', $deckNameTst);
		if ($deckName != null && !empty($deckNameTst)) {
			if (!$this->upload->do_upload()) {
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('new_deck', $error);
			} else {
				$uploadData = $this->upload->data();
				$uplodedFile = $uploadData['full_path'];
				/* echo $uplodedFile."<br>"; */
				/* load the deck */
				$file = fopen($uplodedFile, "r");
				if ($file) {
					//Output a line of the file until the end is reached
					$qans = array();
					while (!feof($file)) {
						$line = fgets($file);
						$tmpQans = explode('||', $line, 2);
						$arrLength = sizeof($tmpQans);
						if ($arrLength == 2) {
							$qans[$tmpQans[0]] = $tmpQans[1];
						}
					}
					fclose($file);
					//save the deck and print a sucess message
					$user = $this->ion_auth->user()->row();
					$saveStatus = $this->card->save_deck($deckName, $qans, $user->id);
					if ($saveStatus) {
						$error = array('error' => 'Card Deck Saved Sucessfully!');
						$this->load->view('new_deck', $error);
					} else {
						$error = array('error' => 'The card deck name allready exist!, User a new name.');
						$this->load->view('new_deck', $error);
					}
				} else {
					$error = array('error' => 'Unable to open the uploded question file! Please Try again!');
					$this->load->view('new_deck', $error);
				}
			}
		} else {
			$error = array('error' => 'Please specify a card deck name, to create a new card deck!');
			$this->load->view('new_deck', $error);
		}
	}

}

?>