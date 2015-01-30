<?php

class card extends CI_Model {

    function __construct() {

        // Call the Model constructor

        parent::__construct();
    }

    /* return arry of card decks in the database */

    function load_decks($userId) {

        if ($userId == -1) {

            $this->db->select('*');

            $this->db->from('card_deck');

            $query = $this->db->get();

            return $query->result();
        }



        /* get the admin users */

        /* $this->db->select('*');

          $this->db->from('users_groups');

          $this->db->where('group_id','1');

          $adminUsersQry = $this->db->get();

          $adminUsers = $adminUsersQry->result();

         */



        $this->db->select('*');

        $this->db->from('users_groups');

        $this->db->where('user_id', $userId);

        $adminUsersQry = $this->db->get();

        $adminUsers = $adminUsersQry->result();



        //Getting decks by assigned groups

        $deck_id_array = $this->get_assigned_groups_decks_by_user($userId);

        $deck_id_array_by_user = $this->get_assigned_decks_by_user($userId);

        if (!is_array($deck_id_array_by_user)) {

            $deck_id_array_by_user = array();
        }



        if (!is_array($deck_id_array)) {

            $deck_id_array = array();
        }

        //Merge array

        array_merge($deck_id_array, $deck_id_array_by_user);



        // print_r($adminUsers); die;

        /* get the cards from admin user and the logged user */

        $this->db->select('*');

        $this->db->from('card_deck');

        $this->db->where('created_user_id', $userId);



        if (!empty($deck_id_array)) {

            $this->db->or_where_in('deck_id', $deck_id_array);
        }



        foreach ($adminUsers as $user) {

            //Commented on 12-06-2014 Because it is showing every deck to each user.

            $this->db->or_where('created_user_id', $user->user_id);
        }

        $query = $this->db->get(); //echo $this->db->last_query();

        return $query->result();
    }

    /*     * Load Decks for plus mode parameters Ashvin Patel 19/jun/2014* */

    function load_plus_decks($userId) {

        if ($userId == -1) {

            $this->db->select('*');

            $this->db->from('card_deck');

            $query = $this->db->get();

            return $query->result();
        }

        $this->db->select('*');

        $this->db->from('users_groups');

        $this->db->where('user_id', $userId);

        $adminUsersQry = $this->db->get();

        $adminUsers = $adminUsersQry->result();



        //Getting decks by assigned groups

        $deck_id_array = $this->get_assigned_groups_decks_by_user($userId);

        $deck_id_array_by_user = $this->get_assigned_decks_by_user($userId);

        if (!is_array($deck_id_array_by_user)) {

            $deck_id_array_by_user = array();
        }



        if (!is_array($deck_id_array)) {

            $deck_id_array = array();
        }

        //Merge array

        array_merge($deck_id_array, $deck_id_array_by_user);



        // print_r($adminUsers); die;

        /* get the cards from admin user and the logged user */

        $this->db->select('*');

        $this->db->from('card_deck');

        $this->db->where('created_user_id', $userId);



        if (!empty($deck_id_array)) {

            $this->db->or_where_in('deck_id', $deck_id_array);
        }



        foreach ($adminUsers as $user) {

            //Commented on 12-06-2014 Because it is showing every deck to each user.

            $this->db->or_where('created_user_id', $user->user_id);
        }

        $query = $this->db->get(); //echo $this->db->last_query();

        return $query->result();
    }

    function load_plus_decks_more($userId) {

        if ($userId == -1) {
            $this->db->select('*');
            $this->db->from('card_deck');
            $query = $this->db->get();
            return $query->result();
        }

        $this->db->select('*');
        $this->db->from('users_groups');
        $this->db->where('user_id', $userId);
        $adminUsersQry = $this->db->get();
        $adminUsers = $adminUsersQry->result();

        //Getting decks by assigned groups

        $deck_id_array = $this->get_assigned_groups_decks_by_user($userId);
        $deck_id_array_by_user = $this->get_assigned_decks_by_user($userId);
        if (!is_array($deck_id_array_by_user)) {
            $deck_id_array_by_user = array();
        }

        if (!is_array($deck_id_array)) {
            $deck_id_array = array();
        }

        //Merge array
        array_merge($deck_id_array, $deck_id_array_by_user);

        // print_r($adminUsers); die;

        /* get the cards from admin user and the logged user */

        /* $this->db->select('*, count(card_in_deck.card_id) as decks_count');
          $this->db->from('card_deck');
          $this->db->join('card_in_deck', 'card_in_deck.deck_id = card_deck.deck_id');
          $this->db->where('card_deck.created_user_id',$userId); */

        $this->db->select('*');
        $this->db->from('card_deck');
        $this->db->where('card_deck.created_user_id', $userId);

        if (!empty($deck_id_array)) {
            $this->db->or_where_in('card_deck.deck_id', $deck_id_array);
        }



        foreach ($adminUsers as $user) {
            //Commented on 12-06-2014 Because it is showing every deck to each user.
            $this->db->or_where('card_deck.created_user_id', $user->user_id);
        }

        $query = $this->db->get(); // echo $this->db->last_query();
        return $query->result();
    }

