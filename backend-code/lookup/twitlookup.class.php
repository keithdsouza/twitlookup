<?php
/*****
* 
* Copyright 2010 Keith Dsouza (dsouza.keith@gmail.com / http://keithdsouza.com)
* 
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
* 
*****/

  require_once("class.ActionFactory.php");
  require_once('../service/twitter.php');
  require_once("settings.php");
  require_once("../includes/class.dbfunctions.php");
  define("ME", "twitlookup");
  class TwitLookup {
    var $twitter;
    var $reply_error;
    var $limit_reached_error;
    var $db;
    var $sendm = false;
    var $glob_message;
    var $sendmessage = false;
    var $messagestatus = -1;
    var $debug;
    var $take_a_break = false;
    var $send = false;
    var $onlydm = false;
    function __construct($username, $password, $debug = false, $send = false, $onlydm = false) {
      $this->twitter = new Arc90_Service_Twitter($username, $password);
      $this->debug = $debug;
      $this->send = $send;
      $this->onlydm = $onlydm;
    }

    function process() {
      if($this->debug) {
        echo "Debugging but are we sending messages ? = ".$this->send."<br>";
      }
      $this->db = new DBConnect();
      //$response = $this->twitter->getReplies('xml');
      if(! $this->onlydm) {
        $this->take_a_break = false;
        $this->process_replies();
      }
      //$response = $this->twitter->getMessages('xml');
      $this->take_a_break = false;
      $this->process_dms();
      unset($this->twitter);
      $this->db->closeTheDB();
      unset($this->db);
    }

    function process_query($status_id, $twitter_query, $twitter_user_id, $twitter_screen_name, $direct_message = false) {
      //moron caller you should have taken a break, go fuck yourself now
      if($this->take_a_break && !$this->debug) {
        return;
      }

      if($twitter_screen_name == ME) {
        return;
      }

      //have we replied to this message already, if so flag to terminate request
      if(!$this->debug || $this->send) {
        if($this->is_replied($status_id)) {
          $this->take_a_break = true;
          return;
        }
      }

      if($this->debug) {
        echo "Processing $status_id from $twitter_user_id for quert $twitter_query<br>";
      }

      $internal_user_id = $this->twitter_user_info($twitter_user_id, $twitter_screen_name);
      if(!$internal_user_id) {
        if($this->debug) {
          echo "user is blocked or something wrong with registration code<br>";
        }
        //TODO log this request
        return;
      }
      
      $twitter_query = trim(str_replace("@twitlookup", "", $twitter_query));
      $lookup_object = ActionFactory::get_lookup_object($twitter_query, "twitter");
      $type = $lookup_object->get_type();
      if("default" == $type) {
        return;
      }
      if($this->debug) {
        echo "Type: $type<br>";
      }
      $actual_search_term = $lookup_object->get_actual_search_term();
      if($this->debug) {
        echo "Getting message<br>";
      }
      $message = $this->get_message($lookup_object);
      if($this->debug) {
        echo "<b>Message is</b> $message<br>";
      }
      if($message) {
        if(!$this->debug || $this->send) {
          $this->add_stats($internal_user_id, $status_id, $twitter_query, $this->messagestatus, $direct_message);
        }
        if($this->debug) {
          echo "added status<br>";
        }
        if(is_array($message)) {
          foreach($message as $mess) {
            if(!$this->debug || $this->send) {
              if($this->debug) {
                echo "SENDING Message NOw<br>";
              }
              $mess = trim($lookup_object->get_hash_tags." ".$mess);
              if($direct_message) {
                $response = $this->send_message($this->prepare_message($mess, $twitter_screen_name, true), $twitter_user_id);
              }
              else {
                $response = $this->update_status($this->prepare_message($mess, $twitter_screen_name));
              }
              if($this->debug) {
                echo $response->getData();
              }
            }
            if($this->debug) {
              echo "DM: $direct_message sending message $mess<br><br>$response";
            }
          }
        }
        else {
          if(!$this->debug || $this->send) {
            if($this->debug) {
              echo "SENDING Message NOw<br>";
            }
            $message = trim($lookup_object->get_hash_tags." ".$message);
            if($direct_message) {
              $response = $this->send_message($this->prepare_message($message, $twitter_screen_name, true), $twitter_user_id);
            }
            else {
              $response = $this->update_status($this->prepare_message($message, $twitter_screen_name));
            }
            if($this->debug) {
              echo $response->getData();
            }
          }
          if($this->debug) {
            echo "DM: $direct_message sending message $message<br><br>$response";
          }
        }
      }
      $message = "";
      unset($lookup_object);
    }

    function process_dms() {
      try {
        //DEBUG CODE
        //$response = Util::read_file('D:\\service\\wiki.xml');
        //$xml = simplexml_load_string($response);
        //echo $response;
        echo "Processing DMs Now<br >";
        $response = $this->twitter->getMessages('xml');
        if($this->debug) {
          //echo $response->getData();
        }
        if($response->isError()) {
            //TODO need to handle the error here
          return;
        }
        $xml = simplexml_load_string($response->getData());

        foreach($xml->direct_message as $replies) {
          $this->sendmessage = true;
          $systemmessage = false;
          $this->messagestatus = -1;
          $status_id = $replies->id;
          $twitter_query = $replies->text;
          $twitter_user_id = $replies->sender->id;
          $twitter_screen_name = $replies->sender->screen_name;
          if($this->debug) {
            echo "<b>Processing DM</b> for $twitter_screen_name: $twitter_query<br>";
          }
          $this->process_query($status_id, $twitter_query, $twitter_user_id, $twitter_screen_name, true);
        }
      }
      catch(Arc90_Service_Twitter_Exception $e)
      {
          print $e->getMessage();
      }
    }

    function process_replies() {
      try {

        //DEBUG CODE
        //$response = Util::read_file('D:\\service\\wiki.xml');
        //$xml = simplexml_load_string($response);
        //echo $response;
        echo "Processing Replies Now<br >";
        $response = $this->twitter->getReplies('xml');
        if($this->debug) {
          //echo $response->getData();
        }
        if($response->isError()) {
            //TODO need to handle the error here
          return;
        }
        $xml = simplexml_load_string($response->getData());

        foreach($xml->status as $replies) {
          $this->sendmessage = true;
          $systemmessage = false;
          $this->messagestatus = -1;
          $status_id = $replies->id;
          $twitter_query = $replies->text;
          $twitter_user_id = $replies->user->id;
          $twitter_screen_name = $replies->user->screen_name;
          if($this->debug) {
            echo "<b>Processing Reply:</b> for $twitter_screen_name: $twitter_query<br>";
          }
          $this->process_query($status_id, $twitter_query, $twitter_user_id, $twitter_screen_name, false);

        }
      }
      catch(Arc90_Service_Twitter_Exception $e)
      {
          print $e->getMessage();
      }
    }

    function custom_defi($search_term) {
      if($search_term == "yaymen" || $search_term == "yaymen") {
        $this->glob_message = "A Twitter competition to find the best man amongst the ladies, for more info see http://tinyurl.com/camxk3";
        return true;
      }
      else if($search_term == "yaywomen" || $search_term == "yaywomen") {
        $this->glob_message = "A Twitter competition to find the best lady amongst the women, for more info see http://tinyurl.com/camxk3";
        return true;
      }
      return false;
    }

    function get_message($lookup_object) {
      $got_errors = false;
      $message = "";
      if($lookup_object->is_ready() && $lookup_object->is_search()) {
        $got_results = $lookup_object->_perform();
        if($this->debug) {
          echo "Get Results is $got_results<br>";  
        }
        if($got_results) {
          $defintions = $lookup_object->the_output();
          if(is_array($defintions)) {
            if($defintions[$lookup_object->get_type()]) {
              foreach($defintions[$lookup_object->get_type()] as $defi) {
                $message[] = $defi;
              }
            }
            else {
              $got_errors = true;
            }
          }
          else {
            $message[] = $defintions;
          }
          $this->messagestatus = REPLIED;
        }
        else {
          $got_errors = true;
          $this->messagestatus = INTERNAL_ERROR;
        }
      }
      else {
        $got_errors = true;
        $this->messagestatus = INTERNAL_ERROR;
      }

      if($got_errors) {
        if($lookup_object->is_empty_search()) {
          $message = "empty search used, ".$lookup_object->get_help();
          $this->messagestatus = EMPTY_SEARCH;
        }
        else if($lookup_object->is_no_results()) {
          $message = "nothing found, ".$lookup_object->get_help();
          $this->messagestatus = NO_RESULTS;
        }
        else {
          $message = $lookup_object->get_help();;
          $this->messagestatus = INTERNAL_ERROR;
        }
      }

      return $message;
    }

    function peform_weather_lookup($lookup_object) {
      $got_errors = false;
      $message = "";
      if($lookup_object->is_ready() && $lookup_object->is_search()) {
        $got_results = $lookup_object->_perform();
        if($got_results) {
          $defintions = $lookup_object->the_output();
          foreach($defintions['weather'] as $match) {
            $message[] = $match;
          }
          $this->messagestatus = REPLIED;
        }
        else {
          $got_errors = true;
          $this->messagestatus = INTERNAL_ERROR;
        }
      }
      else {
        $got_errors = true;
        $this->messagestatus = INTERNAL_ERROR;
      }

      if($got_errors) {
        if($lookup_object->is_empty_search()) {
          $message = "empty weather lookup, try @twitlookup weather city/zipcode/postalcode to get weather";
          $this->messagestatus = EMPTY_SEARCH;
        }
        else if($lookup_object->is_no_results()) {
          $message = "could not find weather lookup, try @twitlookup weather city/zipcode/postalcode to get weather";
          $this->messagestatus = NO_RESULTS;
        }
        else {
          $message = "try @twitlookup weather city/zipcode/postalcode to get weather";
          $this->messagestatus = INTERNAL_ERROR;
        }
      }

      return $message;
    }

    function perform_dict_search($lookup_object) {
      $got_errors = false;
      $message = "";
      if($lookup_object->is_ready() && $lookup_object->is_search()) {
        $got_results = $lookup_object->_perform();
        if($got_results) {
          $defintions = $lookup_object->the_output();
          if($defintions['type']) {
            if($defintions['type']['Noun']) {
              $message = $lookup_object->actual_search_term.": Noun - ".$defintions['type']['Noun']['def'][0];
            }
            else if($defintions['type']['Verb']) {
              $message = $lookup_object->actual_search_term.": Verb - ".$defintions['type']['Verb']['def'][0];
            }
            else {
              foreach($defintions['type'] as $key => $value) {
                $message = $lookup_object->actual_search_term.": $key - ".$defintions['type'][$key]['def'][0];
                break;
              }
            }
            $this->messagestatus = REPLIED;
          }
          else {
            $got_errors = true;
            $this->messagestatus = INTERNAL_ERROR;
          }
        }
        else {
          $got_errors = true;
          $this->messagestatus = INTERNAL_ERROR;
        }
      }
      else {
        $got_errors = true;
        $this->messagestatus = INTERNAL_ERROR;
      }

      if($got_errors) {
        if($lookup_object->is_empty_search()) {
          $message = "Empty string given for search, please use \"dict word\" for searching the dictionary";
          $this->messagestatus = EMPTY_SEARCH;
        }
        else if($lookup_object->is_no_results()) {
          $message = "$actual_search_term: no results found for this word";
          $this->messagestatus = NO_RESULTS;
        }
        else {
          $message = "$actual_search_term: got a general error while searching the dictionary";
          $this->messagestatus = INTERNAL_ERROR;
        }
      }

      return $message;
    }

    function twitter_user_info($user_id, $screen_name) {
      if(!$this->db) {
        return false;
      }
      $this->db->query("select id, blocked from twitter_users where user_id = " . $user_id);
      $results = $this->db->fetch_array();
      $user_count = $this->db->num_rows;
    	$this->db->free_result();
      if($user_count > 0) {
        $user_id = $results[0]['id'];
        $blocked = $results[0]['blocked'];
        if($blocked) {
          return false;
        }
        else {
          return $user_id;
        }
      }
      else {
        $this->db->query("insert into twitter_users (user_id, screen_name, create_date) values ($user_id, '$screen_name', NOW())");
        $user_id = $this->db->insert_id();
        return $user_id;
      }
    }

    function update_status($message) {
      return $this->twitter->updateStatus($message, "xml");
    }

    function send_message($message, $twitter_user_id) {
      return $this->twitter->sendMessage($twitter_user_id, $message, "xml");
    }

    function prepare_message($message, $screen_name, $dm = false) {
      //TODO add URL etc
      if(!$dm) {
        $message = "@$screen_name $message";
      }
      if(strlen($message) > 140) {
        $message = substr($message, 0, 138);
      }
      return $message;
    }

    function add_stats($internal_user_id, $status_id, $twitter_query, $messagestatus, $direct_message = false) {
      if(!$this->db) {
        return false;
      }
      if(!$direct_message) {
        $direct_message = 0;
      }
      else {
        $direct_message = 1;
      }
      $this->db->query("insert into twitter_queries (int_user_id, status_id, message, status, direct_message, replied_date) values ($internal_user_id, $status_id, '$twitter_query', $messagestatus, $direct_message, NOW())");
    }

    function is_replied($status_id) {
      if(!$this->db) {
        return false;
      }

      $status_count = $this->db->get_single_column("select count(*) as status_count from twitter_queries where status_id = $status_id", "status_count");
      if($status_count > 0) {
        return true;
      }
      return false;
    }
  }
?>
