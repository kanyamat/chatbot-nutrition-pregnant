<?php
################################## DATABASE ##################################
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
##############################################################################

$access_token = 'eBp/ZVsDsV2fMTqSBYGq4pgOvc+sgaPxxJFeT/rvpT/WTLiyw44BA2co2RBVROiLPVr8EEMrdiJ2I5cKWBe+j+GhNrHu6FUEHyol1dGf8DM/ZykdR84RgfTU2p+3U9NnhjqhWkDrN0tQT56rf23TxQdB04t89/1O/w1cDnyilFU=';


$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
$curr_years = date("Y");
$curr_y = ($curr_years+ 543);
$_msg = $events['events'][0]['message']['text'];
$user = $events['events'][0]['source']['userId'];
$user_id = pg_escape_string($user);
$u = pg_escape_string($_msg);  
$check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at  FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $seqcode =  $row[0];
                  echo $sender = $row[2]; 
                } 
// $check_user = pg_query($dbconn,"SELECT*FROM users  WHERE $user_id  = '{$user_id}' ");
//****************ทดสอบ
       // $d = date("D");
       // $h = date("H:i");
//****************ทดสอบ จบ
// Validate parsed JSON data
if (!is_null($events['events'])) {
 // Loop through each event
 foreach ($events['events'] as $event) {
  // Reply only when message sent is in 'text' format
  // if ($event['message']['text'] == "ต้องการผู้ช่วย") {

 if (strpos($_msg, 'hello') !== false || strpos($_msg, 'สวัสดี') !== false || strpos($_msg, 'ต้องการผู้ช่วย') !== false) {

      $replyToken = $event['replyToken'];
      $text = "สวัสดีค่ะ คุณสนใจมีผู้ช่วยใช่ไหม";
      // $messages = [
      //   'type' => 'text',
      //   'text' => $text
      // ];
        $messages = [
       'type' => 'template',
        'altText' => 'this is a confirm template',
        'template' => [
            'type' => 'confirm',
            'text' => $text ,
            'actions' => [
                [
                    'type' => 'message',
                    'label' => 'สนใจ',
                    'text' => 'สนใจ'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่สนใจ',
                    'text' => 'ไม่สนใจ'
                ],
            ]
        ]
    ];


 
//  }elseif ($event['message']['text'] == "flex") {
     
//              $messages = [
//                 'type'=> 'bubble',
//                 'direction'=> 'ltr',
//                 'styles'=> {
//                     'header'=> {
//                         'backgroundColor'=> '#ffaaaa',
//                     },
//                     'body'=> {
//                         'backgroundColor'=> '#aaffaa',
//                         'separator'=> true
//                         'separatorColor'=> '#efefef'
//                     },
//                     'footer'=> {
//                         'backgroundColor'=> '#aaaaff'
//                     }
//                 },
//                 'header'=> {},
//                 'hero'=> {},
//                 'body'=> {},
//                 'footer'=> {}
//              ];

     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
########################################################################################################### 
}else{
    
     $replyToken = $event['replyToken'];
      $text = "ดิฉันไม่เข้าใจค่ะ กรุณาพิมพ์ใหม่อีกครั้งนะคะ";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
########################################################################################################################################################

 
  
 }
}
  // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";

?>