    function neverTestedCards($userId) {
        $most_inc_card_ids = array();
        $inc_card_ids = array();
        $cor_card_ids = array();
        $neverTestcards = array();
        $this->db->select('card_id, test_history');
        $this->db->from('user_card');
        $this->db->where('user_id', $userId);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $result = $query->result();
        foreach ($result as $cards) {
            $card_history = str_replace('-', '', $cards->test_history);
            $card_history_0 = str_replace('x', '', $card_history);
            $card_history_1 = str_replace('o', '', $card_history_0);
            $doubleX = substr($card_history_1, 0, 2);
            $singleX = substr($card_history_1, 0, 1);
            if ($doubleX != '' && $doubleX != null && $singleX != '' && $singleX != null) {
                if ($doubleX == 'XX') {
                    $most_inc_card_ids[] = $cards->card_id;
                } else if ($singleX == 'X') {
                    $inc_card_ids[] = $cards->card_id;
                } else {
                    $cor_card_ids[] = $cards->card_id;
                }
            } else {
                $neverTestcards[$cards->card_id] = $card_history_1;
            }
            /* if($card_history_1==''){
              $neverTestcards[$cards->card_id] = 	$card_history_1;
              } */
        }
        $card_count['XX'] = count($most_inc_card_ids);
        $card_count['X'] = count($inc_card_ids);
        $card_count['O'] = count($cor_card_ids);
        $card_count['N'] = count($neverTestcards);

        return $card_count;
    }

    /* Get Deck's details of Assigned Group by user id */

    function get_assigned_groups_decks_by_user($userId) {

        $this->db->select('*');

        $this->db->from('users_groups');

        $this->db->where('user_id', $userId);

        $user_groups = $this->db->get();

        $user_groups_data = $user_groups->result();



        foreach ($user_groups_data as $user) {

            $group_id[] = $user->group_id;
        }



        $this->db->select('*');

        $this->db->from('groups');

        $this->db->where_in('id', $group_id);

        $groups = $this->db->get();

        $groups_data = $groups->result();



        foreach ($groups_data as $group_record) {

            $group_rec = explode(',', $group_record->deck);

            foreach ($group_rec as $res) {

                $deck_array[] = $res;
            }
        }

        return $deck_array;
    }

    function get_assigned_decks_by_user($userId) {

        $this->db->select('*');

        $this->db->from('users');

        $this->db->where_in('id', $userId);

        $groups = $this->db->get();

        $groups_data = $groups->row();

        if (!empty($groups_data)) {

            return explode(',', $groups_data->deck);
        }

        return $groups_data->deck;
    }

    /* return arry of cards from some deck for a particuler user */

    function load_cards($user_id, $deck_id) {



        if ($deck_id == -1) {



            $this->db->select('*,cd.deck_name');

            $this->db->from('user_card');

            $this->db->join('card', ' user_card.card_id = card.card_id');
            $this->db->join('card_deck cd', 'cd.deck_id = user_card.deck_id'); /* show DONE msg */

            $this->db->where('user_card.user_id', $user_id); /* show DONE msg */



            $query = $this->db->get(); /* Show DONE msg */







            return $query->result();
        }



        /* check wheather the user previouly plid the selection */

        $this->db->select('*');

        $this->db->from('user_card');

        $this->db->join('card', ' user_card.card_id = card.card_id');

        $this->db->where('user_card.deck_id ', $deck_id);

        $this->db->where('user_card.user_id', $user_id);



        if ($this->db->count_all_results() <= 0) {



            /* first time for this card set */

            $this->db->select('*');

            $this->db->from('card_in_deck');

            $this->db->where('deck_id ', $deck_id); /* Show DONE msg */



            $query = $this->db->get();

            $cardInDeck = $query->result();



            /* add card to the user card table */

            foreach ($cardInDeck as $card) {

                $this->db->set('user_id', $user_id);

                $this->db->set('deck_id', $deck_id);

                $this->db->set('card_id', $card->card_id);

                $this->db->insert('user_card');
            }
        }

        /* send card deck to the user */

        $this->db->select('*');

        $this->db->from('user_card');

        $this->db->join('card', ' user_card.card_id = card.card_id');

        $this->db->where('user_card.deck_id ', $deck_id);

        $this->db->where('user_card.user_id', $user_id);



        $query = $this->db->get();

        $query->result();

        //echo $str = $this->db->last_query();
        // die();

        return $query->result();
    }

    /*     * Load plus mode cards Ashvin Patel 19/jun/2014* */

