<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game</title>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv = "content-type" content = "text/html" charset = "UTF-8" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
        <!-- Ulatimate Ajax Stript info: http://www.hunlock.com/blogs/The_Ultimate_Ajax_Object -->
        <script type="text/javascript" src="<?php echo base_url() ?>js/uajax.js"></script>
        <!-- Card handling logic -->
        <script type="text/javascript" src="<?php echo base_url() ?>js/deckHandler.js"></script>
        <!-- Game JS -->
        <script type="text/javascript">
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
			var change_minus = 0;
			game_results['correct_total'] = new Object();
			game_results['wrong_total'] = new Object();
			game_results['deck'] = new Object();
			game_results['card_count'] = new Object();
			//    var game_results['user_id'] = new Object();
			var current_user_id = 0;
			var game_count = 0;
			var correct_total = 0;
			var wrong_total = 0;
			var first_time_card_count = 0;
			var first_time_correct_Card_cout = 0;
			var total_cards = 0;
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
					innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadGame(" + deckArray[i]['deck_id'] + ")'><p>" + deckArray[i]['deck_name'] + "</p></div></div></div><br/><br/><br/>";
				}
				/*for multiple deck mode*/
				innerHtml = innerHtml + "<div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:loadGameMultiDeckMode()'><p>Play Multiple Decks</p></div></div></div><br/><br/><br/>";
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
				var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_cards/" + userId + "/" + deckId;
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
					console.log(cardArray);
					deckHander.setDeck(cardArray);
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
				console.log("Saving Card: " + card['card_id']);
				card['last_shown'] = timeMils;
				console.log("\tRecord ID:" + card['record_id'] + ", User Id:" + card['user_id']);
				console.log("\tQuestion:" + card['question']);
				console.log("\tCard Rank: " + card['rank']);
				myRequest.update('data=' + JSON.stringify(card), 'POST');
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
				var loadGameAjaxPath = "<?php echo base_url() ?>index.php/game/load_cards_md/" + userId + "/" + deckIds;
				var myRequest = new ajaxObject(loadGameAjaxPath, loadGameHandlerMd, loadGameResponseMd, loadGameResponseStatusMd);
				myRequest.update();
			}
			function loadGameHandlerMd(loadGameResponseMd, loadGameResponseStatusMd) {
				if (loadGameResponseStatusMd == 200) {
					var deckList = loadGameResponseMd.split(']');
					var responseCleaned = deckList[0];
					for (var i = 1; i < deckList.length; i++)
						if (deckList[i].length > 0)
							responseCleaned += ',' + deckList[i];
					responseCleaned += ']';
					cardArray = eval('(' + responseCleaned + ')');
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
				currentCard = deckHander.getNextCard(gameMode);
				flipBack();
				var avgTime = 0;
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				renderQuestion(gameMode, currentCard['history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), currentCard['question']);
			}
			function showAns() {
				flip();
				/*stop the time up timer and get it value*/
				clearInterval(timerIntervalId);
				currentCard['last_time'] = totalSeconds;
				var timeTakenForQues = getFormatedTime(totalSeconds);
				var avgTime = 0;
				if (parseInt(currentCard['play_count']) != 0) {
					avgTime = currentCard['total_time'] / currentCard['play_count'];
				}
				totalSeconds = 0;
				renderAnswer(gameMode, currentCard['history'], currentCard['rank'], getFormatedTime(parseInt(avgTime)), timeTakenForQues, currentCard['answer']);
			}
			function ansCorrect() {
//                     if(game_results['deck'][game_count]['history']=='----------' || game_results['deck'][game_count]['history']=='' || game_results['deck'][game_count]['history']==null)
//                             {
//                                 
//                                first_time_correct_Card_cout++;
//                             }
//                        game_results['deck'][game_count]['ans']='true';
				total_cards++;
				game_results['deck'][game_count]['ans'] = 'true';
				game_count++;
				correct_total++;
				game_results[correct_total]
				var ansStatus = new Boolean(1);
				deckHander.handleCardStatus(currentCard, ansStatus, gameMode, historyLength);
				saveCard(currentCard);
				showNextQues();
			}
			function ansWrong() {
				total_cards++;
				game_results['deck'][game_count]['ans'] = 'false';
				// game_results['deck'][game_count]['ans']='false';
				game_count++;
				wrong_total++;
				var ansStatus = new Boolean(0);
				deckHander.handleCardStatus(currentCard, ansStatus, gameMode, historyLength);
				saveCard(currentCard);
				showNextQues();
			}
			function finishGame() {
				/* show the deck selection screen */
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
					if (gameMode == 'RW')
					{
						$.post(base_url + "/index.php/auth/reviewModeSave", {"data": game_results}, function(res) {
							if (res != 'success')
							{
								alert(res);
							}
						});
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
			function renderQuestion(mode, history, rank, avgTime, ques) {
				document.getElementById("qMode").innerHTML = "M:" + mode;
				document.getElementById("qHistory").innerHTML = "H:" + history;
				document.getElementById("qRank").innerHTML = "R:" + rank;
				document.getElementById("qAvg").innerHTML = "Avg:" + avgTime;
				document.getElementById("qContent").innerHTML = ques;
				/*Call timer function to set count up time*/
				startTimer(true);
			}
			function renderAnswer(mode, history, rank, avgTime, time, ans) {
				document.getElementById("aMode").innerHTML = "M:" + mode;
				document.getElementById("aHistory").innerHTML = "H:" + history;
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
				if (e.keyCode == e.DOM_VK_LEFT) {		//left arrow
					showAns();
					ansCorrect();
				}
				else if (e.keyCode == e.DOM_VK_RIGHT) {	//right arrow
					showAns();
					ansWrong();
				}
				else if (e.keyCode == e.DOM_VK_UP) {	//up arrow
					showAns();
					finishGame();
				}
				else if (e.keyCode == e.DOM_VK_DOWN) {	//down arrow
					showAns();
				}
			}
			/** NEW QUICK REVIEW **/
			function quick_review() {
				setGameModeAndLoadDecks('RW');
				var loadDecksAjaxPath = "<?php echo base_url() ?>index.php/game/load_decks_id/<?php echo $this->ion_auth->user()->row()->id ?>";
				$.post(loadDecksAjaxPath, {"data": ""}, function(res) {
					//loadGame(res);
					loadGameMd(res);
					// alert(res);
				});
				/* var myRequest = new ajaxObject(loadDecksAjaxPath, loadDecksHandler, loadDecksResponse, loadDecksResponseStatus);
				 //alert(myRequest); loadGameMd(myRequest);
				 //loadGameMd('123_12');
				 alert(myRequest.update()); //alert(myRequest); */
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
                            <div id="qMode" style="width:10%" class="fcardHeadderContent">Mode: Review</div>
                            <div id="qHistory" style="width:50%" class="fcardHeadderContent">History:###</div>
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
                            <div id="aHistory" style="width:50%" class="fcardHeadderContent">History:###</div>
                            <div id="aRank" style="width:10%" class="fcardHeadderContent">Rank: 1</div>
                            <div id="aAvg" style="width:15%" class="fcardHeadderContent">Avg Time: 04:45</div>
                            <div id="aTime" style="width:15%" class="fcardHeadderContent">Time: 00.00</div>
                            <div class="clearFloat"></div>
                        </div>
                        <div id="aContent" class="fcardContent">
                            Answer
                        </div>
                        <div class="fcardFooterAns">
                            <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:ansCorrect();"><p>&#10004;</p></div></div></div> 
                            <div class="buttonHolder"><div class="buttonInner"><div class="button red" onclick="javascript:ansWrong();"><p>&#10007;</p></div></div></div> 
                            <div class="clearFloat"></div>
                        </div>
                    </div>
                </div>
                <div id="extraInfo" style="position:relative; z-index: 500000000000000000000;  top:-20px;"></div>
            </div>
            <!--QuickView not redirect same page-->
            <!-- Game mode and extra functions selector Screen -->
            <div class="gameModeScreen" id="gameModeScreen">
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:quick_review();"><p>Quick Review</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('TR');"><p>Training Mode</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('RW');"><p>Review Mode</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/rw_with_sound" style="text-decoration:none;color:black"><p>Review Mode With Sound</p></a></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:setGameModeAndLoadDecks('TST');"><p>Test Mode</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/supervised_mode" style="text-decoration:none;color:black" ><p>Supervised Test</p></a></div></div>
                <br/><br/><br/>
                <!--create new deck-->
                <div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:newDeck()'><p>New Deck</p></div></div></div>
                <br/><br/><br/>
                <!--manage card decks-->
                <div class='buttonHolder'><div class='buttonInner'><div class='button green' onclick='javascript:manageDeck()'><p>Manage Deck</p></div></div></div>
                <br/><br/><br/>
            </div>
            <!-- Card Deck Selection Screen -->
            <div class="cardDeckSelectionScreen" id="cardDeckSelectionScreen">
            </div>
        </div>
    </body>
</html>
