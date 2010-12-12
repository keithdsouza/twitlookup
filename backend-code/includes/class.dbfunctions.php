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

/************************

################################################################
# Database wrapper class
# --------------------------
# Functions List
# --------------------------
# DATABASE RELATED
# --------------------------
# Function Constructor()
# Function connectMeToDB()
# Function chooseDB()
# Function closeTheDB()
# Function beginTrans()
# Function commitTrans()
# Function rollbackTrans()
# --------------------------
# RECORDSET RELATED
# --------------------------
# Function selectQuery()
# Function deleteQuery()
# Function updateQuery()
# Function insertQuery()
# Function fetch_row()
# Function fetch_array()
# Function fetch_assoc()
# Function fetch_assoc()
# Function fetch_object()
# Function fetch_object()
# Function fetch_field()
# Function result()
# Function move_result()
# Function num_fields()
# Function num_rows()
# Function insert_id()
# Function affected_rows()
# Function free_result()
# Function build_record_array()
# --------------------------
# MISC FUNCTIONS
# --------------------------
# Function display_error()
# Function startTimer()
# Function stopTimer()
# Function log_message()
# --------------------------
################################################################

****************/
	
	class DBConnect{
		var $es_conn; //the connection object
		var $es_database; //the database object
		
		//the connection variables
		var $es_dbhostname;
		var $es_dbusername;
		var $es_dbpassword;
		var $es_dbname;
		
		var $es_resultset;
		var $num_rows;
		var $num_fields;
		
		var $field_names;
		
		var $error; //the error object
		var $show_error; //debugging on or off
		var $debug_mode;
		
		var $result_set_array; //the array to be stored
		var $write_log;
		var $file_logger;
		var $log_mode; //whether you want to display log messages on screen
		var $querynum;
		################################################################
		# The constructor of the class
		# @param none
		# Return Type: void
		################################################################
		
		function DBConnect(){
			$this->es_dbhostname = DATABASE_HOST_NAME;
			$this->es_dbusername = DATABASE_USER_NAME;
			$this->es_dbpassword = DATABASE_PASSWORD;
			$this->es_dbname = DATABASE_NAME;
			$this->show_error = true; //show actual error on screen/ false for dummy errors
			$this->debug_mode = false; //show debug msgs
			$this->log_mode = false; //show log msgs on screen
			$this->write_log = false;
			//$this->file_logger = "/home/proprico/public_html/logs/dbaccess.log";
			$this->file_logger = "C:\Apache2\htdocs\prop\logs\dbaccess.log";
			$this->querynum = 0;
			$this->field_names = array();
			$this->connectMeToDB();
			$this->chooseDB($this->es_dbname);
		}
		
	###################################################################################################
	#								START DATABASE RELATED FUNCTION 								  #
	###################################################################################################
	
		################################################################
		# Function used to create a connection with the database
		# returns connection object
		################################################################

		function connectMeToDB(){
			if(!$this->es_conn){
				$this->es_conn = @mysql_connect($this->es_dbhostname, $this->es_dbusername, $this->es_dbpassword);
				if(!$this->es_conn)
					$this->display_error("Could not connect to the Mysql Server wah wah");
				else
					$this->log_message("Connected to database");
			}
			else{
				//do nothing connection already open
			}
		} //end connectMeToDB
		
		################################################################
		# Function used to choose a database
		# @param none
		################################################################
		
		function chooseDB($dbname){
			$es_database=@mysql_select_db($dbname);
			if(!$es_database)
				$this->display_error('Error choosing the database');
		}
		
		################################################################
		# Function used to close the connection to the database
		################################################################
		
		function closeTheDB(){
			if($this->es_conn){
				@mysql_close($this->es_conn);
				$this->es_conn = false;
				$this->log_message("Connection to database closed");
			}
			else { //the db was already closed
				$this->display_error('Error closing the database');
				$this->log_message('Error closing the database');
			}
		} //end closeTheDB
		
		################################################################
		# Function used to begin a transaction
		################################################################
		function beginTrans(){
			@mysql_query("BEGIN");
		} //end beginTrans
		
		################################################################
		# Function used to commit a transaction
		################################################################
		function commitTrans(){
			@mysql_query("COMMIT");
		} //end commitTrans
		
		################################################################
		# Function used to rollback a transaction
		################################################################
		function rollbackTrans(){
			@mysql_query("ROLLBACK");
		} //end rollbackTrans
		
		
	###################################################################################################
	#								END DATABASE RELATED FUNCTION 								  #
	###################################################################################################
			
	###################################################################################################
	#								START RECORDSET RELATED FUNCTION 								  #
	###################################################################################################
		
		################################################################
		# Function to create a recordset from a given select query
		# @param $sqlquery
		# Return Type:  void
		################################################################

		function query($sql_statement){
			$this->querynum++;
			if($this->es_conn){
				$this->startTimer();
				
				$this->es_resultset = @mysql_query($sql_statement, $this->es_conn);
				@$this->num_rows = @mysql_num_rows($this->es_resultset);
				
				$this->stopTimer();
				
				if ( $this->debug_mode ){
					//echo $this->elapsed_time . " - <b class=\"textnor\">" . $sql_statement . "</b><br>\n";
					$this->log_message($this->elapsed_time . " - " . $sql_statement . "");
				}
				
				if(!$this->es_resultset){
					$this->display_error("Select Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
					$this->log_message("Select Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
				}
				else
					return true;
			}
		} //end Select
		
		function get_single_column($sql_statement, $returnField){
			$this->querynum++;
			if($this->es_conn){
				$this->startTimer();
				
				$this->es_resultset = @mysql_query($sql_statement, $this->es_conn);
				@$this->num_rows = @mysql_num_rows($this->es_resultset);
				
				$this->stopTimer();
				
				if ( $this->debug_mode ){
					//echo $this->elapsed_time . " - <b class=\"textnor\">" . $sql_statement . "</b><br>\n";
					$this->log_message($this->elapsed_time . " - " . $sql_statement . "");
				}
				
				if(!$this->es_resultset){
					$this->display_error("Select Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
					$this->log_message("Select Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
				}
				else {
					return @mysql_result($this->es_resultset, 0, $returnField);
				}
			}
		} //end Select

		################################################################
		# Function to update a record in a table
		# @param $sqlquery
		# Return Type:  int - updatecount
		################################################################

		function update($sql_statement){
			$this->querynum++;
			if($this->es_conn){
				$this->startTimer();
				
				$this->es_resultset = @mysql_query($sql_statement, $this->es_conn);
				//@$this->num_rows = @mysql_affected_rows($this->es_resultset);
				
				$this->stopTimer();
				
				if ( $this->debug_mode ){
					//echo $this->elapsed_time . " - <b class=\"textnor\">" . $sql_statement . "</b><br>\n";
					$this->log_message($this->elapsed_time . " - " . $sql_statement . "");
				}
				
				if(!$this->es_resultset) {
					$this->display_error("Update Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
					$this->log_message("Update Failure: mysql_query( $sql_statement ),".mysql_error($this->es_conn),$_SERVER["SCRIPT_NAME"]);
				}
				else
					//return $this->es_resultset;
					return @mysql_affected_rows();
			}
		} //end generateResultsetForUpdate

		################################################################
		# Fetch the mysql result row wise
		# @param none
		# Return Type: Recordset (single row)
		################################################################
		function fetch_row() {  
			$row=@mysql_fetch_row($this->es_resultset);  
			return $row;  
		}  
  		
		function set_field_names() {
			$this->num_fields = $this->num_fields();
			for($i = 0; $i < $this->num_fields; $i++) {
				$this->field_names[$i] = $this->field_name($i);
			}
		}
		
		function get_field_names() {
			return $this->field_names;
		}
		################################################################
		# Fetch the mysql result in the form of an array
		# @param none
		# Return Type: Array
		################################################################
		function fetch_array() {
			$this->startTimer();
			$i = 0;
			$result_set_array_main = "";
			$this->set_field_names();
			
			while($result = @mysql_fetch_array($this->es_resultset)) {
				$keys = $this->build_record_array($result);
				$result_set_array_main[$i] = $keys;
				$i++;
			}
			$this->stopTimer();
				
			if ( $this->debug_mode ){
					$this->log_message($this->elapsed_time. ' for creating the array');
			}
			//return $this->result_set_array;  
			return $result_set_array_main;
		}
		
		################################################################
		# Fetch the mysql result Associative
		# @param none
		# Return Type: Recordset (single row)
		################################################################
		function fetch_assoc() {  
			$row=@mysql_fetch_assoc($this->es_resultset);  
			return $row;  
		}
		
		################################################################
		# Fetch the mysql result as an object
		# @param none
		# Return Type: Recordset (object)
		################################################################
		function fetch_object() {  
			$row=@mysql_fetch_object($this->es_resultset);  
			return $row;  
		}
		
		################################################################
		# Fetch the mysql result fields in a particular row
		# @param none
		# Return Type: Recordset (single row)
		################################################################
		function fetch_field() {  
			$row=@mysql_fetch_field($this->es_resultset);  
			return $row;
		}
		
		################################################################
		# Fetch the mysql result fields in a particular row
		# @param none
		# Return Type: Recordset (single row)
		################################################################
		function field_name($index) {  
			$name=@mysql_field_name($this->es_resultset, $index);
			return $name;
		}
		
		################################################################
		# Fetch the mysql result row wise
		# @param $recordnumber
		# @param $fieldname
		# Return Type: Recordset result
		################################################################
		function result($recno,$field) {  
			return @mysql_result($this->es_resultset,$recno,$field);  
		}
		
		################################################################
		# Move the sql result set to the next row
		# @param $rownumber
		# Return Type: void
		################################################################
		function move_result($counter) {  
			@mysql_data_seek($counter, $this->es_resultset);  
		}
		
		################################################################
		# Count the number of columns in the recordset
		# @param none
		# Return Type: int - fieldcount
		################################################################
		function num_fields(){  
			return @mysql_num_fields($this->es_resultset);  
		}
		
		################################################################
		# Count the number of rows in the recordset
		# @param none
		# Return Type: int - rowcount
		################################################################
		function num_rows(){  
			return @mysql_num_rows($this->es_resultset);  
		}
		
		################################################################
		# Fetch the last insert id for a particular insert
		# @param none
		# Return Type: int - insertid
		################################################################
		function insert_id() {  
			return @mysql_insert_id();  
		}
		
		################################################################
		# Fetch the number of rows affected by a insert/update/delete
		# @param none
		# Return Type: int - rowsaffected
		################################################################
		function affected_rows(){
        	return @mysql_affected_rows();
		}
		
		################################################################
		# Free the mysql result set
		# @param none
		# Return Type: void
		################################################################
		function free_result() {
			@mysql_free_result($this->es_resultset);
			$this->num_rows = 0;
		}
		
		################################################################
		# Build an array from a recordset
		# @param $resultset
		# Return Type: Array
		################################################################
		function build_record_array($result) {
			$i = 0;		
			$records = array();
			
			while(list($key,$val) = each($result)) {
				$records[$key] = $val;
			}
			return $records;
		}
		
		function escape_string_for_mysql($text){
			return mysql_real_escape_string($text, $this->es_conn);
		}
	###################################################################################################
	#								END RECORDSET RELATED FUNCTION 								  	  #
	###################################################################################################
	
		################################################################
		# Function to debug and show a error to the user
		# @param $description
		# @param $errorfilename - defaults ''
		# @param $linenumber - defaults ''
		# Return Type: void
		################################################################
		
		function display_error($description, $file='', $line='') {
			if($this->show_error)
				die("An error ocurred. These are the details:<br />File: <strong>{$file}</strong><br />Line: <strong>{$line}</strong><br />Description: <strong>{$description}</strong>");
			else
				die("An error ocurred during a database action, please try again later");
    	}
		
		###################################################################
		# Start timer function for benchmarking
		# @param name
		# Return Type: void
		###################################################################
		function startTimer(){
			list($foo,$bar)=explode(' ',microtime());
			$this->timer_start=$foo+$bar;
			unset($foo);
			unset($bar);
		}
		
		###################################################################
		# Stop timer function for benchmarking
		# @param name
		# Return Type: void
		###################################################################
		function stopTimer(){
			list($foo,$bar)=explode(' ',microtime());
			$this->timer_end=$foo+$bar;
			unset($foo);
			unset($bar);
				$this->elapsed_time = round($this->timer_end-$this->timer_start,5);
		}
		
		###################################################################
		# Log file write
		# @param $message
		# Return Type: boolean
		###################################################################
		function log_message($message) {
			/*if((!$message || !$this->file_logger) && !$this->write_log)
				return false;
			$fp = fopen($this->file_logger,"a");
			$write = fputs($fp,date('Y-m-d H:i:s')." File : ".$_SERVER["SCRIPT_NAME"]." Message: $message".chr(13).chr(10));
			fclose($fp);*/
			if($this->log_mode)
				echo "<span class=\"textnor\">".$message."</span><br>";
			return true;
		}
	} //end class definition
?>