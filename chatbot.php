<?php
################################## DATABASE ##################################
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);

##############################################################################

$access_token = 'eBp/ZVsDsV2fMTqSBYGq4pgOvc+sgaPxxJFeT/rvpT/WTLiyw44BA2co2RBVROiLPVr8EEMrdiJ2I5cKWBe+j+GhNrHu6FUEHyol1dGf8DM/ZykdR84RgfTU2p+3U9NnhjqhWkDrN0tQT56rf23TxQdB04t89/1O/w1cDnyilFU=';

$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// $curr_years = date("Y");
// $curr_y = ($curr_years+ 543);
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

####################################  insert data to sequentsteps   ####################################
   $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0003','','0005','0',NOW(),NOW())") or die(pg_errormessage());


#################################### ผู้ใช้เลือกสนใจ #################################### 
  }elseif ($event['message']['text'] == "สนใจ" && $seqcode == "0004"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0005'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   

                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];

                $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0005','','0006','0',NOW(),NOW())") or die(pg_errormessage());

#################################### ผู้ใช้เลือกไม่สนใจ ####################################    
  }elseif ($event['message']['text'] == "ไม่สนใจ" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ:) หากคุณสนใจในภายหลังให้พิมพ์ว่า"ต้องการผู้ช่วย"'
                      ];          
  

  }elseif (strpos($_msg) !== false && $seqcode == "0005" ) {
    
  $u = pg_escape_string($_msg);
    $ans = 'ชื่อของคุณคือ'.$_msg.'ใช่ไหมคะ?' ;
    $replyToken = $event['replyToken'];
    $messages = [
        'type' => 'template',
        'altText' => 'this is a confirm template',
        'template' => [
            'type' => 'confirm',
            'text' => $ans ,
            'actions' => [
                [
                    'type' => 'message',
                    'label' => 'ใช่',
                    'text' => 'ชื่อถูกต้อง'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่ใช่',
                    'text' => 'ไม่ถูกต้อง'
                ],
            ]
        ]
    ];     
      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0006','{$u}','0007','0',NOW(),NOW())") or die(pg_errormessage());



 }elseif ($event['message']['text'] == "ชื่อถูกต้อง" && $seqcode == "0006"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   

                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบอายุของคุณหน่อยค่ะ '
                      ];

$q = pg_exec($dbconn, "INSERT INTO users_register(user_id,user_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007','','0008','0',NOW(),NOW())") or die(pg_errormessage());
 // }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
 //                 $replyToken = $event['replyToken'];
 //                 $messages = [
 //                        'type' => 'text',
 //                        'text' => 'กรุณาพิมพ์ใหม่ค่ะ'
 //                      ];     

}elseif (is_numeric($_msg) !== false && $seqcode == "0007"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'คุณอายุ '.$_msg.'ปี ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'อายุถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007',$_msg,'0008','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "อายุถูกต้อง" && $seqcode == "0007"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   

                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบส่วนสูงปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยเซ็นติเมตร เช่น 160)'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET user_age = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0008','','0009','0',NOW(),NOW())") or die(pg_errormessage());
 // }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
 //                 $replyToken = $event['replyToken'];
 //                 $messages = [
 //                        'type' => 'text',
 //                        'text' => 'กรุณาพิมพ์ใหม่ค่ะ'
 //                      ];     













          

 

}elseif ($event['type'] == 'message' && $event['message']['type'] == 'text'){
    
     $replyToken = $event['replyToken'];
      $text = "ดิฉันไม่เข้าใจค่ะ กรุณาพิมพ์ใหม่อีกครั้งนะคะ";
      $messages = [
          'type' => 'text',
          'text' => $text
        ];

  }else {
   $replyToken = $event['replyToken'];
      $text = "หากคุณสนใจให้ดิฉันเป็นผู้ช่วยอัตโนมัติของคุณ โปรดกดยืนยันด้านล่างด้วยนะคะ";
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
                          ]
                      ]
                  ]
              ]; 
     $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0005','0',NOW(),NOW())") or die(pg_errormessage());         
    // $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0006','0',NOW(),NOW())") or die(pg_errormessage());
    // }else{
    //   $replyToken = $event['replyToken'];
    //   $text = "ฉันไม่เข้าใจค่ะ";
    //   $messages = [
    //       'type' => 'text',
    //       'text' => $text
    //     ];
  }
  
  
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

<!-- AIzaSyB5FmzSJk9yrpwHTyJMQSvl7EdjC7asyyU

search engin 014388729015054466439:gqr4m9bfx0i 

AIzaSyAtvPyVCpD6WNkS4cfqiIWb5-nBEXL9LK8
-->