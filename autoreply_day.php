<?php
//database
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);
//
$access_token = 'eBp/ZVsDsV2fMTqSBYGq4pgOvc+sgaPxxJFeT/rvpT/WTLiyw44BA2co2RBVROiLPVr8EEMrdiJ2I5cKWBe+j+GhNrHu6FUEHyol1dGf8DM/ZykdR84RgfTU2p+3U9NnhjqhWkDrN0tQT56rf23TxQdB04t89/1O/w1cDnyilFU=';
$seqcode =[];
$s =[];
$check_q = pg_query($dbconn,"SELECT DISTINCT user_id FROM auto_reply WHERE auto_day = 1  ");
            while ($row = pg_fetch_assoc($check_q)) {
                  $seqcode[] =  $row['user_id'];
                } 
                
array_push( $s,$seqcode);
print_r($s);
$arrlength = count($s);
for($x = 0; $x <= $arrlength ; $x++) {
       $userid = $s[0][$x];
       $user_id = pg_escape_string($userid);
       $check = pg_query($dbconn,"SELECT answer FROM sequentsteps WHERE sender_id = '{$user_id}' order by updated_at desc limit 1 ");
            while ($row = pg_fetch_row($check)) {
                echo  $code =  $row[0];
                } 
        if($code==2000){
           $messages1 = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ^^'
                     ];
             $url = 'https://api.line.me/v2/bot/message/push';
             $data = [
              'to' => $userid ,
              'messages' => [$messages1],
             ];
             error_log(json_encode($data));
             $post = json_encode($data);
             $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
             $ch = curl_init($url);
             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
             $result = curl_exec($ch);
             curl_close($ch);
             echo $result . "\r\n";
        }else{
 
                $messages1 = [
                                'type' => 'text',
                                'text' => 'วันนี้คุณทานอะไรไปบ้างคะ?'
                             ];

$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2001','','2002','0',NOW(),NOW())") or die(pg_errormessage()); 
                // $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,'0',  NOW()) ") or die(pg_errormessage());        
                     
                 $url = 'https://api.line.me/v2/bot/message/push';
                 $data = [
                  'to' => $userid ,
                  'messages' => [$messages1],
                 ];
                 error_log(json_encode($data));
                 $post = json_encode($data);
                 $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
                 $ch = curl_init($url);
                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                 $result = curl_exec($ch);
                 curl_close($ch);
                 echo $result . "\r\n";
        }
}
  // // Make a POST Request to Messaging API to reply to sender
  //        $url = 'https://api.line.me/v2/bot/message/reply';
  //        // $url2 = 'https://api.line.me/v2/bot/message/reply';
  //        $data = [
  //         'replyToken' => $replyToken,
  //         'messages' => [$messages1],
  //        ];
  //        error_log(json_encode($data));
  //        $post = json_encode($data);
  //        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
  //        $ch = curl_init($url);
  //        // $ch2 = curl_init($url2);
  //        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  //        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  //        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  //        $result = curl_exec($ch);
  //        curl_close($ch);
  //        echo $result . "\r\n"; 
?>