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

$myFile = "D:\\service\wiki.xml";
$fp = @fopen("D:\\service\wiki.xml", "r");
$theData = fread($fp, filesize($myFile));
fclose($fp);

preg_match ("/(.*?)<p class=\"blueBackHeading\">Indian Premier League<\/p>(.*?)<p class=\"blueBackHeading\">/is", $theData, $matcher);
if($matcher) {
  //echo "Language ".$matcher[2];
}

preg_match_all("/<p class=\"live-scores\">(.*?)<\/p>/is", $matcher[2], $matcher);
if($matcher) {
  //print_r($matcher);
  if($matcher[1]) {
    $matches = $matcher[1];
    echo "Total matches ".count($matches);
    $lastmatch = $matches[count($matches) - 1];
    echo $lastmatch;
    preg_match_all("/(.*?)<\/b><br>(.*?)/isU", $lastmatch, $matcher);
    if($matcher) {
      print_r($matcher[2][0]);
    }
    
  }
}


die();

preg_match ("/<td align=\"left\" width=\"25%\" nowrap>(.*?)<\/td>/is", $theData, $matcher);
if($matcher) {
  echo "Language ".strip_tags($matcher[1]);
}
$content = $matcher[1];
preg_match ("/<td align=\"center\" width=\"50%\" rowspan=2>(.*?)<br>/is", $theData, $matcher);
if($matcher) {
  echo "Language ".strip_tags($matcher[1]);
}

//echo $content;
preg_match("/<div style=\"(.*?);\" id=\"rhbox_fixtures\">(.*?)Complete list of results(.*?)<\/div>/is", $theData, $matcher);
if($matcher) {
  
  echo "<pre>";
  print_r($matcher[2]);
  echo "</pre>";
  $content = $matcher[2];
}
preg_match("/<p class=\"live-scores-links\">(.*?)<\/p>/is", $content, $matcher);
if($matcher) {
  echo "Found Match";
  //print_r($matcher);
  echo "<pre>";
  print_r($matcher[1]);
  echo "</pre>";
  //$match = strip_tags($matcher[1][0]);
  //echo $match;
}

preg_match("/<a href=\"\/ipl2009\/engine\/match\/(.*?).html\" class=\"live-scores-links\">Scorecard<\/a>/is", $matcher[1], $matcher);
if($matcher) {
    echo "<pre>";
  print_r($matcher[1]);
  echo "</pre>";

}

preg_match("/(.*?) - (.*?) \((.*?), (.*?)\)/is", $match, $matcher);
if($matcher) {
  print_r($matcher);
}

date_default_timezone_set('Asia/Kolkata');
echo "<br>TIME: ".date('jS F \\a\\t g A', strtotime("Apr 23, 2009, 10:30 GMT"));

?>