    function load_plus_mode_cards($user_id, $deck_id, $total_cards) {

        $c = $total_cards;
        $c2 = $total_cards;
        $card_ids = array();
        $most_inc_card_ids = array();
        $inc_card_ids = array();
        $cor_card_ids = array();
        $never_tested_card_ids = array();

        $card_ids2 = array();
        $most_inc_card_ids2 = array();
        $inc_card_ids2 = array();
        $cor_card_ids2 = array();
        $never_tested_card_ids2 = array();
        /* if($deck_id == -1){
          $this->db->select('*');
          $this->db->from('user_card');
          $this->db->join('card',' user_card.card_id = card.card_id'); /* show DONE msg *
          $this->db->where('user_card.user_id', $user_id); /* show DONE msg *
          $query = $this->db->get(); /* Show DONE msg *
          return $query->result();
          } */
        /* check wheather the user previouly plid the selection */

        $this->addUserCardIfNot($user_id, $deck_id);

        /* send card deck to the user */

        $this->db->select('*');
        $this->db->from('user_card as uc');
        $this->db->join('card as c', ' uc.card_id = c.card_id');
        if ($deck_id != '' || $deck_id != 0 || $deck_id != null) {
            $this->db->where('uc.deck_id ', $deck_id);
        }
        $this->db->where('uc.user_id', $user_id);
        //$this->db->where('REPLACE(uc.history, "-", "") LIKE "X%"');

        $this->db->order_by('rank');

        $query = $this->db->get();
        $result = $query->result();
        //return $query->result();



        foreach ($result as $cards) {
            $card_history = str_replace('-', '', $cards->test_history);
            $card_history_0 = str_replace('x', '', $card_history);
            $card_history_1 = str_replace('o', '', $card_history_0);
            $doubleX = substr($card_history_1, 0, 2);
            $singleX = substr($card_history_1, 0, 1);
            if ($doubleX != '' && $doubleX != null && $singleX != '' && $singleX != null) {
                if ($doubleX == 'XX') {
                    $most_inc_card_ids[] = $cards->card_id;
                } else if ($singleX == 'X') {
                    $inc_card_ids[] = $cards->card_id;
                } else {
                    $cor_card_ids[] = $cards->card_id;
                }
            } else {
                $never_tested_card_ids[] = $cards->card_id;
            }
        }


        if (!empty($most_inc_card_ids) && $c > 0) {
            foreach ($most_inc_card_ids as $most_inc_card_id) {
                if ($c > 0) {
                    $card_ids[] = $most_inc_card_id;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($inc_card_ids) && $c > 0) {
            foreach ($inc_card_ids as $inc_card_id) {
                if ($c > 0) {
                    $card_ids[] = $inc_card_id;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($never_tested_card_ids) && $c > 0) {
            foreach ($never_tested_card_ids as $never_tested_card_id) {
                if ($c > 0) {
                    $card_ids[] = $never_tested_card_id;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($cor_card_ids) && $c > 0) {
            foreach ($cor_card_ids as $cor_card_id) {
                if ($c > 0) {
                    $card_ids[] = $cor_card_id;
                    $c--;
                }
            }
        }








        $this->db->select('*');
        $this->db->from('user_card as uc');
        $this->db->join('card as c', ' uc.card_id = c.card_id');
        if ($deck_id != '' || $deck_id != 0 || $deck_id != null) {
            $this->db->where('uc.deck_id ', $deck_id);
        }
        $this->db->where('uc.user_id', $user_id);
        //$this->db->where('REPLACE(uc.history, "-", "") LIKE "X%"');

        $this->db->order_by('last_date');

        $query2 = $this->db->get();
        $result2 = $query2->result();

        foreach ($result2 as $cards2) {
            $card_history2 = str_replace('-', '', $cards2->test_history);
            $card_history_02 = str_replace('x', '', $card_history2);
            $card_history_12 = str_replace('o', '', $card_history_02);
            $doubleX2 = substr($card_history_12, 0, 2);
            $singleX2 = substr($card_history_12, 0, 1);
            if ($doubleX2 != '' && $doubleX2 != null && $singleX2 != '' && $singleX2 != null) {
                if ($doubleX2 == 'XX') {
                    $most_inc_card_ids2[] = $cards2->card_id;
                } else if ($singleX2 == 'X') {
                    $inc_card_ids2[] = $cards2->card_id;
                } else {
                    $cor_card_ids2[] = $cards2->card_id;
                }
            } else {
                $never_tested_card_ids2[] = $cards2->card_id;
            }
        }


        if (!empty($most_inc_card_ids2) && $c2 > 0) {
            foreach ($most_inc_card_ids2 as $most_inc_card_id2) {
                if ($c2 > 0) {
                    $card_ids2[] = $most_inc_card_id2;
                    $c2--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($inc_card_ids2) && $c2 > 0) {
            foreach ($inc_card_ids2 as $inc_card_id2) {
                if ($c2 > 0) {
                    $card_ids2[] = $inc_card_id2;
                    $c2--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($never_tested_card_ids2) && $c2 > 0) {
            foreach ($never_tested_card_ids2 as $never_tested_card_id2) {
                if ($c2 > 0) {
                    $card_ids2[] = $never_tested_card_id2;
                    $c2--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($cor_card_ids2) && $c2 > 0) {
            foreach ($cor_card_ids2 as $cor_card_id2) {
                if ($c2 > 0) {
                    $card_ids2[] = $cor_card_id2;
                    $c2--;
                }
            }
        }

        $xLimit = ceil(count($card_ids) / 2);

        $checkL1 = 0;
        foreach ($card_ids as $ci1) {
            if ($xLimit == $checkL1) {
                break;
            } else {
                $card_ids_merged2[] = $ci1;
            }
            $checkL1++;
        }

        $xLimit2 = floor(count($card_ids2) / 2);
        $checkL2 = 0;
        foreach ($card_ids2 as $ci2) {
            if ($xLimit2 == $checkL2) {
                break;
            } else {
                if (in_array($ci2, $card_ids_merged2)) {
                    $checkL2--;
                } else {
                    $card_ids_merged2[] = $ci2;
                }
            }
            $checkL2++;
        }

        $data['card_ids'] = $card_ids_merged2;
        $data['remain_cards'] = $c;
        /* if($c==0){
          $card_result_data = $this->loadCardData($card_ids);
          return $card_result_data;
          }else
          {
          return $data;
          } */
        return $data;
    }

    function loadCardData($card_ids, $user_id) {

        if (!empty($card_ids)) {
            foreach ($card_ids as $card_id) {
                $this->db->select('*');
                $this->db->from('user_card as uc');
                $this->db->join('card as c', ' uc.card_id = c.card_id');
                $this->db->where('uc.card_id', $card_id);
                $this->db->where('uc.user_id', $user_id);
                $query = $this->db->get();
                //echo $this->db->last_query();
                $result[] = $query->row();
            }
            return $result;
        }
    }

    function load_more_cards($user_id, $deck_id, $morecards, $multiplier) {

        //echo $multiplier;
        $c = $morecards;
        $card_ids = array();
        $most_inc_card_ids = array();
        $inc_card_ids = array();
        $cor_card_ids = array();
        $old_cards = array();
        $old_cards1 = array();
        $never_tested_card_ids = array();
        $this->db->select('*');
        $this->db->from('user_card as uc');
        $this->db->join('card as c', ' uc.card_id = c.card_id');
        $this->db->where('uc.deck_id ', $deck_id);
        //$this->db->where('uc.user_id', $user_id);
        //$this->db->where('REPLACE(uc.history, "-", "") LIKE "X%"');

        $query = $this->db->get();
        $result = $query->result();
        //return $query->result();
        foreach ($result as $cards) {
            $card_history = str_replace('-', '', $cards->history);
            $card_history_0 = str_replace('x', '', $card_history);
            $card_history_1 = str_replace('o', '', $card_history_0);
            $doubleX = substr($card_history_1, 0, 2);
            $singleX = substr($card_history_1, 0, 1);
            if ($doubleX != '' && $doubleX != null && $singleX != '' && $singleX != null) {
                if ($doubleX == 'XX' || $singleX == 'X') {
                    $c_date = strtotime(date('Y-m-d'));
                    $card_date = strtotime(date('Y-m-d', strtotime($cards->last_tested)));
                    $diff = ($c_date - $card_date) / (60 * 60 * 24);

                    if ($cards->play_count == '1' && $diff >= ($cards->play_count * $multiplier)) {
                        $old_cards[] = $cards->card_id;
                    } elseif ($cards->play_count == '2' && $diff >= ($cards->play_count * $multiplier)) {
                        $old_cards[] = $cards->card_id;
                    } elseif ($cards->play_count == '3' && $diff >= ($cards->play_count * $multiplier)) {
                        $old_cards[] = $cards->card_id;
                    } elseif ($cards->play_count == '4' && $diff >= ($cards->play_count * $multiplier)) {
                        $old_cards[] = $cards->card_id;
                    } elseif ($cards->play_count == '5' && $diff >= ($cards->play_count * $multiplier)) {
                        $old_cards[] = $cards->card_id;
                    } else {
                        $old_cards1[] = $cards->card_id;
                    }
                } else {
                    $cor_card_ids[] = $cards->card_id;
                }
            } else {
                $never_tested_card_ids[] = $cards->card_id;
            }
        }

        if (!empty($never_tested_card_ids) && $c > 0) {
            foreach ($never_tested_card_ids as $never_tested_card_id) {
                if ($c > 0) {
                    $card_ids[] = $never_tested_card_id;
                    $c--;
                }
            }
        }
        /* print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($old_cards) && $c > 0) {
            foreach ($old_card as $old_card) {
                if ($c > 0) {
                    $card_ids[] = $old_card;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($old_cards1) && $c > 0) {
            $oldest_cards = $this->getMostOldestCards($old_cards1);
            foreach ($oldest_cards as $oldest_card) {
                if ($c > 0) {
                    $card_ids[] = $oldest_card;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        if (!empty($cor_card_ids) && $c > 0) {
            foreach ($cor_card_ids as $cor_card_id) {
                if ($c > 0) {
                    $card_ids[] = $cor_card_id;
                    $c--;
                }
            }
        }
        /* echo '<br>';
          print_r($card_ids);
          echo '<br>'.$c; */
        //print_r($old_cards);
        //print_r($old_cards1);
        //print_r($cor_card_ids);
        //print_r($never_tested_card_ids);
        //print_r($card_ids);
        $data['card_ids'] = $card_ids;
        return $data;
    }

    /*     * Check if card exists for user if not add cards for user
      Ashvin Patel 19/jun/2014* */

    function addUserCardIfNot($user_id, $deck_id) {
        $user_decks = '';
        if ($deck_id != '' || $deck_id != 0 || $deck_id != null) {
            $this->insertDeckCards($user_id, $deck_id);
        } else {
            $user_decks = $this->getUserDeck($user_id);
            //print_r($user_decks);
            foreach ($user_decks as $deck_id) {
                $this->insertDeckCards($user_id, $deck_id);
            }
        }
    }

    function insertDeckCards($user_id, $deck_id) {
        $this->db->select('*');
        $this->db->from('user_card');
        $this->db->join('card', ' user_card.card_id = card.card_id');
        if ($deck_id != '' || $deck_id != 0 || $deck_id != null) {
            $this->db->where('user_card.deck_id ', $deck_id);
        }
        $this->db->where('user_card.user_id', $user_id);

        if ($this->db->count_all_results() <= 0) {
            /* first time for this card set */

            if ($deck_id == '' || $deck_id == 0 || $deck_id == null) {
                $user_decks = $this->getUserDeck($user_id);
            }
            if ($deck_id != '' || $deck_id != 0 || $deck_id != null || $user_decks != '') {
                $this->db->select('*');
                $this->db->from('card_in_deck');
                if ($deck_id != '' || $deck_id != 0 || $deck_id != null) {
                    $this->db->where('deck_id ', $deck_id); /* Show DONE msg */
                } else if (!empty($user_decks)) {
                    $this->db->where_in('deck_id ', $user_decks);
                }
                $query = $this->db->get();
                $cardInDeck = $query->result();
                //print_r($cardInDeck);
                /* add card to the user card table */

                foreach ($cardInDeck as $card) {
                    $this->db->set('user_id', $user_id);
                    $this->db->set('deck_id', $card->deck_id);
                    $this->db->set('card_id', $card->card_id);
                    $this->db->insert('user_card');
                }
            }
        }
    }

    function getUserDeck($user_id) {
        $user_decks = array();
        $this->db->select('id,deck');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $result = $query->row();
        $decks = $result->deck;
        if ($decks != '' || $decks != null) {
            $user_decks = explode(',', $decks);
        }
        //return 0;
        //print_r($user_decks);
        $userGroupDeck = $this->getUserGroup($user_id);
        //print_r($userGroupDeck);
        $userDecks = array_unique(array_merge($user_decks, $userGroupDeck));
        //print_r($userDecks);
        if (!empty($userDecks)) {
            return $userDecks;
        } else {
            return 0;
        }
    }

    function getUserGroup($user_id) {
        $userGroupDeck = array();
        $this->db->select('users_groups.id,users_groups.group_id,groups.deck');
        $this->db->from('users_groups');
        $this->db->join('groups', 'groups.id = users_groups.group_id');
        $this->db->where('users_groups.user_id', $user_id);
        $query = $this->db->get();
        $result = $query->result();
        //print_r($result);
        foreach ($result as $deck) {
            $GroupDecks = explode(',', $deck->deck);
            foreach ($GroupDecks as $GroupDeck) {
                if ($GroupDeck != '') {
                    $userGroupDeck[] = $GroupDeck;
                }
            }
        }
        return array_unique($userGroupDeck);
    }

    function getMostOldestCards($old_cards1) {

        $card_ids = array();
        $this->db->select('card_id');
        $this->db->from('user_card');
        $this->db->where_in('card_id', $old_cards1);
        $this->db->order_by('last_tested', 'desc');
        $query = $this->db->get();
        $result = $query->result();

        foreach ($result as $cards) {
            $card_ids[] = $cards->card_id;
        }
        //print_r($card_ids);
        return $card_ids;
    }

    /* return arry of cards for some multiple decks, which are seperated from undersore charactor for a particuler user */

    function load_cards_md($user_id, $deck_ids) {

        $deck_id_arr = explode("_", $deck_ids);



        if (sizeof($deck_id_arr) > 0) {

            foreach ($deck_id_arr as $deck_id) {

                /* check wheather the user previouly plid the selection */

                $this->db->select('*');

                $this->db->from('user_card');

                $this->db->join('card', ' user_card.card_id = card.card_id');

                $this->db->where('user_card.deck_id ', $deck_id);

                $this->db->where('user_card.user_id', $user_id);



                if ($this->db->count_all_results() <= 0) {



                    /* first time for this card set */

                    $this->db->select('*');

                    $this->db->from('card_in_deck');

                    $this->db->where('deck_id ', $deck_id);

                    $query = $this->db->get();

                    $cardInDeck = $query->result();



                    /* add card to the user card table */

                    foreach ($cardInDeck as $card) {

                        $this->db->set('user_id', $user_id);

                        $this->db->set('deck_id', $deck_id);

                        $this->db->set('card_id', $card->card_id);

                        $this->db->insert('user_card');
                    }
                }
            }



            /* load the cards */

            $this->db->select('*');

            $this->db->from('user_card');

            $this->db->join('card', ' user_card.card_id = card.card_id');

            $this->db->where('user_card.user_id', $user_id);

            //$this->db->where('user_card.deck_id ', $deck_id_arr[0]); /*simple hack which is OK*/


            if ($deck_id_arr) {
                $this->db->where_in('user_card.deck_id ', $deck_id_arr);
            }
            /*
              foreach($deck_id_arr as $deck_id){

              //send card deck to the user

              $this->db->or_where('user_card.deck_id ', $deck_id);

              }

             */


            $query = $this->db->get();

            return $query->result();
        } else {

            return NULL;
        }
    }

    /* save a card infomation user did some action on it */

    function save_user_card($record_id, $history, $test_history, $rank, $time, $last_shown, $wrong_twice_or_more_count, $last_date) {

        $this->db->where('record_id', $record_id);

        $this->db->set('history', $history);

        $this->db->set('test_history', $test_history);

        $this->db->set('rank', $rank);

        $this->db->set('last_time', $time);

        $this->db->set('last_shown', $last_shown);

        $this->db->set('wrong_twice_or_more_count', $wrong_twice_or_more_count);

        $this->db->set('total_time', 'total_time +' . (int) $time, FALSE); //false is used to tell not to escape time as well as below 1

        $this->db->set('play_count', 'play_count + 1', FALSE);

        $this->db->set('last_date', $last_date);

        $this->db->update('user_card');
    }

    /* save a card deck */

    function save_deck($deck_name, $deck, $user_id) {



        /* check the name for duplications */

        $this->db->from('card_deck');

        $this->db->where('deck_name', $deck_name);



        if ($this->db->count_all_results() > 0) {

            /* deck name allready exist */

            return false;
        } else {

            /* save the deck and return true */

            /* 1. save the deck name */

            $this->db->set('deck_name', $deck_name);

            $this->db->set('created_user_id', $user_id);

            $this->db->insert('card_deck');



            $deckId = $this->db->insert_id();



            /* 2. save each question and add entry to 'card_in_deck' table */

            foreach ($deck as $question => $anwser) {



                if (strlen($question) > 0 && strlen($anwser) > 0) {

                    $this->db->set('created_user_id', $user_id);

                    $this->db->set('question', $question);

                    $this->db->set('answer', $anwser);

                    $this->db->insert('card');



                    $cardId = $this->db->insert_id();



                    /* insert record to card in deck */

                    $this->db->set('deck_id', $deckId);

                    $this->db->set('card_id', $cardId);

                    $this->db->insert('card_in_deck');
                }
            }

            return true;
        }
    }

    /* load review mode parm */

    function load_rm_params() {

        $query = $this->db->get_where('runtime_params', array('game_mode' => 'RW'));

        return $query->result();
    }

    function load_sm_params() {

        $query = $this->db->get_where('runtime_params', array('game_mode' => 'SW'));

        return $query->result();
    }

    function load_stst_params() {

        $query = $this->db->get_where('runtime_params', array('game_mode' => 'SW'));

        return $query->result();
    }

    /*     * Load Supervied plus mode parameters Ashvin Patel 19/jun/2014* */

    function load_stst_plus_params() {
        $query = $this->db->get_where('runtime_params', array('game_mode' => 'SW'));
        return $query->result();
    }

    /*     * * */

    /* save review mode parameters */

    function save_rm_params($data) {

        foreach ($data as $key => $value) {

            $this->db->set('value', $value);

            $this->db->where('param_name', $key);

            $this->db->where('game_mode', 'RW');

            $this->db->update('runtime_params');
        }
    }

    function save_sm_params($data) {

        foreach ($data as $key => $value) {

            $this->db->set('value', $value);

            $this->db->where('param_name', $key);

            $this->db->where('game_mode', 'SW');

            $this->db->update('runtime_params');
        }
    }

    public function getCompleteCardSet($id) {

        if ($id != FALSE) {

//            $query = $this->db->get_where('news', array('id' => $id));
//
//              $sql = "SELECT *
//
//        FROM card c
//
//        INNER JOIN card_in_deck cid ON c.card_id = cid.card_id
//
//        INNER JOIN card_deck d ON d.deck_id=cid.deck_id
//
//        WHERE cid.deck_id = $id";
            $sql = "SELECT a.card_id, a.created_user_id, a.question, a.answer, a.answer_upload_file, a.created_date,
	b.deck_name, b.deck_id, c.id FROM card a
	INNER JOIN card_in_deck c ON a.card_id = c.card_id
	INNER JOIN card_deck b ON c.deck_id = b.deck_id WHERE c.deck_id = $id";


            $query = $this->db->query($sql, array($id))->result();



            return $query;

            //     return $query->row_array();
        } else {

            return FALSE;
        }
    }

    public function getCompleteGameSessions($user_id) {

        $sql = "SELECT * FROM (SELECT id,

                 no_pre_wrong,

                 'asupervised' AS type,

                 total_time,

                 decks_name,

                 game_date,

                 correct_total,

                 wrong_total,

                 card_count,

                 wrong_twice_or_more_count,

                 first_time_card_count,

                 first_time_correct_Card_cout,

                 change_minus,

                 cardCompleteCount,

                 cardCompleteCorrectCount,

                 change_plus,

                 prex,

                 prexx,

                 current_true_prex,

                 current_true_prexx,

                 TotalCardCount,

                 new_card_count,

                 new_card_correct_count,

                 correct_to_date_card_count



        FROM supervised_session

        WHERE user_id = ?

        UNION ALL

        SELECT id,

        0 AS no_pre_wrong,

        'review' AS type,

        total_time,

        decks_name,

        game_date,

        correct_total,

        wrong_total,

        card_count,

        0 AS wrong_twice_or_more_count,

        0 AS 	first_time_card_count,

        0 AS first_time_correct_Card_count,

        0 AS change_minus,

        0 AS cardCompleteCount,

        0 AS cardCompleteCorrectCount,

        0 AS change_plus,

        0 AS prex,

         0 AS prexx,

         0 AS current_true_prex,

         0 AS current_true_prexx,

         0 AS TotalCardCount,

         0 AS new_card_count,

         0 AS new_card_correct_count,

         0 AS correct_to_date_card_count

        FROM review_session

        WHERE user_id = ?) AS t order by t.game_date DESC,t.type,t.id ";



        $query = $this->db->query($sql, array($user_id, $user_id))->result();



        return $query;
    }

    public function getCardDetailsCount($user_id) {

        $sql = "SELECT COUNT(DISTINCT(t.card_id)) AS card_count FROM (

                    SELECT ssc.card_id FROM supervised_session_cards ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id WHERE ss.user_id=$user_id

                    UNION ALL

                    SELECT rsc.card_id FROM review_session_cards rsc

                    INNER JOIN review_session rs

                    ON rs.id=rsc.review_session_id WHERE rs.user_id=$user_id

                    ) AS t";



        $query = $this->db->query($sql)->result();



        return $query;
    }

    public function isAllAnswersCorrectPre($card_id, $user_id) {

        $sql = "SELECT COUNT(*) AS total_count FROM (

                    SELECT ssc.card_id FROM supervised_session_cards ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id WHERE ss.user_id=$user_id

                    AND ssc.card_id=$card_id AND ssc.ans='false'

                    UNION ALL

                    SELECT rsc.card_id FROM review_session_cards rsc

                    INNER JOIN review_session rs

                    ON rs.id=rsc.review_session_id WHERE rs.user_id=$user_id

                    AND rsc.card_id=$card_id AND rsc.ans='false'

                    ) AS t";



        $query = $this->db->query($sql)->result();



        return $query;
    }

    public function wasOnceRight($card_id, $user_id) {

        $sql = "SELECT COUNT(*) AS total_count FROM (

                    SELECT ssc.card_id FROM supervised_session_cards ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id WHERE ss.user_id=$user_id

                    AND ssc.card_id=$card_id AND ssc.ans='true'



                    ) AS t";



        $query = $this->db->query($sql)->result();



        return $query;
    }

    public function isPreviousTimeWrong($card_id, $user_id) {

        $sql = "SELECT * FROM (SELECT ssc.* FROM supervised_session_cards ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id WHERE ss.user_id=$user_id

                    AND ssc.card_id=$card_id ORDER BY ssc.id DESC) AS t

                    GROUP BY t.card_id

                    ";



        $query = $this->db->query($sql)->result();



        return $query;
    }

    public function isPreviousTwoTimeWrong($card_id, $user_id) {

        $sql = "SELECT * FROM (SELECT ssc.* FROM supervised_session_cards ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id WHERE ss.user_id=$user_id

                    AND ssc.card_id=$card_id ORDER BY ssc.id DESC) AS t

                    LIMIT 1,1";



        $query = $this->db->query($sql)->result();



        return $query;
    }

    public function getCorrectOnlyCount($user_id, $html) {



        $sql = "SELECT COUNT(*) AS total_count FROM (SELECT ssc.card_id FROM supervised_session_cards ssc

                    INNER JOIN card c

                    ON c.card_id=ssc.card_id

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id

                     WHERE ss.user_id=$user_id

                     $html

                     AND ssc.card_id NOT IN (SELECT ssc.card_id FROM supervised_session_cards 						   ssc

                    INNER JOIN supervised_session ss

                    ON ss.id=ssc.supervised_session_id

                    WHERE ans='false' AND ss.user_id=$user_id $html GROUP BY ssc.card_id) GROUP BY ssc.card_id) AS t

                   ";



        $query = $this->db->query($sql)->result();

        return $query;
    }

    public function updateCardUrlInDeck($id) {

        $this->db->trans_begin();

        try {

            $insert = array();

            $insert['answer_upload_file'] = '';

            $this->db->update('card', $insert, array('card_id' => $id));

            $this->db->trans_commit();

            return 1;
        } catch (Exception $ex) {

            print_r($ex);

            $this->db->trans_rollback();

            return 0;
        }
    }

    public function updateCardsInDeck($datas, $user_id) {


        $this->db->trans_begin();

        try {



            $insert = array();



            $items = $datas['items'];

            unset($datas['items']);

            $deck_id = $datas['deck_id'];

            unset($datas['deck_id']);

            $this->db->update('card_deck', $datas, array('deck_id' => $deck_id));

            foreach ($items AS $data) {

                $var = array();

                if (isset($data['id'])) {

                    if ($data['unchanged'] == 'changed') {

                        $id = $data['id'];

                        if ($data['action'] == 'active') {





                            $insert['question'] = $data['question'];

                            $insert['answer'] = $data['answer'];

                            if (isset($data['answer_upload_file'])) {

                                $insert['answer_upload_file'] = $data['answer_upload_file'];
                            }





                            // $wherestr = " id='$id' ";


                            $this->db->update('card', $insert, array('card_id' => $id));
                        } else if ($data['action'] == 'delete') {

                            $this->db->delete('card', array('card_id' => $id));
                        }
                    }
                } else {

                    $var = array();

                    if (isset($data['question']) && (isset($data['answer']))) {

                        $item['created_user_id'] = $user_id;

                        $item['question'] = $data['question'];

                        $item['answer'] = $data['answer'];

                        if (isset($data['answer_upload_file'])) {

                            $item['answer_upload_file'] = $data['answer_upload_file'];
                        }

                        $this->db->insert('card', $item);



                        $card_id = $this->db->insert_id();

                        $var['deck_id'] = $deck_id;

                        $var['card_id'] = $card_id;

                        $this->db->insert('card_in_deck', $var);



                        $sql = "SELECT *

                                        FROM user_card

                                        WHERE deck_id = ?

                                        GROUP BY user_id";



                        $usersCards = $this->db->query($sql, array($deck_id))->result();

                        if (count($usersCards) > 0) {

                            foreach ($usersCards AS $userCard) {

                                $current_user_id = $userCard->user_id;

                                $var1['user_id'] = $current_user_id;

                                $var1['deck_id'] = $deck_id;

                                $var1['card_id'] = $card_id;

                                $var1['history'] = '----------';

                                $var1['rank'] = 0;

                                $var1['last_time'] = 0;

                                $var1['total_time'] = 0;

                                $var1['last_shown'] = 0;

                                $var1['play_count'] = 0;





                                $this->db->insert('user_card', $var1);
                            }
                        }
                    }
                }
            }

            $this->db->trans_commit();

            return 1;
        } catch (Exception $ex) {

            print_r($ex);

            $this->db->trans_rollback();

            return 0;
        }
    }

    public function addCardsInDeck($data, $user_id) {

        $this->db->trans_begin();

        try {



            $data['created_user_id'] = $user_id;



            $var1 = array();

            if (isset($data['items'])) {

                $items = $data['items'];
            }

            unset($data['items']);

            $this->db->insert('card_deck', $data);

            $deck_id = $this->db->insert_id();



            if (isset($items)) {

                foreach ($items AS $item) {

                    $var = array();

                    if (isset($item['question']) && (isset($item['answer']))) {

                        $item['created_user_id'] = $user_id;

                        $this->db->insert('card', $item);

                        $card_id = $this->db->insert_id();

                        $var['deck_id'] = $deck_id;

                        $var['card_id'] = $card_id;





                        $this->db->insert('card_in_deck', $var);
                    }

                    //   $id = $data['id'];
                    //   $insert['question'] = $data['question'];
                    //   $insert['answer'] = $data['answer'];
                    // $wherestr = " id='$id' ";
                    // $str = $this->db->update('card', $insert, array('card_id' => $id));
                }
            }

            $this->db->trans_commit();

            return 1;
        } catch (Exception $ex) {

            print_r($ex);

            $this->db->trans_rollback();

            return 0;
        }
    }

    public function saveReportDetailsSuperficialMode($gameArray) {

        $this->db->trans_begin();

        try {



            //   $data['created_user_id'] = $user_id;

            $cards = ($gameArray['cards']);

            unset($gameArray['cards']);

            $this->db->insert('supervised_session', $gameArray);

            $session_id = $this->db->insert_id();
            /*             * **** */
            foreach ($cards AS $card) {

                unset($card['test_history']);

                $card['supervised_session_id'] = $session_id;
                $this->db->insert('supervised_session_cards', $card);
            }

            $this->db->trans_commit();

            return 1;
        } catch (Exception $ex) {

            print_r($ex);

            $this->db->trans_rollback();

            return 0;
        }
    }

    public function saveReportDetailsReviewMode($gameArray, $user_id) {

        $this->db->trans_begin();

        try {



            $gameArray['user_id'] = $user_id;

            $cards = ($gameArray['cards']);
            unset($gameArray['cards']);

            $this->db->insert('review_session', $gameArray);

            $session_id = $this->db->insert_id();

            foreach ($cards AS $card) {
                unset($card['reason']);
                unset($card['rank']);
                $card['review_session_id'] = $session_id;

                $this->db->insert('review_session_cards', $card);
            }





            $this->db->trans_commit();

            return 1;
        } catch (Exception $ex) {

            print_r($ex);

            $this->db->trans_rollback();

            return 0;
        }
    }

    /*     * Edited by ASHVIN PATEL 21/JUN/2014* */

    public function getAllCardsCount($userId) {

        /*  $sql ="SELECT COUNT(*) AS total_count FROM (SELECT c.* FROM

          card c INNER JOIN

          card_in_deck cid

          ON c.card_id=cid.card_id

          INNER JOIN card_deck cd

          ON cd.deck_id=cid.deck_id

          WHERE c.created_user_id = '".$userId."'

          GROUP BY c.card_id) AS t

          "; */
        $this->db->select('card_id');
        $this->db->from('user_card');
        $this->db->where('user_id', $userId);
        $query = $this->db->get();

        $result = $query->result();
        return count($result);
        // return $query;
    }

    public function getAllErrors($user_name) {
//********/
        $sql = "SELECT ssc.history,ssc.rank,ssc.itp,ssc.last_shown,ssc.utp,cd.deck_name,

                    ss.game_date,

                 c.question,

                 c.answer,

                    cd.deck_id



        FROM supervised_session_cards ssc

        INNER JOIN supervised_session ss

        ON ss.id=ssc.supervised_session_id

        INNER JOIN card c

        ON c.card_id=ssc.card_id

        INNER JOIN card_deck cd

        ON cd.deck_id=ssc.deck_id

        INNER JOIN users u

        ON u.id=ss.user_id

        WHERE u.email = ?

        AND ssc.ans='false'



        ORDER BY ss.game_date DESC,cd.deck_name ";



        $query = $this->db->query($sql, array($user_name))->result();



        return $query;
    }

    public function save_quick_review_log($data) {
        $data['reason'] = str_replace('&gt;&gt;', '', $data['reason']);

        $deck_name = $this->db->select("deck_name")->from("card_deck")->where("deck_id", $data['deck_id'])->get()->row_array();
        $data['deck_name'] = $deck_name['deck_name'];
        if (isset($data['reason'])) {
            $utp = $this->db->select("utp")->from("user_card")
                            ->where("user_id", $data['user_id'])
                            ->where("deck_id", $data['deck_id'])
                            ->where("card_id", $data['card_id'])
                            ->get()->row_array();
            $this->db->set('utp', 'NOW()',FALSE)
                    ->where("user_id", $data['user_id'])
                    ->where("deck_id", $data['deck_id'])
                    ->where("card_id", $data['card_id'])
                    ->update('user_card');
            //print_r($utp);
            //$data['utp'] = $utp['utp'];
            $this->db->insert('quick_review_log', $data);
            
            //print_r($this->db->last_query());
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    public function get_log_utp($data) {
        if (isset($data['reason'])) {
            $utp = $this->db->select("utp")->from("user_card")
                            ->where("user_id", $data['user_id'])
                            ->where("deck_id", $data['deck_id'])
                            ->where("card_id", $data['card_id'])
                            ->get()->row_array();
            return $utp['utp'];
        } else {
            return FALSE;
        }
    }

    public function get_quick_review_log($user_id) {
        return $this->db->where("user_id", $user_id)->order_by("id desc")->get("quick_review_log")->result_array();
    }

    public function get_users() {
        return $this->db->get("users")->result_array();
    }

    public function save_quick_review_control($logs) {

        foreach ($logs as $log) {
            $this->db->where("id", $log['user_id'])->update("users", array("review_log_status" => $log['status']));
        }
    }
    public function get_right_cards($user_id){
        return $this->db->select("REPLACE (uc.history, '-', '') as history",false)
                ->from("user_card uc")
                ->like('uc.history', 'O', 'before')
                //->join("supervised_session ss","ss.id=uc.supervised_session_id","left")
                ->where("uc.user_id",$user_id)
                ->group_by("uc.card_id")
                ->get()->num_rows();

    }

}

?>