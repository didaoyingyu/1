<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game</title>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<!-- Ultimate Ajax Stript info: http://www.hunlock.com/blogs/The_Ultimate_Ajax_Object -->
		<script type="text/javascript" src="<?php echo base_url() ?>js/uajax.js"></script>
		<!-- Card handling logic -->
		<script type="text/javascript" src="<?php echo base_url() ?>js/deckHandler.js"></script>
		<!-- Game JS -->
		<script type="text/javascript">
			/*******Common JS varible Section***************/
			var userId = <?php echo $this->ion_auth->user()->row()->id ?>;
			var deckHandler = new DeckHandler();
			var currentCard;
			var gameMode;
			/*variable for timer function*/
			var totalSeconds = 0;
			var timerIntervalId;
			/*game history length */
			var historyLength = 30;
			/*current deck array*/
			var currentDeckArray;
			var gameResults = new Object();
			var quickReviewLog = new Object();
			var totalDeckTime = 0;
			gameResults['correct_total'] = new Object();
			gameResults['wrong_total'] = new Object();
			gameResults['deck'] = new Object();
			gameResults['card_count'] = new Object();
			//	var gameResults['user_id'] = new Object();
			var gameCount = 0;
			var correctTotal = 0;
			var wrongTotal = 0;
			var totalCards = 0;
			/*******encode JSON objects for POST************/
			function preparePost(jsonObj) {
				return "data=" + JSON.stringify(jsonObj).replace(/&/g, "%26");
			}
			/*******JS to control main game Flow************/
			function startGame() {
				/*hide unanted screens*/
				gameScreen.style.display = "none";
				deckScreen.style.display = "none";
				modeScreen.style.display = "block";
			}
			/*******Set Game Mode and Load Card Decks************/
			function loadGameMode(gameModeIn, doLoadDecks) {
				deckHandler.reset();
				gameMode = gameModeIn;
				if (gameMode == 'RW') {
					loadReviewModeParams();
					if (doLoadDecks) {
						loadDecks();
					}
				}
			}
			function loadReviewModeParams() {
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_rm_params/",
					function(data, status) {
						if (status == 200) {
							reviwModeParams = JSON.parse(data);
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
								}
							}
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update();
			}
			/********Load Card Decks****************************/
			function loadDecks() {
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_decks/<?php echo $this->ion_auth->user()->row()->id ?>",
					function(data, status) {
						if (status == 200) {
							currentDeckArray = JSON.parse(data);
							deckScreen.style.display = "block";
							modeScreen.style.display = "none";
							/*render deck selection view*/
							renderDeckSelection(currentDeckArray);
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update();
			}

			function renderDeckSelection(deckArray) {
				var innerHtml = "";
				for (var i = 0; i < deckArray.length; i++) {
					innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='loadGame(" + deckArray[i]['deck_id'] + ")'><p>" + deckArray[i]['deck_name'] + "</p></div></div></div><br/><br/><br/>";
				}
				/*for multiple deck mode*/
				innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='loadGameMultiDeckMode()'><p>Play Multiple Decks</p></div></div></div><br/><br/><br/>";
				deckScreen.innerHTML = innerHtml;
			}
			function renderDeckMultiSelection(deckArray) {
				var innerHtml = "";
				for (var i = 0; i < deckArray.length; i++) {
					innerHtml = innerHtml + "<p><input type='checkbox'  id='chk_" + deckArray[i]['deck_id'] + "' name='" + deckArray[i]['deck_id'] + "'>" + deckArray[i]['deck_name'] + "</p><br/>";
				}
				/*play multiple deck mode*/
				innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='playMultiDeckMode()'><p>Play</p></div></div></div><br/><br/><br/>";
				deckScreen.innerHTML = innerHtml;
			}
			/*******Load/save game section********/
			function loadGame(deckIdIn) {
				gameResults = new Object();
				gameResults['correct_total'] = new Object();
				gameResults['wrong_total'] = new Object();
				gameResults['deck'] = new Object();
				gameResults['card_count'] = new Object();
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_cards/" + userId + "/" + deckIdIn, 
					function(data, status) {
						if (status == 200) {
							var cardArray = JSON.parse(data);
							/*add two extra varibles to cards*/
							for (var i = 0; i < cardArray.length; i++) {
								cardArray[i]['correct'] = 0;
								cardArray[i]['wrong'] = 0;
							}
							deckHandler.setDeck(cardArray);
							gameScreen.style.display = "block";
							modeScreen.style.display = "none";
							deckScreen.style.display = "none";
							showNextQues();
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update();
			}
			function saveCard(card) {
				/*set last shown time*/
				var time = new Date();
				var timeMils = time.getTime();
				card['last_shown'] = timeMils;
//				console.log("Saving Card: " + card['card_id']);
//				console.log("\tRecord ID:" + card['record_id'] + ", User Id:" + card['user_id']);
//				console.log("\tQuestion:" + card['question']);
//				console.log("\tCard Rank: " + card['rank']);
				new ajaxObject("<?php echo base_url() ?>index.php/game/save_user_card", 
					function(response, status) {
						if (status != 200) {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update(preparePost(card), "POST");
			}
			/*********Load Game multiple deck mode*********************/
			function loadGameMd(deckIds) {
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_cards_md/" + userId, 
					function(data, status) {
						if (status == 200) {
							var cardArray = JSON.parse(data);
							/*add two extra varibles to cards*/
							for (var i = 0; i < cardArray.length; i++) {
								cardArray[i]['correct'] = 0;
								cardArray[i]['wrong'] = 0;
							}
							deckHandler.setDeck(cardArray);
							gameScreen.style.display = "block";
							modeScreen.style.display = "none";
							deckScreen.style.display = "none";
							showNextQues();
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update(preparePost(deckIds), "POST");
			}
		
			/*********Load Game multiple deck mode QUICK Sounds*********************/
			function loadGameMd_Sound(deckIds) {
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_cards_md_sound/" + userId, 
					function(data, status) {
						if (status == 200) {
							var cardArray = JSON.parse(data);
							/*add two extra varibles to cards*/
							for (var i = 0; i < cardArray.length; i++) {
								cardArray[i]['correct'] = 0;
								cardArray[i]['wrong'] = 0;
							}
							deckHandler.setDeck(cardArray);
							gameScreen.style.display = "block";
							modeScreen.style.display = "none";
							deckScreen.style.display = "none";
							showNextQues();
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update(preparePost(deckIds), "POST");
			}
			/*********Load game mulit deck mode************************/
			function loadGameMultiDeckMode() {
				renderDeckMultiSelection(currentDeckArray);
			}
			/********Play game multi deck mode*************************/
			function playMultiDeckMode() {
				/*check the selected decks*/
				var selected = false;
				var decks = [];
				for (var i = 0; i < currentDeckArray.length; i++) {
					if (ui("chk_" + currentDeckArray[i]['deck_id']).checked) {
						decks.push(currentDeckArray[i]['deck_id']);
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
				currentCard = deckHandler.getNextCard(gameMode);
				gameResults['deck'][gameCount] = new Object();
				gameResults['deck'][gameCount]['deck_id'] = currentCard['deck_id'];
				gameResults['deck'][gameCount]['card_id'] = currentCard['card_id'];
				gameResults['deck'][gameCount]['history'] = currentCard['history'];
				flipBack();
				var avgTime = 0;
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				renderQuestion(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), currentCard['question']);
				quickReviewLog['before_history'] = gameResults['deck'][gameCount]['history'];
				quickReviewLog['reason'] = extraInfo.innerHTML;
				quickReviewLog['before_rank'] = currentCard['rank'];
				quickReviewLog['question'] = currentCard['question'];
				quickReviewLog['answer'] = currentCard['answer'];
				quickReviewLog['deck_id'] = currentCard['deck_id'];
				quickReviewLog['card_id'] = currentCard['card_id'];
				new ajaxObject("<?php echo base_url() ?>index.php/auth/get_log_utp", function(res, status) {
					if (status != 200 || res == "0") {
						alert('UTP retrieval failed!\n' + res);
					} else {
						console.log("utp=" + res);
						quickReviewLog['utp'] = res;
					}
				}).update(preparePost(quickReviewLog), "POST");
			}
			function showAns() {
				flip();
				/*stop the time up timer and get it value*/
				clearInterval(timerIntervalId);
				currentCard['last_time'] = totalSeconds;
				totalDeckTime += totalSeconds;
				var timeTakenForQues = getFormatedTime(totalSeconds);
				var avgTime = 0;
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				totalSeconds = 0;
				renderAnswer(gameMode, currentCard['history'], currentCard['test_history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), timeTakenForQues, currentCard['answer']);
			}
			function markAnswer(mark) {
				var isValid = mark > 0;
				deckHandler.handleCardStatus(currentCard, mark, gameMode, historyLength);
				totalCards++;
				gameResults['deck'][gameCount]['ans'] = isValid;
				gameResults['deck'][gameCount]['rank'] = currentCard['rank'];
				gameResults['deck'][gameCount]['reason'] = extraInfo.innerHTML;
			<?php if ($this->ion_auth->user()->row()->review_log_status == 0) {?>
				quickReviewLog['ans'] = isValid;
				quickReviewLog['after_history'] = currentCard['history'];
				quickReviewLog['test_history'] = currentCard['test_history'];
				quickReviewLog['after_rank'] = currentCard['rank'];
				new ajaxObject("<?php echo base_url() ?>index.php/auth/save_quick_review_log", 
					function(res, status) {
						if (status != 200) {
							alert('Quick review log save failed!\n' + res);
						}
					}).update(preparePost(quickReviewLog), "POST");
			<?php }?>
				gameCount++;
				if (isValid) {
					correctTotal++;
				} else {
					wrongTotal++;
				}
				saveCard(currentCard);
				showNextQues();
			}
			function finishGame() {
				/* show the deck selection screen */
				gameResults['card_count'] = totalCards;
				totalCards = 0;
				clearInterval(timerIntervalId);
				if (confirm("Do you really want to finish this game?")) {
					gameResults['total_time'] = totalDeckTime;
					totalDeckTime = 0;
					gameResults['correct_total'] = correctTotal;
					gameResults['wrong_total'] = wrongTotal;
					correctTotal = 0;
					wrongTotal = 0;
					if (gameMode == 'RW') {
						new ajaxObject("<?php echo base_url() ?>index.php/auth/reviewModeSave", 
							function(res, status) {
								if (status != 200) {
									alert('Review mode save failed!\n' + res);
								}
							}).update(preparePost(gameResults), "POST");
					}
					gameScreen.style.display = "none";
					modeScreen.style.display = "block";
				}
				else {
					startTimer(false);	//restart timer without reset
				}
			}
			/*******Card Flipping JS********/
			function flip() {
				qView.className += " fcardQuesFlip";
				aView.className += " fcardAnsFlip";
			}
			function flipBack() {
				qView.className = qView.className.replace
					(/(?:^|\s)fcardQuesFlip(?!\S)/g, '');
				aView.className = aView.className.replace
					(/(?:^|\s)fcardAnsFlip(?!\S)/g, '');
			}
			/********Card Content Rendering********/
			function renderQuestion(mode, history,test_history, rank, avgTime, ques) {
				qMode.innerHTML = "M:" + mode;
				qHistory.innerHTML = "H:" + history;
				qTestHistory.innerHTML = "Test H:" + test_history;
				qRank.innerHTML = "R:" + rank;
				qAvg.innerHTML = "Avg:" + avgTime;
				qContent.innerHTML = ques;
				/*Call timer function to set count up time*/
				startTimer(true);
			}
			function renderAnswer(mode, history, test_history, rank, avgTime, time, ans) {
				aMode.innerHTML = "M:" + mode;
				aHistory.innerHTML = "H:" + history;
				aTestHistory.innerHTML = "Test H:" + test_history;
				aRank.innerHTML = "R:" + rank;
				aAvg.innerHTML = "Avg:" + avgTime;
				aTime.innerHTML = "Time:" + time;
				aContent.innerHTML = ans;
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
				qTime.innerHTML = "Time:" + getFormatedTime(totalSeconds);
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
				window.location = "<?php echo base_url() ?>index.php/deck/create_deck";
			}
			/**"add ability for user to create new decks and edit personal decks"**/
			/***********Manage Deck Section**************/
			function manageDeck() {
				var userId = document.getElementById("userId").value;
				
				window.location = "<?php echo base_url() ?>index.php/game/user_delete_decks1/"+0+"/"+userId;
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
			/** NEW QUICK REVIEW **/
			function quickReview() {
				loadGameMode('RW', false);
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_decks_id/<?php echo $this->ion_auth->user()->row()->id ?>", 
					function(response, status) {
						if (status == 200) {
							loadGameMd(JSON.parse(response));
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update();
			}
			
			/** NEW QUICK REVIEW With Sound**/
			/**"add ability for user to create new decks and edit personal decks"**/
			function quickReviewSound() {
			
				loadGameMode('RW', false);
				new ajaxObject("<?php echo base_url() ?>index.php/game/load_decks_id/<?php echo $this->ion_auth->user()->row()->id ?>", 
					function(response, status) {
						if (status == 200) {
							loadGameMd(JSON.parse(response));
						} else {
							alert("Communication Error! Please check Your Network Connection!\nStatus Code: " + status);
						}
					}).update();
			}
			// register the handler 
			document.addEventListener('keyup', keyHandler, false);
		</script>
	</head>
	<body onload="startGame()">
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Flash Card Game</div>
				<div class="logOut">
					<div style="margin-left: 130px;">
						<?php
						$userId = "";	
						if ($this->ion_auth->logged_in()) {
							$user = $this->ion_auth->user()->row();
							$userName = $user->first_name;
							$userId = $user->id;
							echo "Logg out: " . anchor('game/logout', $userName);
						}
						?>
						<input type="hidden" value="<?php echo $userId; ?>" id="userId">
					</div>
				</div>
			</div>
			<!-- Flash card section -->
			<div class="fcardHodler" id="gameScreen">
				<div class="fcardFlipper">
					<!-- Question Card -->
					<div class="fcardQues" id="fcardQues">
						<div class="fcardHeadder">
							<div id="qMode" style="width:10%" class="fcardHeadderContent">Mode: Review</div>
							<div id="qHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="qTestHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="qRank" style="width:10%" class="fcardHeadderContent">Rank: 1</div>
							<div id="qAvg" style="width:15%" class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="qTime" style="width:15%" class="fcardHeadderContent">Time: 00.00</div>
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
							<div id="aHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="aTestHistory" style="width:25%" class="fcardHeadderContent">History:###</div>
							<div id="aRank" style="width:10%" class="fcardHeadderContent">Rank: 1</div>
							<div id="aAvg" style="width:15%" class="fcardHeadderContent">Avg Time: 04:45</div>
							<div id="aTime" style="width:15%" class="fcardHeadderContent">Time: 00.00</div>
							<div class="clearFloat"></div>
						</div>
						<div id="aContent" class="fcardContent">
							Answer
						</div>
						<div class="fcardFooterAns">
							<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:markAnswer(1);"><p>&#10004;</p></div></div></div> 
							<div class="buttonHolder"><div class="buttonInner"><div class="button red" onclick="javascript:markAnswer(0);"><p>&#10007;</p></div></div></div> 
							<div class="clearFloat"></div>
						</div>
					</div>
				</div>
				<div id="extraInfo" style="position:relative; z-index: 500000000000000000000;  top:-20px;"></div>
			</div>
			<!--QuickView not redirect same page-->
			<!-- Game mode and extra functions selector Screen -->
			<div class="gameModeScreen" id="gameModeScreen">
				<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:quickReview();"><p><span class="black">Quick Review</span></p></div></div></div> 
				<br/><br/><br/>
				<div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:loadGameMode('RW', true);"><p><span class="white">Review Mode</span></p></div></div></div> 
				<br/><br/><br/>
				<div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/quick_with_sound/<?php echo $this->ion_auth->user()->row()->id ?>" style="text-decoration:none;color:black"><p><span class="black">Quick Review With Sound</span></p></a></div></div>
				
				<br/><br/><br/>
				<div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/rw_with_sound" style="text-decoration:none;color:black"><p><span class="white">Review Mode With Sound</span></p></a></div></div> 
				
				<br/><br/><br/>
				<div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/quick_reverse_with_sound/<?php echo $this->ion_auth->user()->row()->id ?>" style="text-decoration:none;color:black"><p><span class="black">Quick Reverse With Sound</span></p></a></div></div>
				<br/><br/><br/>
				<div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/reverse_with_sound" style="text-decoration:none;color:black"><p><span class="white">Reverse Mode With sound</span></p></a></div></div> 
				
				<!-- <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:quickReviewSound();"><p>Quick Review With Sound</p></div></div></div>  -->
				<br/><br/><br/>
				<!--create new deck-->
				<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='newDeck()'><p>Create New Deck</p></div></div></div>
				<br/><br/><br/>
				<!--manage card decks-->
				<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='manageDeck()'><p>Manage Deck</p></div></div></div>
				<br/><br/><br/>
				<!--upload card decks
				<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='uploadDeck()'><p>Upload Deck</p></div></div></div>
				<br/><br/><br/>-->
			</div>
			<!-- Card Deck Selection Screen -->
			<div class="cardDeckSelectionScreen" id="cardDeckSelectionScreen">
			</div>
		</div>
		<script type="text/javascript">
			function ui(id) {
				return document.getElementById(id);
			}
			var deckScreen = ui("cardDeckSelectionScreen");
			var gameScreen = ui("gameScreen");
			var modeScreen = ui("gameModeScreen");
			var extraInfo = ui("extraInfo");
			var qView = ui("fcardQues");
			var aView = ui("fcardAns");
			var qTime = ui("qTime");
			var qMode = ui("qMode");
			var qHistory = ui("qHistory");
			var qTestHistory = ui("qTestHistory");
			var qRank = ui("qRank");
			var qAvg = ui("qAvg");
			var qContent = ui("qContent");
			var aTime = ui("aTime");
			var aMode = ui("aMode");
			var aHistory = ui("aHistory");
			var aTestHistory = ui("aTestHistory");
			var aRank = ui("aRank");
			var aAvg = ui("aAvg");
			var aContent = ui("aContent");
		</script>
	</body>
</html>
