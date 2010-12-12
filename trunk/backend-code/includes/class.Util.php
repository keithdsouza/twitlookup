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

    class Util {
      function starts_with($string, $dictstring){
       return strpos($string, $dictstring) === 0;
      }
      
      function load_page($URL, $ispost = false, $postparams = "") {
        //echo "LOAAAAAAAAAAAAADING $URL<br /><br />";
        $return = false;
        $snoopy = new HTTPClient();
        if($snoopy->loadPage($URL)) {
          $return = $snoopy->getData();
        }
        unset($snoopy);
        return $return;
      }
      
      function print_array($array) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
      }
      
      function read_file($filename) {
        $fp = @fopen($filename, "r");
        $data = fread($fp, filesize($filename));
        fclose($fp);
        return $data;
      }
      
      function get_first_word($sentence) {
        $data = explode(" ", $sentence); 
        return $data[0];
      }
      
      function get_word($sentence, $num) {
        $data = explode(" ", $sentence); 
        if($num > count($data) && $num >= 1) {
          return $data[count($data)];
        }
        else if($num <= 0) {
          return false;
        }
        return $data[$num];
      }
      
      function get_word_after($sentence, $num) {
        $data = explode(" ", $sentence); 
        if($num > count($data) && $num >= 1) {
          return $num = $num - 1;
        }
        else if($num <= 0) {
          return false;
        }
        $return = "";
        for($i = $num; $i < count($data); $i++) {
          $return .= $data[$i]." ";
        }
        return trim($return);
      }
      
      function get_last_word($sentence) {
        $data = explode(" ", $sentence); 
        return $data[count($data)];
      }
      
      //TODO make sure to loop till we get 140 char messages
      function format_message_for_twitter($message) {
        $formatted_message = array();
        if(strlen($message) > 140) {
          $formatted_message[] = substr($message, 0, 120)."..";
          $formatted_message[] = "..".substr($message, 120);
        }
        else {
          $formatted_message[] = $message;
        }
        return $formatted_message;
        
      }
      
      function get_tiny_url($url) {
      }
    }
    
?>
