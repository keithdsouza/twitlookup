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

  require_once('twitlookup.class.php');
  
  $username = "twitlookup";
  $password = "thepassword";
  
  $debug = false;
  $send = false;
  $onlydm = false;
  
  $testaccount = false;
  
  if(isset($_REQUEST['debug'])) {
    $debug = true;
  }
  if(isset($_REQUEST['send'])) {
    $send = true;
  }
  
  if(isset($_REQUEST['dm'])) {
    $onlydm = true;
  }
  
  if(isset($_REQUEST['test'])) {
    $username = "keithtest";
    $password = "testpassword";
  }
  
  $twitlook = new TwitLookup($username, $password, $debug, $send, $onlydm);
  
  define("DEFAULT_POLLING_INTERVAL", 60);
  define("DEFAULT_POLLING_INTERVAL", 540);
  $start_time = time();
  $verystarttime = $start_time;
  $twitlook->process();
?>