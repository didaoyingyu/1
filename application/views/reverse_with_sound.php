<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game-Sound</title>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
		<!-- Ulatimate Ajax Stript info: http://www.hunlock.com/blogs/The_Ultimate_Ajax_Object -->
		<script type="text/javascript" src="<?php echo base_url() ?>js/uajax.js"></script>
		<!-- Card handling logic -->
		<script type="text/javascript" src="<?php echo base_url() ?>js/deckHandler.js"></script>
		<!-- Game JS -->
		<script>
			/*******Common JS varible Section***************/
			var userId = <?php echo $this->ion_auth->user()->row()->id ?>;
			var deckId = 1;
			var cardArray = null;
			var deckHander = new DeckHandler();
			var currentCard;
			var gameMode = 'TR'; /*default mode is training*/
			/*variables needed by ajax calls*/
			var loadGameResponse;
			var loadGameResponseStatus;
			var loadGameResponseMd;
			var loadGameResponseStatusMd;
			var saveCardResponse;
			var saveCardResponseStatus;
			var loadDecksResponse;
			var loadDecksResponseStatus;
			/*variable for timer function*/
			var totalSeconds = 0;
			var questionTimeDiv;
			var timerIntervalId;
			/*game history length */
			var historyLength = 30;
			/*reviw mode game parameters*/
			var loadRmParamsResponse;
			var loadRmParamsResponseStatus;
			/*current deck array*/
			var currentDeckArray;
			var pre_cards = new Array();
			var z_count = 1;
			var game_results = new Object();
			var quick_review_log = new Object();
			var deck_count = 0;
			var total_time_for_deck = 0;
			var change_minus = 0;
			game_results['correct_total'] = new Object();
			game_results['wrong_total'] = new Object();
			game_results['deck'] = new Object();
			game_results['card_count'] = new Object();
			//	var game_results['user_id'] = new Object();
			var current_user_id = 0;
			var game_count = 0;
			var correct_total = 0;
			var wrong_total = 0;
			var first_time_card_count = 0;
			var first_time_correct_Card_cout = 0;
			var total_cards = 0;
			/*******encode JSON objects for POST************/
			function preparePost(jsonObj) {
				return "data=" + JSON.stringify(jsonObj).replace(/&/g, "%26");
			}
			/*******JS to contral main game Flow************/
			function startGame() {
				/*hide unanted screens*/
				document.getElementById("gameScreen").style.display = "none";
				document.getElementById("cardDeckSelectionScreen").style.display = "none";
				document.getElementById("gameModeScreen").style.display = "block";
			}
			/*******Set Game Mode and Load Card Decks************/
			function setGameModeAndLoadDecks(gameModeIn) {
				gameMode = gameModeIn;
				setGameModeParams();
			}
			/********Set Game Mode Parameters*******************/
			function setGameModeParams() {
				if (gameMode == 'TR') {
					loadDecks();
				} else if (gameMode == 'RW') {
					loadReviewModeParams();
				}
			}
			function loadReviewModeParams() {
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_rm_params/";
				var myRequest = new ajaxObject(loadDecksAjaxPath, loadReviewModeParamsHandler, loadRmParamsResponse, loadRmParamsResponseStatus);
				myRequest.update();
			}
			function loadReviewModeParamsHandler(loadRmParamsResponse, loadRmParamsResponseStatus) {
				if (loadRmParamsResponseStatus == 200) {
					var responseCleaned = loadRmParamsResponse.split(']')[0] + ']';
					reviwModeParams = eval('(' + responseCleaned + ')');
					console.log(reviwModeParams);
					for (var i = 0; i < reviwModeParams.length; i++) {
						if (reviwModeParams[i]['param_name'] == 'minRepeatTime') {
							minRepeatTime = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'maxNoShowTime') {
							maxNoShowTime = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'rankInc') {
							rankInc = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'rankDesc') {
							rankDesc = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'correctCountForInc') {
							correctCountForInc = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'wrongCountForDesc') {
							wrongCountForDesc = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'avgExceedRankDesc') {
							avgExceedRankDesc = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'avgExceedPercentage') {
							avgExceedPercentage = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'Q_AudioLoopResetInterval') {
							Q_AudioLoopResetInterval = parseInt(reviwModeParams[i]['value']);
						} else if (reviwModeParams[i]['param_name'] == 'A_AudioLoopResetInterval') {
							A_AudioLoopResetInterval = parseInt(reviwModeParams[i]['value']);
						}
					}
					/*start game*/
					loadDecks();
				} else {
					alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
				}
			}
			/********Load Card Decks****************************/
			function loadDecks() {
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_decks/<?php echo $this->ion_auth->user()->row()->id ?>";
						var myRequest = new ajaxObject(loadDecksAjaxPath, loadDecksHandler, loadDecksResponse, loadDecksResponseStatus);
						myRequest.update();
					}
					function loadDecksHandler(loadDecksResponse, loadDecksResponseStatus) {
						if (loadDecksResponseStatus == 200) {
							var responseCleaned = loadDecksResponse.split(']')[0] + ']';
							deckArray = eval('(' + responseCleaned + ')');
							currentDeckArray = deckArray;
							document.getElementById("cardDeckSelectionScreen").style.display = "block";
							document.getElementById("gameModeScreen").style.display = "none";
							/*render deck selection view*/
							renderDeckSelection(deckArray);
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
						}
					}
					function renderDeckSelection(deckArray) {
						var innerHtml = "";
						for (var i = 0; i < deckArray.length; i++) {
							innerHtml = innerHtml + "<div class='buttonHolder1'><div class='buttonHolder1'><div class='button green' onclick='loadGame(" + deckArray[i]['deck_id'] + ")'><p>" + deckArray[i]['deck_name'] + "</p></div></div></div><br/><br/><br/>";
						}
						/*for multiple deck mode*/
						innerHtml = innerHtml + "<div class='buttonHolder1'><div class='buttonHolder1'><div class='button green' onclick='loadGameMultiDeckMode()'><p>Play Multiple Decks</p></div></div></div><br/><br/><br/>";
						document.getElementById("cardDeckSelectionScreen").innerHTML = innerHtml;
					}
					function renderDeckMultiSelection(deckArray) {
						var innerHtml = "";
						for (var i = 0; i < deckArray.length; i++) {
							innerHtml = innerHtml + "<p><input type='checkbox' id='chk_" + deckArray[i]['deck_id'] + "' name='" + deckArray[i]['deck_id'] + "'>" + deckArray[i]['deck_name'] + "</p><br/>";
						}
						/*play multiple deck mode*/
						innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='playMultiDeckMode()'><p>Play</p></div></div></div><br/><br/><br/>";
						document.getElementById("cardDeckSelectionScreen").innerHTML = innerHtml;
					}
					/*******Load/save game section********/
					function loadGame(deckIdIn) {
						card_ids = new Array();
						pre_cards = new Array();
						game_results = new Object();
						game_results['correct_total'] = new Object();
						game_results['wrong_total'] = new Object();
						game_results['deck'] = new Object();
						game_results['card_count'] = new Object();
						deckId = deckIdIn;
						var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_cards_re/" + userId + "/" + deckId;
						var myRequest = new ajaxObject(loadGameAjaxPath, loadGameHandler, loadGameResponse, loadGameResponseStatus);
						myRequest.update();
					}
					
					function loadGameHandler(loadGameResponse, loadGameResponseStatus) {
						if (loadGameResponseStatus == 200) {
							var responseCleaned = loadGameResponse.split(']')[0] + ']';
							cardArray = eval('(' + responseCleaned + ')');
							/*add two extra varibles to cards*/
							for (var i = 0; i < cardArray.length; i++) {
								cardArray[i]['correct'] = 0;
								cardArray[i]['wrong'] = 0;
							}
							
							document.getElementById("deckIds").value = deckId;
						
							deckHander.setDeck(cardArray);
							document.getElementById("gameScreen").style.display = "block";
							document.getElementById("gameModeScreen").style.display = "none";
							document.getElementById("cardDeckSelectionScreen").style.display = "none";
							$(".fcardStage").css({"display":"block"});
							$(".fcardQues").css({"display":"none"});
							//showNextQues();
							
							
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
						}
					}
					function saveCard(card) {
						var saveCardAjaxPath = "<?php echo base_url() ?>index.php/game/save_user_card_re";
						var myRequest = new ajaxObject(saveCardAjaxPath, saveCardHandler, saveCardResponse, saveCardResponseStatus);
						/*set last shown time*/
						var time = new Date();
						var timeMils = time.getTime();
						card['last_shown'] = timeMils;
//				console.log("Saving Card: " + card['card_id']);
//				console.log("\tRecord ID:" + card['record_id'] + ", User Id:" + card['user_id']);
//				console.log("\tQuestion:" + card['question']);
//				console.log("\tCard Rank: " + card['rank']);
						myRequest.update(preparePost(card), 'POST');
					}
					function saveCardHandler(saveCardResponse, saveCardResponseStatus) {
						if (saveCardResponseStatus == 200) {
							//no problem
							//alert(JSON.stringify(saveCardResponse));
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
						}
					}
					/*********Load Game multiple deck mode*********************/
					function loadGameMd(deckIds) {
						var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_cards_md/" + userId;
						var myRequest = new ajaxObject(loadGameAjaxPath, loadGameHandlerMd, loadGameResponseMd, loadGameResponseStatusMd);
						myRequest.update(preparePost(deckIds), "POST");
					}
					function loadGameHandlerMd(loadGameResponseMd, loadGameResponseStatusMd) {
						if (loadGameResponseStatusMd == 200) {
							cardArray = JSON.parse(loadGameResponseMd);
							/*add two extra varibles to cards*/
							for (var i = 0; i < cardArray.length; i++) {
								cardArray[i]['correct'] = 0;
								cardArray[i]['wrong'] = 0;
							}
							console.log(cardArray);
							deckHander.setDeck(cardArray);
							document.getElementById("gameScreen").style.display = "block";
							document.getElementById("gameModeScreen").style.display = "none";
							document.getElementById("cardDeckSelectionScreen").style.display = "none";
							showNextQues();
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatusMd);
						}
					}
					/*********Load game mulit deck mode************************/
					function loadGameMultiDeckMode() {
						renderDeckMultiSelection(currentDeckArray);
					}
					/********Play game multi deck mode*************************/
					function playMultiDeckMode() {
						/*check the selected decks*/
						var selected = false;
						var decks = "";
						for (var i = 0; i < currentDeckArray.length; i++) {
							if (document.getElementById("chk_" + currentDeckArray[i]['deck_id']).checked) {
								decks = decks + currentDeckArray[i]['deck_id'] + "_";
								selected = true;
							}
						}
						/*load and play the game*/
						if (selected) {
							loadGameMd(decks);
						} else {
							alert("You have atleast, select one deck to play!")
						}
					}
					/*********User button clicking event handling**************/
					function showNextQues() {
						$("#source_div_a").html("");
						$("#source_div_q").html("");
						$("#source_div_a_slow").html("");
						$("#source_div_q_slow").html("");
						currentCard = deckHander.getNextCard(gameMode);
						game_results['deck'][game_count] = new Object();
						//console.log(game_results['deck'][game_count]['deck_id'] = currentCard['deck_id']);
						game_results['deck'][game_count]['deck_id'] = currentCard['deck_id'];
						game_results['deck'][game_count]['card_id'] = currentCard['card_id'];
						game_results['deck'][game_count]['history'] = currentCard['history'];
						var base_url = '<?php echo base_url(); ?>';
						var question_upload_file = currentCard['question_upload_file'];
						var answer_upload_file = currentCard['answer_upload_file'];
						var question_upload_file_slow = currentCard['question_upload_file_slow'];
						var answer_upload_file_slow = currentCard['answer_upload_file_slow'];
						
						$("#source_div_q").html("<audio id='player_a'><source id='sorce_id_a' type='audio/mpeg' src='" + base_url + "/sound-files/" + answer_upload_file + "'></audio>");
						$("#source_div_a").html("<audio id='player_q'><source id='sorce_id_q' type='audio/mpeg' src='" + base_url + "/sound-files/" + question_upload_file + "'></audio>");
						$("#sorce_id_q").attr("src", base_url + "/sound-files/" + currentCard['answer_upload_file']);
						$("#sorce_id_a").attr("src", base_url + "/sound-files/" + currentCard['question_upload_file']);
						
						$("#source_div_q_slow").html("<audio id='player_a_slow'><source id='sorce_id_a_slow' type='audio/mpeg' src='" + base_url + "/sound-files/" + answer_upload_file_slow + "'></audio>");
						$("#source_div_a_slow").html("<audio id='player_q_slow'><source id='sorce_id_q_slow' type='audio/mpeg' src='" + base_url + "/sound-files/" + question_upload_file_slow + "'></audio>");
						$("#sorce_id_q_slow").attr("src", base_url + "/sound-files/" + currentCard['answer_upload_file_slow']);
						$("#sorce_id_a_slow").attr("src", base_url + "/sound-files/" + currentCard['question_upload_file_slow']);
						
						flipBack();
						document.getElementById('player_q').play();
						var first_play = 0;
						window.loop = 	function(){
											window.loop_q = setTimeout(function(){
												first_play++;
												console.log(Q_AudioLoopResetInterval+" "+first_play);
												
												if(first_play < 0)
												{
													console.log(0);
												}
												else
												{	
													console.log(1);
													document.getElementById('player_q_slow').play();
												}
											}, Q_AudioLoopResetInterval);
										};
										
						document.getElementById('player_q').addEventListener("ended",loop);
						document.getElementById('player_q_slow').addEventListener("ended",loop);
						
						
						var avgTime = 0;
						if (parseInt(currentCard['play_count']) != 0) {
							avgTime = currentCard['total_time'] / currentCard['play_count'];
						}
						renderQuestion(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), currentCard['answer'], currentCard['answer_note'], currentCard['question']);
						quick_review_log['before_history'] = game_results['deck'][game_count]['history'];
						quick_review_log['reason'] = extraInfo.innerHTML;
						quick_review_log['before_rank'] = currentCard['rank'];
						quick_review_log['question'] = currentCard['question'];
						quick_review_log['answer'] = currentCard['answer'];
						quick_review_log['deck_id'] = currentCard['deck_id'];
						quick_review_log['card_id'] = currentCard['card_id'];
						quick_review_log['utp'] = currentCard['utp'];
						quick_review_log['utp'] = '';
						new ajaxObject("<?php echo base_url() ?>index.php/auth/get_log_utp_re", function(res) {
							quick_review_log['utp'] = res;
						}).update(preparePost(quick_review_log), "POST");
					}
					function showAns() {						
						flip();
						
						document.getElementById('player_q').removeEventListener("ended", loop);
						document.getElementById('player_q_slow').removeEventListener("ended", loop);
						if(typeof loop_q !== "undefined"){
							clearTimeout(loop_q);
						}
						document.getElementById('player_q').pause();
						document.getElementById('player_q_slow').pause();
						document.getElementById('player_a').play();
						var first_play = 0;
						window.loop = 	function(){
											window.loop_q = setTimeout(function(){
												first_play++;
												console.log(A_AudioLoopResetInterval+" "+first_play);
												
												if(first_play < 0)
												{
													console.log(0);
												}
												else
												{	
													console.log(1);
													document.getElementById('player_a_slow').play();
												}
											}, A_AudioLoopResetInterval);
										};
										
						document.getElementById('player_a').addEventListener("ended",loop);
						document.getElementById('player_a_slow').addEventListener("ended",loop);
						/*stop the time up timer and get it value*/
		
						clearInterval(timerIntervalId);
						currentCard['last_time'] = totalSeconds;
						var timeTakenForQues = getFormatedTime(totalSeconds);
						var avgTime = 0;
						if (parseInt(currentCard['play_count']) != 0) {
							avgTime = currentCard['total_time'] / currentCard['play_count'];
						}
						totalSeconds = 0;
						renderAnswer(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), timeTakenForQues, currentCard['question'], currentCard['question_note'], currentCard['answer']);
					}
					function markAnswer(mark) {
						document.getElementById('player_a').removeEventListener("ended", loop);
						document.getElementById('player_a_slow').removeEventListener("ended", loop);
						if(typeof loop_a !== "undefined"){
							clearTimeout(loop_a);
						}
						document.getElementById('player_a').pause();
						document.getElementById('player_a_slow').pause();
						
						var isValid = mark > 0;
						console.log("isValid "+isValid+ " mark="+mark);
						deckHander.handleCardStatus(currentCard, mark, gameMode, historyLength);
						total_cards++;
						game_results['deck'][game_count]['ans'] = isValid;
						game_results['deck'][game_count]['rank'] = currentCard['rank'];
						game_results['deck'][game_count]['reason'] = extraInfo.innerHTML;
					<?php if ($this->ion_auth->user()->row()->review_log_status == 0) {?>
						quick_review_log['ans'] = isValid;
						quick_review_log['after_history'] = currentCard['history'];
						quick_review_log['test_history'] = currentCard['test_history'];
						quick_review_log['after_rank'] = currentCard['rank'];
						new ajaxObject("<?php echo base_url() ?>index.php/auth/save_quick_review_log_re", 
							function(res, status) {
								if (status != 200) {
									alert('Quick review log save failed!\n' + res);
								}
							}).update(preparePost(quick_review_log), "POST");
					<?php }?>
						game_count++;
						if (isValid) {
							correct_total++;
						} else {
							wrong_total++;
						}
						saveCard(currentCard);
						showNextQues();
					}
					function finishGame() {
						/* show the deck selection screen */
						document.getElementById('player_a').removeEventListener("ended", loop);
						document.getElementById('player_a_slow').removeEventListener("ended", loop);
						if(typeof loop_a !== "undefined"){
							clearTimeout(loop_a);
						}
						document.getElementById('player_a').pause();
						document.getElementById('player_a_slow').pause();
						document.getElementById('player_q').removeEventListener("ended", loop);
						document.getElementById('player_q_slow').removeEventListener("ended", loop);
						if(typeof loop_q !== "undefined"){
							clearTimeout(loop_q);
						}
						document.getElementById('player_q').pause();
						document.getElementById('player_q_slow').pause();
						
						game_results['card_count'] = total_cards;
						total_cards = 0;
						clearInterval(timerIntervalId);
						if (confirm("Do you really want to finish this game?")) {
							var base_url = '<?php echo base_url(); ?>';
							game_results['total_time'] = total_time_for_deck;
							total_time_for_deck = 0;
							game_results['correct_total'] = correct_total;
							game_results['wrong_total'] = wrong_total;
							correct_total = 0;
							wrong_total = 0;
							//   game_results['deck'] = '';
							console.log(game_results);
							if (gameMode == 'RW') {
								new ajaxObject("<?php echo base_url() ?>index.php/auth/reviewModeSave", 
									function(res, status) {
										if (status != 200) {
											alert('Review mode save failed!\n' + res);
										}
									}).update(preparePost(game_results), "POST");
							}
							document.getElementById("gameScreen").style.display = "none";
							document.getElementById("cardDeckSelectionScreen").style.display = "block";
						}
						else {
							startTimer(false);	//restart timer without reset
						}
					}
					/*******Card Flipping JS********/
					function flip() {
						document.getElementById("fcardQues").className += " fcardQuesFlip";
						document.getElementById("fcardAns").className += " fcardAnsFlip";
					}
					function flipBack() {
						document.getElementById("fcardQues").className = document.getElementById("fcardQues").className.replace
								(/(?:^|\s)fcardQuesFlip(?!\S)/g, '');
						document.getElementById("fcardAns").className = document.getElementById("fcardAns").className.replace
								(/(?:^|\s)fcardAnsFlip(?!\S)/g, '');
					}
					/********Card Content Rendering********/
					function renderQuestion(mode, history,test_history, rank, avgTime, ans, ansNotes, ques) {
						document.getElementById("qMode").innerHTML = "M:" + mode;
						document.getElementById("qHistory").innerHTML = "H:" + history;
						document.getElementById("qTestHistory").innerHTML = "Test H:" + test_history;
						document.getElementById("qRank").innerHTML = "R:" + rank;
						document.getElementById("qAvg").innerHTML = "Avg:" + avgTime;
						document.getElementById("qContent").innerHTML = 'A. '+ans;
						/*Call timer function to set count up time <div style="font-size:14px;">Q. '+ques+'</div>*/
						startTimer(true);
					}
					function renderAnswer(mode, history,test_history, rank, avgTime, time, ques, quesNotes,ans) {
						document.getElementById("aMode").innerHTML = "M:" + mode;
						document.getElementById("aHistory").innerHTML = "H:" + history;
						document.getElementById("aTestHistory").innerHTML = "Test H:" + test_history;
						document.getElementById("aRank").innerHTML = "R:" + rank;
						document.getElementById("aAvg").innerHTML = "Avg:" + avgTime;
						document.getElementById("aTime").innerHTML = "Time:" + time;
						document.getElementById("aContent").innerHTML = '<div style="font-size:14px;">'+ques + '</div>'+ans+'<div style="font-size:14px;">'+quesNotes+'</div>';
					}
					/***********Timer Functions****************/
					function startTimer(restart) {
						timerIntervalId = setInterval(tick, 1000);
						if (restart) {
							totalSeconds = -1;
							tick();
						}
					}
					function tick() {
						++totalSeconds;
						document.getElementById("qTime").innerHTML = "Time:" + getFormatedTime(totalSeconds);
					}
					function pad(val) {
						var valString = val + "";
						if (valString.length < 2) {
							return "0" + valString;
						}
						else {
							return valString;
						}
					}
					function getFormatedTime(totalSeconds) {
						var sec = pad(totalSeconds % 60);
						var min = pad(parseInt(totalSeconds / 60));
						return min + ":" + sec;
					}
					/***********Card Deck Section**************/
					function newDeck() {
						window.location = "<?php echo base_url() ?>index.php/deck/";
					}
					/********Arrow Key Command Map*******************/
					// define a handler
					function keyHandler(e) {
						if (e.keyCode == 37) {		//left arrow
							showAns();
							markAnswer(1);
						}
						else if (e.keyCode == 39) {	//right arrow
							showAns();
							markAnswer(0);
						}
						else if (e.keyCode == 38) {	//up arrow
							showAns();
							finishGame();
						}
						else if (e.keyCode == 40) {	//down arrow
							showAns();
						}
					}
					
			function show_all()
			{
				document.getElementById("deckIds").value = deckId;
				window.location = "<?php echo base_url(); ?>index.php/game/reverse_with_sound_show_all/<?php echo $this->ion_auth->user()->row()->id ?>/"+deckId;
			}
			function show_text()
			{
				$(".fcardQues").css({"display":"block"});
				$(".fcardStage").css({"display":"none"});
				showNextQues();
			}
			function finish()
			{
				if (confirm("Do you really want to finish this game?")) {
					window.location = "<?php echo base_url(); ?>index.php/game/game_view/";
				}
				else
				{
				}
			}
			
			
					// register the handler 
					document.addEventListener('keyup', keyHandler, false);
		</script>
	</head>
	<body onload="startGame()">
		<div id="source_div_a">
			<audio id='player_a'><source id='sorce_id_a' type='audio/mpeg' src=''></audio>
		</div>
		<div id="source_div_q">
			<audio id='player_q'><source id='sorce_id_q' type='audio/mpeg' src=''></audio>
		</div>
		<div id="source_div_a_slow">
			<audio id='player_a_slow'><source id='sorce_id_a_slow' type='audio/mpeg' src=''></audio>
		</div>
		<div id="source_div_q_slow">
			<audio id='player_q_slow'><source id='sorce_id_q_slow' type='audio/mpeg' src=''></audio>
		</div>
		<input type="hidden" id="deckIds" value="">
		<!--	  
		<audio controls="controls">
		  <source id="sorce_id" type="audio/mpeg" >
		Your browser does not support the audio element. Please Update Browser..
		Details:
		supported browser: 
		Internet Explorer
		Google Chrome
		Firefox (Firefox 21 running on Windows 7, Windows 8, Windows Vista, and Android now supports MP3)
		Safari
		</audio>-->
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Flash Card Game</div>
				<div class="logOut">
					<?php
					if ($this->ion_auth->logged_in()) {
						$user = $this->ion_auth->user()->row();
						$userName = $user->first_name;
						echo "Logg out: " . anchor('game/logout', $userName);
					}
					?>
				</div>
			</div>
			<!-- Flash card section -->
			<div class="fcardHodler" id="gameScreen">
				<div class="fcardFlipper">
					
					<!-- Question Card -->
					<div class="fcardQues" id="fcardQues">
						<div class="fcardHeadder">
							<div id="qMode" style="width:10%"  class="fcardHeadderContent">Mode: Review</div>
							<div id="qHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="qTestHistory" style="width:25%"  class="fcardHeadderContent">History:###</div>
							<div id="qRank" style="width:10%"  class="fcardHeadderContent">Rank: 1</div>
							<div id="qAvg" style="width:15%"  class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="qTime" style="width:15%"  class="fcardHeadderContent">Time: 00.00</div>
							<div class="clearFloat"></div>
						</div>
						<div id="qContent" class="fcardContent">
							Content
						</div>
						<div class="fcardFooterAns">
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:markAnswer(1);"><p>&#10004;</p></div></div></div> 
							<div class="buttonHolder"><div class="buttonInner"><div class="button red" onclick="javascript:markAnswer(0);"><p>&#10007;</p></div></div></div> 
							
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="showAns()"><p>Meaning</p></div></div></div>
							<div class="buttonHolder"><div class="buttonInner"><div class="button" onclick="finishGame()"><p>Finish</p></div></div></div>
							<div class="clearFloat"></div>
						</div>
					</div>
					<!-- Answer Card-->
					<div class="fcardAns" id="fcardAns">
						<div class="fcardHeadder">
							<div id="aMode" style="width:10%"  class="fcardHeadderContent">Mode: Review</div>
							<div id="aHistory" style="width:25%"  class="fcardHeadderContent">History:###</div>
							<div id="aTestHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="aRank" style="width:10%"  class="fcardHeadderContent">Rank: 1</div>
							<div id="aAvg" style="width:15%"  class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="aTime" style="width:15%" class="fcardHeadderContent">Time: 00.00</div>
							<div class="clearFloat"></div>
						</div>
						<div id="aContent" class="fcardContent">
							Answer
						</div>
						<div class="fcardFooterAns">
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:markAnswer(1);"><p>&#10004;</p></div></div></div> 
							<div class="buttonHolder"><div class="buttonInner"><div class="button red" onclick="javascript:markAnswer(0);"><p>&#10007;</p></div></div></div> 
							<div class="buttonHolder"><div class="buttonInner"><div class="button" onclick="finishGame()"><p>Finish</p></div></div></div>
							
							<div class="clearFloat"></div>
						</div>
					</div>
					
					<!-- Stage-->
					<div class="fcardStage" style="display:none;" id="fcardStage">
						<h1>What o you hear?</h1>
						<div class="fcardContent">	
							<div class="reversclass  green " onclick="show_text();" >SHOW TEXT</div>
							<div class="reversclass  green " onclick="show_all();" >SHOW ALL</div>
							<div class="reversclass  green " onclick="finish();" >FINISH</div>
							<div class="clearFloat"></div>
						</div>
					</div>
				
				
				</div>
				<div id="extraInfo" style="position:relative; z-index: 500000000000000000000;  top:-20px;"></div>
			</div>
			<!-- Game mode and extra functions selector Screen -->
			<div class="gameModeScreen" id="gameModeScreen">
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('TR');"><p>Training Mode</p></div></div></div> 
				<br/><br/><br/>  
				<div class="buttonHolder" ><div class="buttonInner"><div class="button green" id="sound_button_loading" onclick="javascript:setGameModeAndLoadDecks('RW');"><p>Review Mode<br/>Loading......</p></div></div></div> 
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/rw_with_sound"><p>Review Mode With Sound</p></a></div></div> 
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('TST');"><p>Test Mode</p></div></div></div>
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('STST');"><p>Supervised Test</p></div></div></div>
				<br/><br/><br/>
				create new deck
				<div class='buttonHolder' style="display:none"><div class='buttonInner'><div class='button green' onclick='newDeck()'><p>New Deck</p></div></div></div>
				<br/><br/><br/>
				manage card decks
				<div class='buttonHolder' style="display:none"><div class='buttonInner'><div class='button green' onclick='manageDeck()'><p>Manage Deck</p></div></div></div>
				<br/><br/><br/> 
			</div>
			<!-- Card Deck Selection Screen -->
			<div class="cardDeckSelectionScreen" id="cardDeckSelectionScreen">
			</div>
		</div>
	</body>
</html>
<script>
	$(document).ready(function() {
		$("#sound_button_loading").trigger('click');
	});
</script>
