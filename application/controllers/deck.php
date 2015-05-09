<?php
include 'PHPExcel/IOFactory.php';  


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
		if($deckName != null && !empty($deckNameTst)) {
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
		//			$saveStatus = $this->card->save_deck($deckName, $qans, $user->id);
					if ($saveStatus) {
						$error = array('error' => 'Card Deck Saved Sucessfully!');
		//				$this->load->view('new_deck', $error);
					} else {
						$error = array('error' => 'The card deck name allready exist!, User a new name.');
		//				$this->load->view('new_deck', $error);
					}
				} else {
					$error = array('error' => 'Unable to open the uploded question file! Please Try again!');
		//			$this->load->view('new_deck', $error);
				}
			}
		} else {
			$error = array('error' => 'Please specify a card deck name, to create a new card deck!');
		//	$this->load->view('new_deck', $error);
		}
	}


	function new_deck_upload() {
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'txt|xls';
		$config['max_size'] = '1000';
		$this->load->library('upload', $config);
		$deckName = $this->input->post('deck_name', FALSE);
		$deckNameTst = $deckName . "";
		 
		$deckNameTst = preg_replace('/\s+/', '', $deckNameTst);
		
		if($deckName != null && !empty($deckNameTst)) 
		{
			
			$user = $this->ion_auth->user()->row();
						
			if(isset($_POST) && !empty($_FILES['userfile']['name']))
			{
				$namearr = explode(".",$_FILES['userfile']['name']);

				$invalid = 0;
				if(end($namearr) == 'xls' || end($namearr) == 'xlsx')
				{
					$invalid = 1;
				}
				else if(end($namearr) == 'txt')
				{
					$invalid = 2;
				}
				if($invalid == 1)
				{
					$response = move_uploaded_file($_FILES['userfile']['tmp_name'],$_FILES['userfile']['name']); // Upload the file to the current folder
					if($response)
					{
						try
						{
							$objPHPExcel = PHPExcel_IOFactory::load($_FILES['userfile']['name']);
						} 
						catch(Exception $e) 
						{
							die('Error : Unable to load the file : "'.pathinfo($_FILES['userfile']['name'],PATHINFO_BASENAME).'": '.$e->getMessage());
						}
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						$arrayCount = count($allDataInSheet);  // Total Number of rows in the uploaded EXCEL file
						
						$qans = array();	
						
						for($i=1;$i<=$arrayCount;$i++)
						{
							$tmpQans0 = trim($allDataInSheet[$i]["A"]);
							$tmpQans1 = trim($allDataInSheet[$i]["B"]);
							$tmpQans2 = trim($allDataInSheet[$i]["C"]);
							$tmpQans3 = trim($allDataInSheet[$i]["D"]);
							$tmpQans4 = trim($allDataInSheet[$i]["E"]);
							$tmpQans5 = trim($allDataInSheet[$i]["F"]);
							$tmpQans6 = trim($allDataInSheet[$i]["G"]);
							$tmpQans7 = trim($allDataInSheet[$i]["H"]);
							
							$qans[] = array(
										"q" => $tmpQans0,
										"qn" => $tmpQans1,
										"a" => $tmpQans2,
										"an" => $tmpQans3,
										"qf" => $tmpQans4,
										"qnf" => $tmpQans5,
										"af" => $tmpQans6,
										"anf" => $tmpQans7,
									);
						}
						
						
						
							$saveStatus = $this->card->save_deck_upload($deckName, $qans, $user->id);
							if ($saveStatus) {
								$error = array('error' => 'Card Deck Saved Sucessfully!');
								$this->load->view('new_deck', $error);
							} else {
								$error = array('error' => 'The card deck name allready exist!, User a new name.');
								$this->load->view('new_deck', $error);
							}
					}
				}
				else if($invalid == 2)
				{
										
					if (!$this->upload->do_upload()) {
						$error = array('error' => $this->upload->display_errors());
						$this->load->view('new_deck', $error);
					} else {
						$uploadData = $this->upload->data();
						$uplodedFile = $uploadData['full_path'];
						
						$file = fopen($uplodedFile, "r");
						if ($file) {
							//Output a line of the file until the end is reached
							$qans = array();
							while (!feof($file)) {
								$line = fgets($file);
								$tmpQans = explode('||', $line, 4);
								
								$arrLength = sizeof($tmpQans);
								if ($arrLength == 4) {
									$qans[] = array(
												"q" => $tmpQans[0],
												"qn" => $tmpQans[1],
												"a" => $tmpQans[2],
												"an" => $tmpQans[3],
												"qf" => "",
												"qnf" => "",
												"af" => "",
												"anf" => ""
												
											);
								}
							}

							fclose($file);
							//save the deck and print a sucess message
							
							$saveStatus = $this->card->save_deck_upload($deckName, $qans, $user->id);
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
				
					
				}
			} 

		} else {
			$error = array('error' => 'Please specify a card deck name, to create a new card deck!');
			$this->load->view('new_deck', $error);
		}
	}

}

?>