/*TEMP - Review Mode variables all the times are in seconds*/
var maxNoShowTime = 4000000;
var rankInc = 1;
var rankDesc = 1;
var correctCountForInc = 1;
var wrongCountForDesc = 1;
var avgExceedRankDesc = 1;
var avgExceedPercentage = 0.5;
/*previously selected card index*/
var prevSelectedCardIndex = -1;
/*previously selected random number*/
var prevRandNum = -1;
/*algorithm alternation: 0 = RANDOM, 1 = OLDEST*/
var algoChoice = 0;
/*EXPIRED card queue (maxNoShowTime based); can grow limitlessly*/
var expiredQueue = [];
/*skip counter for skipping rounds, to show expired cards periodically*/
var expiredSkipCount = 0;
/*period after which a new expired card can be picked*/
var expiredCardPickPeriod = 10;
/*indicates whether SPECIAL FREQUENCY is active*/
var specialFreq = false;
/*SPECIAL FREQUENCY card queue; change 10 (length) as needed*/
var specialQueue = new Array(10);
specialQueue.size = 0;
specialQueue.start = 0;
specialQueue.end = 0;
/*adds card to queue*/
specialQueue.enqueue = function(cardID) {
	if (this.start == this.end && this.size == this.length) //full
	{
		return false; //enqueue failed
	}
	this[this.end] = cardID;
	this.size++; //1 element added
	this.end++;
	if (this.end == this.length) //wrap around
		this.end = 0; //to beginning
	return true; //enqueue success
}
/*fetches and removes next card from queue*/
specialQueue.dequeue = function() {
	if (this.start == this.end && this.size == 0) { //nothing to return
		return;
	}
	var temp = this[this.start]; //next output
	this.size--; //1 element removed
	this.start++;
	if (this.start == this.length) //wrap around
		this.start = 0; //to beginning
	return temp;
}
/*'shows' next card from queue*/
specialQueue.peek = function() {
	if (this.start == this.end && this.size == 0) { //nothing to 'show'
		return;
	}
	return this[this.start]; //'show' first card
}
/*SPECIAL FREQUENCY card skip sequences; add/edit skips as necessary*/
var skips = [
	[1, 1], //Skip 1 cards for 1 times 
	[3, 1] //Skip 3 cards for 1 times
];
/*skip progress of current SPECIAL FREQUENCY card in the format 
 [skipIndex, skipCount] matching above definition */
