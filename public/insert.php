<?php
  function OpenCon()
   {
   $dbhost = "localhost";
   $dbuser = "root";
   $dbpass = "qwer4UA";
   $db = "db_weather";
   $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

   return $conn;
   }

  function CloseCon($conn)
   {
   $conn -> close();
   }
?>