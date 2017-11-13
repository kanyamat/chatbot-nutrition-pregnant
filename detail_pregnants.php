<?php
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user = $_GET["data"];
$user_id = pg_escape_string($user);
 // echo $user_id;
         $des_preg = pg_query($dbconn,"SELECT  descript,img FROM pregnants ");
              while ($row = pg_fetch_row($des_preg)) {
                  echo $des = $row[0]; 
                  echo $img = $row[1]; 
 
                } 
?>