var skipIndex = 0; //index of current skip sequence
var skipCount = 0; //how many times the current skip sequence has run
/*frequency constants based on rank*/
var r1 = 40; //40
var r2 = 55; //15
var r3 = 65; //10
var r4 = 73; //8
var r5 = 80; //7
var r6 = 86; //6
var r7 = 91; //5
var r8 = 95; //4
var r9 = 98; //3
var r10 = 100; //2 //rand 13+
/****************************LEARN Logic Variables***************************/
var learn_correct_first_time = '5'; //First time shown marked right history mark
var learn_correct = 'L'; //Answered correctly history mark
var learn_wrong = '*'; //Answered incorrectly history mark
var L = 3; //Learning rank maximum
var learn_skips = {
	0: 1,
	1: 1,
	2: 2,
	3: 3,
	4: 4
}; //Skips to take per rank.  Format:  rank : #_of_skips
var first_time_correct_rank = 5; //Rank value for learning cards answered correctly on the first try.
/*******************************************************************************/
function DeckHandler() {}
DeckHandler.prototype.setDeck = function(deck) {
	this.deck = deck;
	var nowMils = new Date().getTime();
	matches = [];
	DATA_INDEX = 0;
	ERROR_INDEX = 1;
	/* preliminary scan of card deck, for initializing SPECIAL FREQUENCY and EXPIRED queues */
	for (var i in deck) {
		var card = deck[i];
		/* ignore LEARN cards when populating EXPIRED queue */
		this.checkHistory(card);
		if (card['learning'] != 1) {
			/* check if expired and add index to EXPIRED queue */
			var oldness = (nowMils - parseInt(card['last_shown'])) / 1000;
			if (oldness >= maxNoShowTime) {
				expiredQueue.push(i);
			}
		}
		/* check if 'failed' (having an X not preceded by an O) and add to matches list */
		m = card['history'].replace('oS', 'O').match(/^[^O]*X/);
		if (m && card['rank'] < 3) {
			specialFreq = true;
			temp = m[0].replace(/x/g, '_').split('').reverse().join(''); /* 'x' can intervene in localCompare() */
			matches.push([card, temp]);
		}
	}
	/* next, sort list by increasing order of consecutive errors */
	matches.sort(function(a, b) {
		return a[ERROR_INDEX].localeCompare(b[ERROR_INDEX]);
	});
	console.log('------------');
	/* now, populate SPECIAL FREQUENCY queue in reverse order */
	matches = matches.reverse();
	for (var i in matches) {
		console.log(matches[i][DATA_INDEX]['history']);
		if (!specialQueue.enqueue(matches[i][DATA_INDEX]))
			break;
	}
	console.log("Loaded " + expiredQueue.length + " cards to Expired queue");
	console.log('Loaded ' + specialQueue.size + ' cards that have not been reviewed after last test');
};
/*****card selection algorithms****/
/*get the card to show next - work as a delegate*/
DeckHandler.prototype.getNextCard = function(gameMode) {
	if (gameMode == "RW") {
		return this.getNextCardReviewMode();
	} else if (gameMode == "STST") {
		return this.getNextCardSupervisedPlusMode();
	}
};
/*get next card in review mode v1*/
DeckHandler.prototype.getNextCardReviewMode = function() {
	/*current time, for subsequent calculations*/
	var nowMils = new Date().getTime();
	/*0. check for SPECIAL FREQUENCY*/
	if (specialFreq) {
		var specialCard = this.checkSpecialFreq();
		if (specialCard != undefined)
			return specialCard;
	}
	/*1. look for LEARN cards, and update status of existing LEARN card */
	var inLearning = 0;
	console.log("Trying LEARN mode...");
	for (var i = 0; i < this.deck.length; i++) {
		/************************************************/
		var card = this.deck[i];
		this.checkHistory(card);
		/************************************************/
		if (card['learning'] == 1) {
			console.log('Learning card skips: ' + card['lskips'] + ' Rank: ' + card['rank']);
			if (card['rank'] < L && card['lskips'] < 1) {
				return this.setAsNextCard(i, "LEARN logic");
			} else if (card['lskips']-- > 0) {
				inLearning = 1;
				break;
			}
		}
	}
	/*2. pick from EXPIRED queue, if period has reached */
	if (expiredSkipCount++ % expiredCardPickPeriod == 0 && expiredQueue.length > 0) {
		return this.setAsNextCard(expiredQueue.pop(), "maxNoShowTime");
	}
	/*3. if expiry check fails, generate a random number and show a card accordingly*/
	/* decide between OLDEST and RANDOM; will alternate between
	 0 (RANDOM) and 1 (OLDEST). */
	algoChoice = 1 - algoChoice;
	/*i. get the psuod randoma number*/
	var randNum;
	do {
		randNum = parseInt(Math.random() * 100 + 1);
	}
	while (randNum == prevRandNum); //pick something different from previous
	prevRandNum = randNum;
	/*ii. get the rank regarding to it*/
	var rank = 1;
	if (randNum <= r1) {
		rank = 4;
	} else if (randNum > r1 && randNum <= r2) {
		rank = 5;
	} else if (randNum > r2 && randNum <= r3) {
		rank = 6;
	} else if (randNum > r3 && randNum <= r4) {
		rank = 7;
	} else if (randNum > r4 && randNum <= r5) {
		rank = 8;
	} else if (randNum > r5 && randNum <= r6) {
		rank = 9;
	} else if (randNum > r6 && randNum <= r7) {
		rank = 10;
	} else if (randNum > r7 && randNum <= r8) {
		rank = 11;
	} else if (randNum > r8 && randNum <= r9) {
		rank = 12;
	} else if (randNum > r9 && randNum <= r10) {
		rank = Math.floor(12 + 2 / (r10 - randNum));
	}
	console.log("Trying random rank mode...");
	console.log("Rand: " + randNum + ", Selectd Rank: " + rank);
	/*iii. search for a card from a random point*/
	var startPoint = Math.floor(Math.random() * this.deck.length);
	var firstRound = true;
	var selectedCardIndex = -1;
	var longestDelay = -1;
	var delay = 0;
	for (var j = startPoint; j < this.deck.length; j++) {
		var ch = this.deck[j]['history'].search(/[^-]/);
		if (ch == -1 || this.deck[j]['learning'] == 1)
			continue;
//		console.log("Current Card: " + j);
		if (j == startPoint) {
			if (!firstRound) {
				break;
			}
			firstRound = false;
		}
		delay = (nowMils - parseInt(this.deck[j]['last_shown'])) / 1000;
		if (rank == parseInt(this.deck[j]['rank'])) {
			if (algoChoice == 0) { //RANDOM
				// since we start from random point, no need to make a randomized pick again
				if (prevSelectedCardIndex != j) {
					selectedCardIndex = j;
					break;
				}
			} else if (delay >= longestDelay) { //OLDEST: candidate found
				if (prevSelectedCardIndex != j) {
					selectedCardIndex = j;
					longestDelay = delay;
				}
			}
		}
		if (j == (this.deck.length - 1)) {
			j = -1;
		}
	}
	//see if a card was selected
	if (selectedCardIndex != -1) {
		for (var i = 0; i < this.deck.length; i++) {
			if (this.deck[i]['learning'] == 1) {
				if (this.deck[selectedCardIndex]['alternate'] == 1) {
					this.deck[i]['lskips'] = 0;
				}
				break;
			}
		}
		return this.setAsNextCard(selectedCardIndex, (algoChoice == 0 ? "RANDOM" : "OLDEST") + " rank match: " + rank + ", Random Number: " + randNum);
	}
	console.log("No card for rank " + rank + "\nTrying random rank mode...");
	/*4. if above fails, get the oldest card from the smallest rank*/
	var minRank = Number.MAX_VALUE;
	longestDelay = -1;
	delay = 0;
	/* if random algorithm in use, list all lowest-rank cards
	 and pick a random index */
	if (algoChoice == 0) { //RANDOM
		var lowRanks = new Array();
		for (var i = 0; i < this.deck.length; i++) {
			var ch = this.deck[i]['history'].search(/[^-]/);
			if (ch == -1 || this.deck[i]['learning'] == 1)
				continue;
			rank = parseInt(this.deck[i]['rank']);
			if (rank < minRank) { //new lowest rank found
				minRank = rank;
				lowRanks = new Array(); //clear previous array
				lowRanks.push(i);
			} else if (rank == minRank) { //one more card of lowest rank
				lowRanks.push(i);
			}
		}
		//pick random index from lowest-rank cards
		selectedCardIndex = lowRanks[Math.floor(lowRanks.length * Math.random())];
	} else { //OLDEST
		//here we don't want to check for prevSelectedCardIndex, because it cannot be the 'longest-not-shown' card
		for (var i = 0; i < this.deck.length; i++) {
			var ch = this.deck[i]['history'].search(/[^-]/);
			if (ch == -1 || this.deck[i]['learning'] == 1)
				continue;
			rank = parseInt(this.deck[i]['rank']);
			delay = (nowMils - parseInt(this.deck[i]['last_shown'])) / 1000;
//			console.log("Card " + i + ": rank = " + rank + ", delay = " + delay);
			if (rank < minRank) { //lower rank; pick it
				longestDelay = delay;
				minRank = rank;
				selectedCardIndex = i; //this is the smallest, oldest card found so far
				console.log("Card " + i + " (min.rank: " + minRank + ") is the best choice so far");
			} else if (rank == minRank && delay > longestDelay) {
				//ranks equal; compare delay
				longestDelay = delay;
				selectedCardIndex = i; //this is the smallest, oldest card found so far
				console.log("Card " + i + " (min.rank and longest delay) is the best choice so far");
			}
		}
	}
	//see if a card was selected
	if (selectedCardIndex != -1) {
		return this.setAsNextCard(selectedCardIndex, (algoChoice == 0 ? "RANDOM" : "OLDEST") + " pick from min rank " + minRank + ", Random Number: " + randNum);
	}
	//prompt that min rank mode failed
	console.log("Failed to find a card for min rank: " + minRank);
	//if using learn logic select another new card
	if (inLearning == 1) {
		for (var i = 0; i < this.deck.length; i++) {
			if (this.deck[i]['learning'] == 1) {
				this.deck[i]['lskips'] = 0;
			} else if (this.deck[i]['alternate'] == 1) {
				return this.setAsNextCard(i, "not finding an old card; Random Number: " + randNum);
			} else if (this.deck[i]['last_shown'] == 0) {
				this.deck[i]['alternate'] = 1;
				return this.setAsNextCard(i, "not finding an old card; Random Number: " + randNum);
			}
		}
	}
	/*5. if everything above fails, use random selection*/
	var randomSelection = true;
	while (randomSelection) {
		/*select a card randomly*/
		console.log("######No card Selected: Using a random card!######");
		var stop = false;
		do {
			selectedCardIndex = Math.floor(Math.random() * this.deck.length);
			var ch = this.deck[selectedCardIndex]['history'].search(/[^-]/);
			if (ch != -1 && this.deck[i]['learning'] != 1)
				stop = true;
		} while (!stop);
		console.log("Last Shown - Now: " + (nowMils - parseInt(this.deck[selectedCardIndex]['last_shown'])) / 1000);
		if (selectedCardIndex != prevSelectedCardIndex) {
			randomSelection = false;
		}
	}
	return this.setAsNextCard(selectedCardIndex, "Random Index, Deck Size: " + this.deck.length + ", Random Number: " + randNum);
};
/*next card prompt and prevSelectedCardIndex update*/
DeckHandler.prototype.setAsNextCard = function(cardId, reason) {
	var prompt = "Selected Card: " + cardId + " based on " + reason;
	console.log("~~ " + prompt);
	document.getElementById("extraInfo").innerHTML = ">>" + prompt;
	prevSelectedCardIndex = cardId;
	return this.deck[cardId];
};
/*special frequency algorithm*/
DeckHandler.prototype.checkSpecialFreq = function() {
	//update control variables
	if (skipIndex < skips.length) { //still processing
		skipCount++; //update turn
		//If we skip n cards, we'll have to fire SPECIAL FREQUENCY every (n + 1)th time. So see if (n + 1) divides skipCount.
		if (skipCount % (skips[skipIndex][0] + 1) == 0) {
			//one skip cycle complete; return SPECIAL FREQUENCY card
			//back up card currently being reviewed (in case it gets removed during the variable update)
			var temp = specialQueue.peek();
			for (var i = 0; i < this.deck.length; i++) {
				if (this.deck[i]['learning'] == 1) {
					if (temp['alternate'] == 1) {
						this.deck[i]['lskips'] = 0;
					} else {
						--this.deck[i]['lskips'];
					}
					break;
				}
			}
			//mark chosen card so that it won't be selected in the next non-SPECIAL FREQUENCY attempt(s)
			var returnCard;
			for (var i = 0; i < this.deck.length; i++) {
				if (this.deck[i] == temp) {
					prevSelectedCardIndex = i; //won't be selected next time
					returnCard = this.setAsNextCard(i, "Special Frequency (sequence " + (skipIndex + 1) + ", cycle " + (skipCount / (skips[skipIndex][0] + 1)) + ")");
				}
			}
			/*	check if cycle has run the required number of times (i.e. whether the current sequence is complete) */
			if (skipCount / (skips[skipIndex][0] + 1) == skips[skipIndex][1]) { //one sequence is over
				skipCount = 0; //reset
				skipIndex++; //next sequence
				if (skipIndex == skips.length) //end of skip sequence list
				{
					skipIndex = 0; //reset
					console.log("Special frequency is over for card ID " + specialQueue.peek()['card_id']);
					specialQueue.dequeue(); //remove finished card from queue
					//see if more cards are in the queue
					if (specialQueue.peek() == undefined) { //no more cards
						specialFreq = false; //stop SPECIAL FREQUENCY
						console.log("Special frequency ended");
					} else {
						console.log("Special frequency will continue for card ID " + specialQueue.peek()['card_id']);
					}
				}
			}
			return returnCard;
		}
	} else { //error; impossible value
		console.log('Error occurred in SPECIAL FREQUENCY - invalid skipIndex');
		skipIndex = skipCount = 0; //start over
	}
};
/*get card in test supervisedplus mode*/
DeckHandler.prototype.getNextCardSupervisedPlusMode = function() {
	for (var selectedCardIndex = 0; selectedCardIndex < this.deck.length; selectedCardIndex++) {
		if (jQuery.inArray(this.deck[selectedCardIndex]['card_id'], pre_cards) == -1) {
			//add picked card to prev_cards
			pre_cards.push(this.deck[selectedCardIndex]['card_id']);
			var historyStr = this.deck[selectedCardIndex]['history'];
			if (historyStr == null || historyStr == '' || historyStr.match(/[^-]/) == null) {
				first_time_card_count++;
			}
			return this.setAsNextCard(selectedCardIndex, "Supervised Plus logic");
		}
	}
	//if nothing found
	finishSupervisedMode();
};
/*update a card*/
DeckHandler.prototype.updateCard = function(card) {
	var cardId = card['card_id'];
	var length = this.deck.length,
			tmpCard = null;
	for (var i = 0; i < length; i++) {
		tmpCard = this.deck[i];
		if (tmpCard['card_id'] == cardId) {
			this.deck[i] = card;
			break;
		}
	}
};
/*manage the history of a card*/
DeckHandler.prototype.handleCardStatus = function(card, ansCorrect, gameMode, historyLength, variableOk) {
//	console.log("Updating card: current rank = " + card['rank'] + ", answer = " + (ansCorrect == 1 ? "correct" : "wrong") + ", history = " + card['history']);
	/*handle the rank*/
	var rank = parseInt(card['rank']);
	if (ansCorrect == 1) {
		if (card['wrong'] > 0) {
			card['wrong'] = 0;
		}
		card['correct'] = card['correct'] + 1;
		if (gameMode == 'RW' || gameMode == 'STST') {
//			console.log(card);
			if (card['correct'] == correctCountForInc) {
				rank = rank + rankInc;
				if (card['learning'] == 1) {
					card['lskips'] = learn_skips[rank];
				}
				card['correct'] = 0;
			}
		}
	} else if (ansCorrect == 2) {
		if (gameMode == 'RW' || gameMode == 'STST') {
			rank = rank + variableOk;
			if (rank < 0) {
				rank = 0;
			}
		}
	} else {
		if (card['correct'] > 0) {
			card['correct'] = 0;
		}
		card['wrong'] = card['wrong'] + 1;
		if (gameMode == 'RW' || gameMode == 'STST') {
			if (card['wrong'] == wrongCountForDesc) {
				rank = rank - rankDesc;
				if (rank < 0) {
					rank = 0;
				}
				if (card['learning'] == 1) {
					card['lskips'] = learn_skips[rank];
				}
				card['wrong'] = 0;
			}
		}
	}
	card['rank'] = rank;
	/*handle the history string*/
	var historyStr = card['history'];
	if (ansCorrect != 1) {
		if (historyStr.substring(0, 2) == 'xx' || historyStr.substring(0, 2) == 'XX') {
			card['wrong_twice_or_more_count'] = card['wrong_twice_or_more_count'] + 1;
			game_results['wrong_twice_or_more_count'] = game_results['wrong_twice_or_more_count'] + 1;
		}
	}
	//decide on the character to be placed in history
	var recordChar = '';
	if (gameMode == 'TR' || gameMode == 'RW') {
		if (specialFreq && specialQueue.peek() == card && card['learning'] != 1)
			recordChar = (ansCorrect == 1 ? 'S' : '%');
		else if (card['learning'] == 1) {
			var ch = card['history'].search(/[^-]/);
			if (ch == -1) {
				recordChar = (ansCorrect == 1 ? learn_correct_first_time : learn_wrong);
				if (ansCorrect == 1) {
					card['rank'] = first_time_correct_rank;
					card['learning'] = 0;
					card['lskips'] = 0;
				}
			} else
				recordChar = (ansCorrect == 1 ? learn_correct : learn_wrong);
		} else
			recordChar = (ansCorrect == 1 ? 'o' : 'x');
	} else if (gameMode == 'SUP') {
		if (specialFreq && specialQueue.peek() == card)
			recordChar = (ansCorrect == 1 ? 'S' : '%');
		else
			recordChar = (ansCorrect == 1 ? 'O' : 'X');
	} else if (gameMode == 'STST') {
		//recordChar = (ansCorrect == 1 ? 'O' : 'X');
		if (ansCorrect == 1) {
			recordChar = 'O';
		} else if (ansCorrect == 3) {
			recordChar = 'P';
		} else {
			recordChar = 'X';
		}
	}
	//get the last occuranceof '-' in the string
	var lastOccr = historyStr.lastIndexOf('-');
	if (lastOccr >= 0) {
		historyStr = setCharAt(historyStr, lastOccr, recordChar);
	} else {
		historyStr = recordChar + historyStr.substr(0, historyLength - 2);
	}
	card['history'] = historyStr;
//	console.log(card);
//	console.log("Card updated: new rank = " + card['rank'] + ", new history = " + card['history']);
	if (ansCorrect == 0) //marked wrong; start SPECIAL FREQUENCY
	{
		if (card['learning'] != 1 && card['alternate'] != 1) {
			//start processing from next turn (if not already in SPECIAL FREQUENCY)
			specialFreq = true;
			//if the card currently in SPECIAL FREQUENCY was marked wrong
			if (specialQueue.peek() == card) {
//				console.log("Card " + card['card_id'] + " marked wrong, " + "moving it to start of queue");
				//reset controls (start over)
				skipIndex = 0;
				skipCount = 0;
			} else {
				//a different card was marked wrong; add it to queue
//				console.log("Card " + card['card_id'] + " marked wrong, " + "adding to SPECIAL FREQUENCY queue");
				//add to queue
				specialQueue.enqueue(card);
			}
		}
	}
};
DeckHandler.prototype.checkHistory = function(card) {
	var ch = card['history'].search(/[^-]/);
	if (ch == -1) {
		card['learning'] = 1;
		card['lskips'] = 0;
	} else if (card['learning'] == 1 && card['rank'] == L)
		card['learning'] = 0;
};
/****************Util Section**********************/
/*char replacement algorithm for js*/
function setCharAt(str, index, chr) {
	if (index > str.length - 1)
		return str;
	return str.substr(0, index) + chr + str.substr(index + 1);
}
;
