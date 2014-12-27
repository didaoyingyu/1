<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game-Supervised Mode</title>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<link rel="stylesheet" href="<?php echo base_url(); ?>colorbox/colorbox.css"/>
<!--		<script src="jquery.min.js"></script>-->
		<script src="<?php echo base_url(); ?>colorbox/jquery.colorbox-min.js"></script>
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
			var deckHandler = new DeckHandler();
			var currentCard;
			var gameMode = 'TR'; /*default mode is training*/
			var card_to_test = 0;
			var prev_card_ids = 0;
			var more_cards = 0;
			var selectedDeck = 0;
			var elapsedTimeMultiplier = 0;
			var variableOk = 0
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
			/*reviw mode gama parameters*/
			var loadRmParamsResponse;
			var loadRmParamsResponseStatus;
			/*current deck array*/
			var currentDeckArray;
			var pre_cards = new Array();
			var z_count = 1;
			var game_results = new Object();
			var deck_count = 0;
			var total_time_for_deck = 0;
			//	 var change_minus = 0;
			game_results['correct_total'] = new Object();
			game_results['wrong_total'] = new Object();
			game_results['deck'] = new Object();
			game_results['card_count'] = new Object();
			game_results['wrong_twice_or_more_count'] = new Object();
			game_results['wrong_twice_or_more_count'] = 0;
			//	var game_results['user_id'] = new Object();
			var current_user_id = 0;
			var game_count = 0;
			var correct_total = 0;
			var wrong_total = 0;
			var first_time_card_count = 0;
			var first_time_correct_Card_cout = 0;
			//	var no_pre_wrong=0;
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
				else if (gameMode == 'STST')
				{
					// loadSuperVisedModeParams();
					getUsername();
				}
			}
			function loadReviewModeParams() {
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_stst_params/";
				var myRequest = new ajaxObject(loadDecksAjaxPath, loadReviewModeParamsHandler, loadRmParamsResponse, loadRmParamsResponseStatus);
				myRequest.update();
			}
			function askPasswordstst()
			{
				$.colorbox({html: "<label>Enter Your Password</label><input type='password' id='password_inp'/><input type='button' value='enter' onclick='sendPassword()' />"});
			}
			function sendPassword()
			{
				var pass = $("#password_inp").val();
				$.post("<?php echo base_url() ?>index.php/auth/check_password", {'pass': pass}, function(res) {
					if (res == 'correct')
					{
						//  jQuery('#cboxClose').click();
						getUsername();
						//   loadSuperVisedModeParams();
					}
					else
					{
						alert("You had entered a wrong password/ You are not the admin");
					}
				});
			}
			function clickify(res) {
				var dvWords = res.split(' ');
				var dvHTML = ''
				var flag = 0;
				for (i = 0; i < dvWords.length; i++)
				{
					if (dvWords[i].toLowerCase() == 'playing')
					{
						flag = 1;
					}
				}
				if (flag == 1)
				{
					return 'yes';
				}
				else
				{
					return 'no';
				}
			}
			function sendUsername()
			{
				$("#username_inp").attr("disabled", "disabled");
				/*$("#card_to_test").attr("disabled","disabled");*/
				var username = ($("#username_inp").val());
				var card_to_test = ($("#card_to_test").val());
				if (username != '') {
					$.post("<?php echo base_url() ?>index.php/auth/check_username", {'user': username}, function(res) {
						var result = clickify(res);
						if (result == 'yes')
						{
//										alert(res);
							$.colorbox({html: "<p>" + res + "<p>"});
							$.post("<?php echo base_url() ?>index.php/auth/check_userid", {'user': username}, function(res) {
								if (res != 'Please check username again' && res != 'not admin')
								{
									game_results['user_id'] = new Object();
									game_results['user_id'] = res;
									current_user_id = res;
									console.log(game_results);
									//loadSuperVisedModeParams(card_to_test);
									loadSupervisedPlusPreviousResult(current_user_id);
								}
								else
								{
									alert(res);
								}
							});
							//jQuery('#cboxClose').click();
							$(".user_identifier").text("Playing As " + res.split(" ").pop());
						}
						else
						{
							alert(res);
							$("#username_inp").removeAttr("disabled");
							/* $("#card_to_test").attr("disabled",false);*/
						}
					});
				} else {
					alert('Please check username');
					$("#username_inp").attr("disabled", false);
					/* $("#card_to_test").attr("disabled",false);*/
				}
			}
			function getUsername()
			{
				$.colorbox({html: "<div class='user_box'><div class='username'><label>Enter your username</label><input type='text' id='username_inp' /></div><input type='button' value='enter' id='username_enter_blur' onclick='sendUsername()' /></div>"});
			}
			/**load supervised plus mode privious result ASHVIN PATEL 29/JUN/2014**/
			function loadSupervisedPlusPreviousResult(current_user_id) {
				$.ajax({
					url: "<?php echo base_url() ?>index.php/game/load_stst_plus_privious_result/",
					type: "post",
					data: {userid: current_user_id},
					success: function(loadRmParamsResponse, loadRmParamsResponseStatus, xhr) {
						//alert(loadRmParamsResponse);
						//alert(xhr.status);
						$('.gameModeScreen').hide();
						$('.plus_mode_test_report').show();
						$('.plus_mode_test_report').html(loadRmParamsResponse);
					}
				});
			}
			/**load supervised plus mode parameters ASHVIN PATEL 29/JUN/2014**/
			function loadSuperVisedModeParams(test_type) {
				card_to_test = '';
				card_to_test = $('#card_to_test').val();
				if (card_to_test != '') {
					$.ajax({
						url: "<?php echo base_url() ?>index.php/game/load_stst_plus_params/",
						type: "post",
						success: function(loadRmParamsResponse, loadRmParamsResponseStatus, xhr) {
							//alert(loadRmParamsResponse);
							//alert(xhr.status);
							loadReviewModeParamsHandler(loadRmParamsResponse, xhr.status, test_type);
						}
					});
				} else {
					alert('Enter card to test');
				}
				/*var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_stst_plus_params/";
				 var myRequest = new ajaxObject(loadDecksAjaxPath, loadReviewModeParamsHandler, loadRmParamsResponse, loadRmParamsResponseStatus);
				 myRequest.update();*/
			}
			function loadReviewModeParamsHandler(loadRmParamsResponse, loadRmParamsResponseStatus, test_type) {
				if (loadRmParamsResponseStatus == 200) {
					console.log(loadRmParamsResponse);
					/*var responseCleaned = loadRmParamsResponse.split(']')[0] + ']';
					 reviwModeParams = eval('(' + responseCleaned + ')');
					 console.log(reviwModeParams);*/
					for (var i = 0; i < loadRmParamsResponse.length; i++) {
						if (loadRmParamsResponse[i]['param_name'] == 'minRepeatTime') {
							minRepeatTime = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'maxNoShowTime') {
							maxNoShowTime = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'rankInc') {
							rankInc = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'rankDesc') {
							rankDesc = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'correctCountForInc') {
							correctCountForInc = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'wrongCountForDesc') {
							wrongCountForDesc = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'avgExceedRankDesc') {
							avgExceedRankDesc = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'avgExceedPercentage') {
							avgExceedPercentage = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'elapsedTimeMultiplier') {
							elapsedTimeMultiplier = parseInt(loadRmParamsResponse[i]['value']);
						} else if (loadRmParamsResponse[i]['param_name'] == 'variableOk') {
							variableOk = parseInt(loadRmParamsResponse[i]['value']);
							//alert(variableOk);
						}
					}
					/*start game*/
					if (test_type == 'automatic') {
						loadGame('');
					} else {
						loadDecks()
					}
				} else {
					alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
				}
			}
			/********Load Card Decks****************************/
			function loadDecks() {
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_plus_decks/" + current_user_id;
				var myRequest = new ajaxObject(loadDecksAjaxPath, loadDecksHandler, loadDecksResponse, loadDecksResponseStatus);
				myRequest.update();
			}
			function loadDecksHandler(loadDecksResponse, loadDecksResponseStatus) {
				if (loadDecksResponseStatus == 200) {
					var responseCleaned = loadDecksResponse.split(']')[0] + ']';
					deckArray = eval('(' + responseCleaned + ')');
					currentDeckArray = deckArray;
					$('.plus_mode_test_report').hide();
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
					innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadGame(" + deckArray[i]['deck_id'] + ")'><p>" + deckArray[i]['deck_name'] + "</p></div></div></div><br/><br/><br/>";
				}
				/*for multiple deck mode*/
				innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadGameMultiDeckMode()'><p>Play Multiple Decks</p></div></div></div><br/><br/><br/>";
				document.getElementById("cardDeckSelectionScreen").innerHTML = innerHtml;
			}
			/********Load Card Decks For More Cards ASHVIN PATEL 20/JUN/2014****************************/
			function loadDecksM() {
				pre_deck_card = parseInt(card_to_test) - parseInt(more_cards);
				var loadmorecardshtml = '<div class="more_cards" style="padding: 19px 72px 2px 80px;text-align: center;color: #ff0000;"><label style="display: block;margin-bottom: 5px;text-align: center;">Selected deck have only <span style="font-weight: bold;color: #831F1F;">' + pre_deck_card + '</span> cards to test</label><label style="display: block;">Please load <span style="font-weight: bold;color: #831F1F;">' + more_cards + '</span> more cards from belove decks</label></div>';
				$('.plus_mode_test_report').after(loadmorecardshtml);
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_plus_decks_more/" + current_user_id;
				var myRequest = new ajaxObject(loadDecksAjaxPath, loadDecksHandlerM, loadDecksResponse, loadDecksResponseStatus);
				myRequest.update();
			}
			function loadDecksHandlerM(loadDecksResponse, loadDecksResponseStatus) {
				if (loadDecksResponseStatus == 200) {
					var responseCleaned = loadDecksResponse.split(']')[0] + ']';
					deckArray = eval('(' + responseCleaned + ')');
					currentDeckArray = deckArray;
					$('.plus_mode_test_report').hide();
					document.getElementById("cardDeckSelectionScreen").style.display = "block";
					document.getElementById("gameModeScreen").style.display = "none";
					console.log(deckArray);
					/*render deck selection view*/
					renderDeckSelectionM(deckArray);
				} else {
					alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
				}
			}
			function renderDeckSelectionM(deckArray) {
				var innerHtml = "";
				for (var i = 0; i < deckArray.length; i++) {
					if (deckArray[i]['deck_id'] != selectedDeck) {
						innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadMoreCard(" + deckArray[i]['deck_id'] + ")'><p>" + deckArray[i]['deck_name'] + "</p></div></div></div><br/><br/><br/>";
					}
				}
				/*for multiple deck mode*/
				/*innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadGameMultiDeckMode()'><p>Play Multiple Decks</p></div></div></div><br/><br/><br/>";*/
				document.getElementById("cardDeckSelectionScreen").innerHTML = innerHtml;
			}
			function renderDeckMultiSelection(deckArray) {
				var innerHtml = "";
				for (var i = 0; i < deckArray.length; i++) {
					innerHtml = innerHtml + "<p><input type='checkbox' id='chk_" + deckArray[i]['deck_id'] + "' name='" + deckArray[i]['deck_id'] + "'>" + deckArray[i]['deck_name'] + "</p><br/>";
				}
				/*play multiple deck mode*/
				innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:playMultiDeckMode()'><p>Play</p></div></div></div><br/><br/><br/>";
				document.getElementById("cardDeckSelectionScreen").innerHTML = innerHtml;
			}
			function loadMoreCard(deckIdIn) {
				var deckId1 = deckIdIn;
				//alert(more_cards);
				$.ajax({
					url: '<?php echo base_url('index.php/game/load_plus_mode_more_cards') ?>',
					type: 'post',
					data: {user_id: current_user_id, deck_id: deckId1, cards: more_cards, elapsedTimeMultiplier: elapsedTimeMultiplier},
					success: function(data) {
						console.log('card_ids: ' + data['card_ids']);
						console.log('prev_card_ids: ' + prev_card_ids);
						var new_cards = prev_card_ids.concat(data['card_ids']);
						console.log('New Card ids: ' + new_cards);
						//alert(new_cards);
						$('.more_cards').remove();
						startPlusModeGame(new_cards);
					}
				});
			}
			/*******Load/save game section********/
			function loadGame(deckIdIn) {
				deckHandler.reset();
				deckId = deckIdIn;
				$.ajax({
					url: '<?php echo base_url('index.php/game/load_plus_mode_cards') ?>',
					type: 'post',
					data: {user_id: current_user_id, deck_id: deckId, cards: card_to_test},
					success: function(data) {
						console.log(data);
						console.log('card_ids: ' + data['card_ids'] + ', Remaining cards' + data['remain_cards']);
						var card_ids = data['card_ids'];
						var remaining_cards = data['remain_cards'];
						if (card_ids != '') {
							if (remaining_cards == 0 || remaining_cards == '' || remaining_cards == '0') {
								startPlusModeGame(card_ids);
							}
							else if (remaining_cards > 0) {
								//alert();
								selectDeckForMoreCards(remaining_cards, card_ids, deckId);
							}
						} else {
							alert('No card to test');
						}
					}
				});
			}
			function selectDeckForMoreCards(remaining_cards, card_ids, deckId) {
				prev_card_ids = '';
				more_cards = '';
				selectedDeck = '';
				prev_card_ids = card_ids;
				selectedDeck = deckId;
				more_cards = remaining_cards;
				loadDecksM();
			}
			function startPlusModeGame(card) {
				$(".answer_code").html("");
				card_ids = new Array();
				pre_cards = new Array();
				game_results = new Object();
				game_results['correct_total'] = new Object();
				game_results['wrong_total'] = new Object();
				game_results['deck'] = new Object();
				game_results['card_count'] = new Object();
				game_results['wrong_twice_or_more_count'] = new Object();
				game_results['wrong_twice_or_more_count'] = 0;
				game_results['user_id'] = new Object();
				game_results['user_id'] = current_user_id;
				console.log(game_results);
				//	var game_results['user_id'] = new Object();
				//   var current_user_id=0;
				var game_count = 0;
				var correct_total = 0;
				var wrong_total = 0;
				var first_time_card_count = 0;
				var first_time_correct_Card_cout = 0;
				//alert(card);
				$.ajax({
					url: '<?php echo base_url('index.php/game/load_card_data/') ?>',
					type: 'post',
					data: {card_ids: card, user_id: current_user_id},
					success: function(loadRmParamsResponse, loadRmParamsResponseStatus, xhr) {
						console.log(loadRmParamsResponse);
						loadGameHandler(loadRmParamsResponse, xhr.status)
					}
				});
				/*var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_card_data/";
				 var myRequest = new ajaxObject(loadGameAjaxPath, loadGameHandler, loadGameResponse, loadGameResponseStatus);
				 //myRequest.update();
				 myRequest.update('card_ids=' + JSON.stringify(card_ids), 'POST');*/
			}
			function loadGameHandler(loadGameResponse, loadGameResponseStatus) {
				if (loadGameResponseStatus == 200) {
					cardArray = loadGameResponse;
					for (var i = 0; i < cardArray.length; i++) {
						cardArray[i]['correct'] = 0;
						cardArray[i]['wrong'] = 0;
					}
					console.log(cardArray);
					deckHandler.setDeck(cardArray);
					$('.plus_mode_test_report').hide();
					document.getElementById("gameScreen").style.display = "block";
					document.getElementById("gameModeScreen").style.display = "none";
					document.getElementById("cardDeckSelectionScreen").style.display = "none";
					showNextQues();
				} else {
					alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + loadGameResponseStatus);
				}
			}
			function saveCard(card) {
				var saveCardAjaxPath = "<?php echo base_url() ?>index.php/game/save_user_card";
				var myRequest = new ajaxObject(saveCardAjaxPath, saveCardHandler, saveCardResponse, saveCardResponseStatus);
				/*set last shown time*/
				var time = new Date();
				var timeMils = time.getTime();
				card['last_shown'] = timeMils;
				card['last_date'] = timeMils;
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
				$(".answer_code").html("");
				card_ids = new Array();
				pre_cards = new Array();
				game_results = new Object();
				game_results['correct_total'] = new Object();
				game_results['wrong_total'] = new Object();
				game_results['deck'] = new Object();
				game_results['card_count'] = new Object();
				game_results['wrong_twice_or_more_count'] = new Object();
				game_results['wrong_twice_or_more_count'] = 0;
				game_results['user_id'] = new Object();
				game_results['user_id'] = current_user_id;
				console.log(game_results);
				//	var game_results['user_id'] = new Object();
				//   var current_user_id=0;
				var game_count = 0;
				var correct_total = 0;
				var wrong_total = 0;
				var first_time_card_count = 0;
				var first_time_correct_Card_cout = 0;
				// deckId = deckIdIn;
				var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_cards_md/" + current_user_id;
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
					deckHandler.setDeck(cardArray);
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
				$("#source_div").html("");
				currentCard = deckHandler.getNextCard(gameMode);
				if (!currentCard) { //if nothing found
					finishSupervisedMode();
					return;
				}
				game_results['deck'][game_count] = new Object();
				game_results['deck'][game_count]['deck_id'] = currentCard['deck_id'];
				game_results['deck'][game_count]['card_id'] = currentCard['card_id'];
				game_results['deck'][game_count]['history'] = currentCard['history'];
				flipBack();
				var avgTime = 0;
				console.log(currentCard);
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				renderQuestion(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), currentCard['question']);
			}
			function showAns() {
				flip();
				// document.getElementById('player').play();
				/*stop the time up timer and get it value*/
				clearInterval(timerIntervalId);
				currentCard['last_time'] = totalSeconds;
				total_time_for_deck += totalSeconds;
				var timeTakenForQues = getFormatedTime(totalSeconds);
				var avgTime = 0;
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				totalSeconds = 0;
				renderAnswer(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), timeTakenForQues, currentCard['answer']);
 			}
			function ansCorrect() {
				total_cards++;
				if (game_results['deck'][game_count]['history'] == '----------' || game_results['deck'][game_count]['history'] == '' || game_results['deck'][game_count]['history'] == null)
				{
					first_time_correct_Card_cout++;
				}
				var test_history = $(".test_history").html();
				test_history = test_history.substring(7);
				Current_test_history = 'O' + test_history;
				game_results['deck'][game_count]['ans'] = 'true';
				game_results['deck'][game_count]['rank'] = currentCard['rank'];
				game_results['deck'][game_count]['last_shown'] = currentCard['last_shown'];
				game_results['deck'][game_count]['utp'] = currentCard['utp'];
				currentCard['test_history'] = Current_test_history;
				game_count++;
				correct_total++;
				var prev_history = $(".answer_code").html();
				Current_history = prev_history + 'O';
				$(".answer_code").html(Current_history);
				var ansStatus = new Boolean(1);
				//alert(currentCard);
				deckHandler.handleCardStatus(currentCard, ansStatus, gameMode, historyLength, variableOk);
				saveCard(currentCard);
				showNextQues();
			}
			function ansWrong() {
				total_cards++;
				var test_history = $(".test_history").html();
				test_history = test_history.substring(7);
				Current_test_history = 'X' + test_history;
				game_results['deck'][game_count]['ans'] = 'false';
				game_results['deck'][game_count]['rank'] = currentCard['rank'];
				game_results['deck'][game_count]['last_shown'] = currentCard['last_shown'];
				game_results['deck'][game_count]['utp'] = currentCard['utp'];
				currentCard['test_history'] = Current_test_history;
				game_count++;
				wrong_total++;
				var prev_history = $(".answer_code").html();
				Current_history = prev_history + 'X';
				$(".answer_code").html(Current_history);
				var ansStatus = new Boolean(0);
				deckHandler.handleCardStatus(currentCard, ansStatus, gameMode, historyLength, variableOk);
				saveCard(currentCard);
				showNextQues();
			}
			function ansOk() {
				total_cards++;
				//alert(total_cards);
				game_results['deck'][game_count]['ans'] = 'false';
				game_count++;
				wrong_total++;
				var prev_history = $(".answer_code").html();
				Current_history = prev_history + 'K';
				$(".answer_code").html(Current_history);
				var ansStatus = 2;
				deckHandler.handleCardStatus(currentCard, ansStatus, gameMode, historyLength, variableOk);
				saveCard(currentCard);
				showNextQues();
			}
			function ansP() {
				total_cards++;
				//alert(total_cards);
				game_results['deck'][game_count]['ans'] = 'false';
				game_count++;
				wrong_total++;
				var prev_history = $(".answer_code").html();
				Current_history = prev_history + 'P';
				$(".answer_code").html(Current_history);
				var ansStatus = 3;
				deckHandler.handleCardStatus(currentCard, ansStatus, gameMode, historyLength, variableOk);
				saveCard(currentCard);
				showNextQues();
			}
			function finishGame() {
				game_results['card_count'] = total_cards;
				total_cards = 0;
				/* show the deck selection screen */
				game_results['first_time_card_count'] = first_time_card_count;
				game_results['first_time_correct_Card_cout'] = first_time_correct_Card_cout;
				//   game_results['change_minus'] = change_minus;
				first_time_card_count = 0;
				first_time_correct_Card_cout = 0;
				change_minus = 0;
				consle.log(game_results);
				clearInterval(timerIntervalId);
				if (confirm("Do you really want to finish this game?")) {
					(alert("Game Completed"));
					var base_url = '<?php echo base_url(); ?>';
					game_results['total_time'] = total_time_for_deck;
					total_time_for_deck = 0;
					game_results['correct_total'] = correct_total;
					game_results['wrong_total'] = wrong_total;
					correct_total = 0;
					wrong_total = 0;
					//	game_results['no_pre_wrong'] = no_pre_wrong;
					//	  no_pre_wrong=0;
					//   game_results['deck'] = '';
					$.post(base_url + "/index.php/auth/supervisedModeSave", {"data": game_results}, function(res) {
						if (res != 'success')
						{
							alert(res);
						}
					});
					window.open(base_url + "/index.php/game/deck_report/" + current_user_id, "_blank");
					document.getElementById("gameScreen").style.display = "none";
					document.getElementById("cardDeckSelectionScreen").style.display = "block";
				}
				else {
					startTimer(false);	//restart timer without reset
				}
			}
			function finishSupervisedMode()
			{
				game_results['card_count'] = total_cards;
				total_cards = 0;
				game_results['first_time_card_count'] = first_time_card_count;
				game_results['first_time_correct_Card_cout'] = first_time_correct_Card_cout;
				//	  game_results['change_minus'] = change_minus;
				first_time_card_count = 0;
				first_time_correct_Card_cout = 0;
				change_minus = 0;
				clearInterval(timerIntervalId);
				(alert("Game Completed"));
				var base_url = '<?php echo base_url(); ?>';
				game_results['total_time'] = total_time_for_deck;
				total_time_for_deck = 0;
				game_results['correct_total'] = correct_total;
				game_results['wrong_total'] = wrong_total;
				correct_total = 0;
				wrong_total = 0;
				//  game_results['no_pre_wrong'] = no_pre_wrong;
				//	no_pre_wrong=0;
				//   game_results['deck'] = '';
				$.post(base_url + "index.php/auth/supervisedModeSave", {"data": game_results, 'user_id': current_user_id}, function(res) {
					if (res != 'success')
					{
						//alert(res);
					}
				});
				loadSupervisedPlusPreviousResult(current_user_id);
				window.open(base_url + "/index.php/game/deck_report/" + current_user_id, "_blank");
				document.getElementById("gameScreen").style.display = "none";
				//document.getElementById("cardDeckSelectionScreen").style.display = "block";
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
			function renderQuestion(mode, history, test_history, rank, avgTime, ques) {
				document.getElementById("qMode").innerHTML = "M:" + mode;
				document.getElementById("qHistory").innerHTML = "H:" + history;
				document.getElementById("qTestHistory").innerHTML = "Test H:" + test_history;
				document.getElementById("qRank").innerHTML = "R:" + rank;
				document.getElementById("qAvg").innerHTML = "Avg:" + avgTime;
				document.getElementById("qContent").innerHTML = ques;
				/*Call timer function to set count up time*/
				startTimer(true);
			}
			function renderAnswer(mode, history, test_history, rank, avgTime, time, ans) {
				document.getElementById("aMode").innerHTML = "M:" + mode;
				document.getElementById("aHistory").innerHTML = "H:" + history;
				document.getElementById("aTestHistory").innerHTML = "Test H:" + test_history;
				document.getElementById("aRank").innerHTML = "R:" + rank;
				document.getElementById("aAvg").innerHTML = "Avg:" + avgTime;
				document.getElementById("aTime").innerHTML = "Time:" + time;
				document.getElementById("aContent").innerHTML = ans;
			}
			/***********Timer Functions****************/
			function startTimer(restart)
			{
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
			function pad(val)
			{
				var valString = val + "";
				if (valString.length < 2)
				{
					return "0" + valString;
				}
				else
				{
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
				//alert(e.keyCode)
				if (e.keyCode == '37') {		//left arrow
					showAns();
					ansCorrect();
				}
				else if (e.keyCode == '39') {	//right arrow
					showAns();
					ansWrong();
				}
				else if (e.keyCode == '38') {	//up arrow
					showAns();
					finishGame();
				}
				else if (e.keyCode == '40') {	//down arrow
					showAns();
				}
			}
			// register the handler
			/*document.addEventListener('keypress', keyHandler, false);*/
			window.addEventListener("keydown", keyHandler);
		</script>
	</head>
	<body onload="startGame()">
		<div id="source_div">
		</div>
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
			<div class="answer_code pull-left"></div>
			<div class="user_identifier">No user selected</div>
			<div style="width:100%"><input type="button" value="New User" class="btn-danger pull-right" onClick="clickOnSuperVisedButton()"/></div>
			<!-- Flash card section -->
			<div class="fcardHodler" id="gameScreen">
				<div class="fcardFlipper">
					<!-- Question Card -->
					<div class="fcardQues" id="fcardQues">
						<div class="fcardHeadder">
							<div id="qMode" style="width:10%" class="fcardHeadderContent">Mode: Review</div>
							<div id="qHistory" style="width:32%" class="fcardHeadderContent">History:###</div>
							<div id="qTestHistory" style="width:33%" class="test_history fcardHeadderContent">Test History:###</div>
							<div id="qRank" style="width:5%" class="  fcardHeadderContent">Rank: 1</div>
							<div id="qAvg" style="width:10%" class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="qTime" style="width:10%" class="fcardHeadderContent">Time: 00.00</div>
							<div class="clearFloat"></div>
						</div>
						<div id="qContent" class="fcardContent">
							Content
						</div>
						<div class="fcardFooterQues">
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="showAns()"><p>Answer</p></div></div></div>
							<div class="buttonHolder"><div class="buttonInner"><div class="button" onclick="finishGame()"><p>Finish</p></div></div></div>
							<div class="clearFloat"></div>
						</div>
					</div>
					<!-- Answer Card-->
					<div class="fcardAns" id="fcardAns">
						<div class="fcardHeadder">
							<div id="aMode" style="width:10%" class="fcardHeadderContent">Mode: Review</div>
							<div id="aHistory" style="width:32%" class="fcardHeadderContent">History:###</div>
							<div id="aTestHistory" style="width:33%" class=" test_history fcardHeadderContent">Test History:###</div>
							<div id="aRank" style="width:5%" class="fcardHeadderContent">Rank: 1</div>
							<div id="aAvg" style="width:10%" class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="aTime" style="width:10%" class="fcardHeadderContent">Time: 00.00</div>
							<div class="clearFloat"></div>
						</div>
						<div id="aContent" class="fcardContent">
							Answer
						</div>
						<div class="fcardFooterAns">
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:ansCorrect();"><p>&#10004;</p></div></div></div>
							<div class="buttonHolder"><div class="buttonInner"><div class="button red" onclick="javascript:ansWrong();"><p>&#10007;</p></div></div></div>
							<div class="buttonHolder"><div class="buttonInner"><div class="button yellow" onclick="javascript:ansOk();"><p>OK</p></div></div></div>
							<div class="buttonHolder"><div class="buttonInner"><div class="button " onclick="javascript:ansP();"><p>P</p></div></div></div>
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
				<div class="buttonHolder" ><div class="buttonInner"><div class="button green" id="sound_button_loading" onclick="javascript:setGameModeAndLoadDecks('STST');"><p>Supervised Mode<br/>Loading......</p></div></div></div>
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/rw_with_sound"><p>Review Mode </p></a></div></div>
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('TST');"><p>Test Mode</p></div></div></div>
				<br/><br/><br/>
				<div class="buttonHolder" style="display:none"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('STST');"><p>Supervised Test</p></div></div></div>
				<br/><br/><br/>
				<div class='buttonHolder' style="display:none"><div class='buttonInner'><div class='button green' onclick='javascript:newDeck()'><p>New Deck</p></div></div></div>
				<br/><br/><br/>
				<div class='buttonHolder' style="display:none"><div class='buttonInner'><div class='button green' onclick='javascript:manageDeck()'><p>Manage Deck</p></div></div></div>
				<br/><br/><br/>
			</div>
			<!--Show previous test and review session result ASHVIN PATEL 20/JUN/2014-->
			<div class="plus_mode_test_report">
			</div>
			<!-- Card Deck Selection Screen -->
			<div class="cardDeckSelectionScreen" id="cardDeckSelectionScreen">
			</div>
		</div>
	</body>
</html>
<script>
	$(document).ready(function() {
		clickOnSuperVisedButton();
	});
	function clickOnSuperVisedButton()
	{
		startGame();
		$("#sound_button_loading").trigger('click');
	}
</script>