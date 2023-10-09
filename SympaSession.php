<?php
/**********************************************************************
SympaSession.php

A rough and ready php class to allow a php application to authenticate
a user for wwsympa

Synopsis:

  include ('SympaSession.php');
  $ss = new SympaSession($email);
  $sympa_session_id = $ss->getid();
  setcookie("sympa_session", $sympa_session_id, 0, '/', $sympa_session_domain, FALSE);
  
*** DON'T FORGET TO CONFIGURE THE vars AT THE BEGINNING OF THE CLASS **
  
(c) Chris Hastie 2008
This file is free software; you can redistribute
it and/or modify it under the terms of the GNU
General Public License as published by the Free
Software Foundation; either version 2 of the
License, or (at your option) any later version.

GPL: http://www.gnu.org/licenses/gpl.txt

************************* W A R N I N G *****************************
*                                                                   *
*  This file contains database passwords in clear text. Make sure   *
*  you set the permission accordingly. On Unix like systems         *
*  something like:                                                  *
*                                                                   *
*  chown www SympaSession.php                                       *
*  chmod 600 SympaSession.php                                       *
*                                                                   *
*  The user which you connect to the database as should have the    *
*  minimum permission needed for things to work, ie SELECT,         *
*  UPDATE and INSERT privilages on sympa.session_table only         *
*                                                                   *
*********************************************************************

Requires PHP 5. Expects to be run in a context where $_COOKIE and 
$_SERVER are available

If you need PHP 4 support look to the way $session->data is stored. 
This is an object, and _obj2string iterates it. Iteration of objects
is not supported in PHP 4 - try an associative array instead.

This file comes with no warranty and is provided as a basis for your
own experiments!

version 0.1
**********************************************************************/

// if (!defined('EXPONENT')) exit('');

class SympaSession {
   
   /**************************
   *     Configuration       *
   **************************/
   
  var  $ss_host = 'localhost';            // MySQL host where sympa db is
  var  $ss_db = 'sympa';                  // Database used by sympa
  var  $ss_user = 'sympa_session';        // MySQL user to login as. Requires SELECT, UPDATE and INSERT
                                          // priviliges on table session_table
  var  $ss_passwd = 'thats_my_secreet';   // MySQL user password
  
  var  $robot = 'lists.example.com';      // Sympa robot we're handling login for
  
   /**************************
   *     End Configuration   *
   **************************/
  
  
  var  $ss_dbh = null;
  var  $id = '';
  var  $session = null;
  var  $email = '';
  var  $debug = 1;
  
  /*
  * Initializes the class
  *
  * @param string $email  The email address of the authenticated user to start a session for
  *
  * @access public
  * @return void
  */  
  
  function SympaSession($email) {
    $this->email = strtolower($email);
    $this->ss_dbh = mysql_connect($this->ss_host, $this->ss_user, $this->ss_passwd);
    if (empty($this->ss_dbh)) {
      trigger_error( "[phpSympaSession] Error connecting to MySQL server: " . mysql_error(), E_USER_WARNING);
      return null;
    }
        
    if (!mysql_select_db($this->ss_db, $this->ss_dbh)) {
      trigger_error( "[phpSympaSession] Error selecting database: " . mysql_error(), E_USER_WARNING);
      return null;
    }
  }
  
  /*
  * Get the session ID
  *  
  * Returns a session ID of a valid sympa session for this user. Creates a session if needed,
  * writing to the session_table, or updates an existing one to authenticated, 
  * or just returns the ID of an existing authenticated session.
  *
  * @access public
  * @return string  The session ID
  */  
  
