<?php

// database functions ************************************************

function fCleanString($link, $UserInput, $MaxLen) {
   //remove html tags
   $UserInput = strip_tags($UserInput);

   //Escape special characters - very important.
   $UserInput = htmlspecialchars($UserInput);
   
   //mysqli_real_escape_string requires database connection
   $UserInput = mysqli_real_escape_string($link, $UserInput);
   
   //remove everything that is not an ascii character between 32 and 126
   $UserInput = preg_replace('/[^\x20-\x7E]/', '', $UserInput);

   //truncate to max length of database field
   return substr($UserInput, 0, $MaxLen);
}

function fCleanTextArea($link, $UserInput, $MaxLen) {
   //remove html tags
   $UserInput = strip_tags($UserInput);
   

   //remove everything that is not an ascii character between 32 and 126, or a line break
   $UserInput = preg_replace('/[^\
   \x20-\x7E]/', '', $UserInput);
   
   $UserInput = nl2br($UserInput);

   //Escape special characters - very important.
   //mysqli_real_escape_string requires database connection
   $UserInput = mysqli_real_escape_string($link, $UserInput);
   
   $UserInput = str_ireplace(array("\r","\n",'\r','\n'),'', $UserInput);

   //truncate to max length of database field
   return substr($UserInput, 0, $MaxLen);
}

function fCleanNumber($UserInput) {
   $pattern = "/[^0-9\.]/"; //replace everything except 0-9 and period
   $UserInput = preg_replace($pattern, "", $UserInput);
   return substr($UserInput, 0, 6);
}

function fConnectToDatabase() {
   //For code reusability this function is often located in its own file.
   //Pages that require database assess include it with include('connection.php');
   //where 'connection.php' is the name of your connect file.
   //Create a connection object
   //@ suppresses errors.  
   //parameters: mysqli_connect('my_server', 'my_user', 'my_password', 'my_db');  
   $link = @mysqli_connect('my_server', 'my_user', 'my_password', 'my_db');

   //handle connection errors
   if (!$link) {
      die('Connection Error: ' . mysqli_connect_error());
   }
   return $link;
}
?>