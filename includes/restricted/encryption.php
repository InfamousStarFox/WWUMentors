<?php
//Instructions:
//Create a file named encryption.php 
//and add the two encryption functions below 
//along with a password.
//include encryption.php in any pages that utilize encryption
//
//Troubleshooting: If the encrypted string is corrupted and cannot be decrypted
//the decrypt function returns "Decryption failed." The most likely reason
//is that you did not use urlencode() prior to putting the encrypted string
//into an anchor tag. The $_GET function automatically applies urldecrypt()
//so the text must be encrypted first. 
//
//password has global scope
$myPassword = "place custom hash here";

$demoActive = false; //set this to false when using the functions in your site
if ($demoActive) {
//encrypt
   $memID = 646;
   $memIDe = encrypt($memID, $myPassword);
   
   echo "memIDe: $memIDe <br>";

//decrypt
   $memID = decrypt($memIDe, $myPassword);
   echo "memID: $memID <br>";
}

function encrypt($decrypted, $password, $salt = 'place another custom salt here') {
// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
   $key = hash('SHA256', $salt . $password, true);
// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
   srand();
   $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
   if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
      return false;
// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
   $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
// We're done!
   return $iv_base64 . $encrypted;
}

function decrypt($encrypted, $password, $salt = 'place another custom salt here') {
   $encrypted = trim($encrypted);
   //if passed through query string + signs replaced with spaces.
   $encrypted = str_replace(' ', '+', $encrypted);
// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
   $key = hash('SHA256', $salt . $password, true);
// Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
   $iv = base64_decode(substr($encrypted, 0, 22) . '==');
// Remove $iv from $encrypted.
   $encrypted = substr($encrypted, 22);
// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
   $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
// Retrieve $hash which is the last 32 characters of $decrypted.
   $hash = substr($decrypted, -32);
// Remove the last 32 characters from $decrypted.
   $decrypted = substr($decrypted, 0, -32);
// Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
   if (md5($decrypted) != $hash)
      return "Decryption failed.";
// Yay!
   return $decrypted;
}

//source: http://php.net/manual/en/book.mcrypt.php

?>