  function getid() {
  
    if (isset($_COOKIE['sympa_session'])) {
      $this->id = $_COOKIE['sympa_session'];
    }
    if ($this->debug) trigger_error( "[phpSympaSession] Session ID from cookie: " . $this->id, E_USER_NOTICE);
    
    // no existing session cookie - create a new session
    if (empty($this->id)) {
      return ($this->_newsession());
    }
    
    $this->session = $this->_loadsession();
    
    // something went wrong loading the existing session - create a new one
    if (empty($this->session)) {
      trigger_error( "[phpSympaSession] Couldn't load session " . $this->id . ". Creating new session", E_USER_WARNING);
      return $this->_newsession();      
    }
    
    // existing session is not for this client IP - create a new one
    if ($this->session->remote_addr_session != $_SERVER['REMOTE_ADDR']) {
      trigger_error( "[phpSympaSession] Session IP " . $this->session->remote_addr_session . " doesn't match client IP " .  $_SERVER['REMOTE_ADDR'] . ". Creating new session", E_USER_WARNING);
      return $this->_newsession();
    }
    
    // existing session is not for this user - create a new one
    if ($this->session->email_session != $this->email) {
      trigger_error( "[phpSympaSession] Session email " . $this->session->email_session . " doesn't match user email " .  $this->email . ". Creating new session", E_USER_WARNING);
      return $this->_newsession();
    }
    
    // existing session looks good and is already logged in to Sympa - just return the ID
    if ($this->session->data->auth == 'classic') {
      if ($this->debug) trigger_error( "[phpSympaSession] User already authenticated to Sympa", E_USER_NOTICE);
      return $this->id;
    }    
    
    // existing session needs updating to set it as authenticated
    if ($this->debug) trigger_error( "[phpSympaSession] Setting auth to 'classic' on existing session " . $this->id, E_USER_NOTICE);
    $this->session->data->auth = 'classic';
    
    return $this->_updatesession();
            
  }
  
  
  /*
  * Update session_table
  *  
  * Updates the data_session field of an existing session in session_table
  *
  * @access private
  * @return string  The session ID
  */    
  function _updatesession() {
    //return ('testupdate');
    $ss_data = $this->_obj2string($this->session->data);
    $sql = sprintf("UPDATE session_table SET `data_session` = '%s' WHERE `id_session` = '%s';", mysql_escape_string($ss_data) , mysql_escape_string($this->id));
    if ($this->debug) trigger_error("[phpSympaSession] Doing SQL: " . $sql, E_USER_NOTICE);
    if (!$result = mysql_query($sql, $this->ss_dbh)) {      
        trigger_error( "[phpSympaSession] Error executing SQL: " . mysql_error(), E_USER_WARNING);
        return 0;
    }
    return $this->id;    
  }
  
  /*
  * Create new session
  *  
  * Inserts a new session into session_table
  *
  * @access private
  * @return string  The session ID
  */      
  function _newsession() {
    //return ('testnew');
    $ss_data = ';auth="classic";data=""';
    $ss_date = time();
    $ss_hits = 1;
    $ss_id = rand(1000000, 9999999) . rand(1000000, 9999999);
    $ss_start_date = time();
    
    $sql = sprintf("INSERT INTO session_table (
      `data_session`, 
      `date_session` ,
      `email_session` ,
      `hit_session` ,
      `id_session` ,
      `remote_addr_session` ,
      `robot_session` ,
      `start_date_session`)
        VALUES ( '%s', %u, '%s', %u, '%s', '%s', '%s', %u);", 
        mysql_escape_string($ss_data),
        $ss_date,
        mysql_escape_string($this->email),
        $ss_hits,
        mysql_escape_string($ss_id),
        mysql_escape_string($_SERVER['REMOTE_ADDR']),
        mysql_escape_string($this->robot),
        $ss_start_date
    );
    
    if ($this->debug) trigger_error("[phpSympaSession] Doing SQL: " . $sql, E_USER_NOTICE);
    if (!$result = mysql_query($sql, $this->ss_dbh)) {      
        trigger_error( "[phpSympaSession] Error executing SQL: " . mysql_error(), E_USER_WARNING);
        return 0;
    }
    if ($this->debug) trigger_error("[phpSympaSession] Created new session " . $ss_id, E_USER_NOTICE);
    return $ss_id;    
  }  
  
  /*
  * Load a session
  *  
  * Loads an existing session from the session_table
  *
  * @access private
  * @return object  The session. Properties as field names. The data in
  *                 data_session are also presented as an object in session->data
  */      
  function _loadsession() {
    $session = null;
    $sql = sprintf("SELECT * FROM `session_table` WHERE `id_session` = '%s' ", $this->id);
    if (!$result = mysql_query($sql, $this->ss_dbh)) {      
        trigger_error( "[phpSympaSession] Error retrieving sympa session data: " . mysql_error(), E_USER_WARNING);
        return 0;
    }    
    
   $session = @mysql_fetch_object($result);
   if (empty($session)) return 0;
   
   $session->data = $this->_string2obj($session->data_session);
      
   return $session;
       
  }
  
  /*
  * Convert a string to an object
  *  
  * Converts the string stored in the data_session field to an object
  *
  * @param string $data  The string to convert
  * @access private
  * @return object  The data represented as a string    
  */      
  function _string2obj($data) {
    $out = new stdClass();
    //$out = array();
    
    while ( preg_match("/^(\;?(\w+)\=\"([^\"]*)\")/", $data, $matches)) {
	  $out->$matches[2] = $matches[3]; 	  
	  $data = preg_replace("/$matches[1]/", '', $data) ;
    }
    
    return $out;
  }
  
  /*
  * Convert an object to a string for storage in the data_session field
  *
  * @param object  The object to conver
  * @access private
  * @return string  Formatted string for storage in data_session field
  */      
  function _obj2string($obj) {
    $string = '';
    foreach($obj as $key => $val) {
      $string .= ';' . $key . '="' . $val . '"';
    }
    return $string;
  }
}