<?php
################################## DATABASE ##################################
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);

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

###########################################################################################################
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0008',$_msg,'0009','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "อายุถูกต้อง" && $seqcode == "0008"  ) {
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
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0009','','0010','0',NOW(),NOW())") or die(pg_errormessage());
 // }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
 //                 $replyToken = $event['replyToken'];
 //                 $messages = [
 //                        'type' => 'text',
 //                        'text' => 'กรุณาพิมพ์ใหม่ค่ะ'
 //                      ];     

###########################################################################################################
}elseif (is_numeric($_msg) !== false && $seqcode == "0009"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'ส่วนสูงปัจจุบันของคุณคือ'.$_msg.'เซ็นติเมตร ใช่ไหมคะ' ;
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
                                  'text' => 'ส่วนสูงถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0010',$_msg,'0011','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "ส่วนสูงถูกต้อง" && $seqcode == "0010"  ) {
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
                        'text' => 'ขอทราบน้ำหนักปกติก่อนตั้งครรภ์ค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม เช่น 55)'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET user_height = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0011','','0012','0',NOW(),NOW())") or die(pg_errormessage());

###########################################################################################################

}elseif (is_numeric($_msg) !== false && $seqcode == "0011"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'ก่อนตั้งครรภ์คุณมีน้ำหนัก'.$_msg.'กิโลกรัมใช่ไหมคะ' ;
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
                                  'text' => 'น้ำหนักก่อนตั้งครรภ์ถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0012',$_msg,'0013','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "น้ำหนักก่อนตั้งครรภ์ถูกต้อง" && $seqcode == "0012"  ) {
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
                        'text' => 'ขอทราบน้ำหนักปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม เช่น 59)'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET user_pre_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0013','','0014','0',NOW(),NOW())") or die(pg_errormessage());

###########################################################################################################

}elseif (is_numeric($_msg) !== false && $seqcode == "0013"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'น้ำหนักปัจจุบันของคุณคือ'.$_msg.'กิโลกรัมใช่ไหมคะ' ;
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
                                  'text' => 'น้ำหนักปัจจุบันถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0014',$_msg,'0015','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "น้ำหนักปัจจุบันถูกต้อง" && $seqcode == "0014"  ) {
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
                        'text' => 'ขอทราบครั้งสุดท้ายที่คุณมีประจำเดือนเพื่อคำนวณอายุครรภ์ค่ะ (กรุณาตอบวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0015','','0016','0',NOW(),NOW())") or die(pg_errormessage());




###########################################################################################################
 // }elseif (is_numeric($_msg) !== false && $seqcode == "0015"){
 }elseif (strlen($_msg) == 5 && $seqcode == "0015") {
    // $birth_years =  str_replace("วันที่","", $_msg);
    $pieces = explode(" ", $_msg);
    $date = str_replace("","",$pieces[0]);
    $month  = str_replace("","",$pieces[1]);
   
            $today_years= date("Y") ;
            $today_month= date("m") ;
            $today_day  = date("d") ;
          
            if(($month>$today_month&& $month<=12 && $date<=31) || ($month==$today_month && $date>$today_day)  ){
                $years = $today_years-1;
                $strDate1 = $years."-".$month."-".$date;
                $strDate2=date("Y-m-d");
                
                $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );
                $week = $date_pre/7;
                $week_preg = number_format($week);
                $day = $date_pre%7;
                $day_preg = number_format($day);
                $age_pre = 'คุณมีอายุครรภ์'. $week_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                      $replyToken = $event['replyToken'];
                      $messages = [
                          'type' => 'template',
                          'altText' => 'this is a confirm template',
                          'template' => [
                              'type' => 'confirm',
                              'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                              'actions' => [
                                  [
                                      'type' => 'message',
                                      'label' => 'ใช่',
                                      'text' => 'อายุครรภ์ถูกต้อง'
                                  ],
                                  [
                                      'type' => 'message',
                                      'label' => 'ไม่ใช่',
                                      'text' => 'ไม่ถูกต้อง'
                                  ],
                              ]
                          ]
                      ];   
            
            }elseif($month<$today_month && $month<=12 && $date<=31){
                $strDate1 = $today_years."-".$month."-".$date;
                $strDate2=date("Y-m-d");
                $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );;
                $week = $date_pre/7;
                $week_preg = number_format($week);
                $day = $date_pre%7;
                $day_preg = number_format($day);
                $age_pre = 'คุณมีอายุครรภ์'. $week_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                    $replyToken = $event['replyToken'];
                    $messages = [
                        'type' => 'template',
                        'altText' => 'this is a confirm template',
                        'template' => [
                            'type' => 'confirm',
                            'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => 'ใช่',
                                    'text' => 'อายุครรภ์ถูกต้อง'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'ไม่ใช่',
                                    'text' => 'ไม่ถูกต้อง'
                                ],
                            ]
                        ]
                    ];   
            }else{
               $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง'
                      ];
            }
  
      $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages],
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','00016', $week_preg ,'0017','0',NOW(),NOW())") or die(pg_errormessage());


 }elseif ($event['message']['text'] == "อายุครรภ์ถูกต้อง" && $seqcode == "0016" ) {
    $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบเบอร์โทรศัพท์ของคุณหน่อยค่ะ'
                      ];
   
    // $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id,preg_week,preg_weight,updated_at  )VALUES('{$user_id}',$answer ,'0',NOW()) ") or die(pg_errormessage());   

 $q = pg_exec($dbconn, "UPDATE users_register SET preg_week = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0017','','0018','0',NOW(),NOW())") or die(pg_errormessage());


###########################################################################################################

}elseif (is_numeric($_msg) !== false && $seqcode == "0017"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'เบอร์โทรศัพท์ของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
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
                                  'text' => 'เบอร์โทรศัพท์ถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0018',$_msg,'0019','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "เบอร์โทรศัพท์ถูกต้อง" && $seqcode == "0018"  ) {
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
                        'text' => 'ขอทราบชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์หน่อยค่ะ'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET phone_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019','','0020','0',NOW(),NOW())") or die(pg_errormessage());


###########################################################################################################


 }elseif (strpos($_msg) !== false && $seqcode == "0019" ) {
    
  $u = pg_escape_string($_msg);
    $ans = 'ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์คือ'.$_msg.'ใช่ไหมคะ?' ;
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
                    'text' => 'ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์ถูกต้อง'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่ใช่',
                    'text' => 'ไม่ถูกต้อง'
                ],
            ]
        ]
    ];     
      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0018','{$u}','0019','0',NOW(),NOW())") or die(pg_errormessage());



 }elseif ($event['message']['text'] == "ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์ถูกต้อง" && $seqcode == "0018"  ) {
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
                        'text' => 'ขอทราบเลขประจำตัวผู้ป่วยของโรงพยาบาลที่คุณแม่ไปฝากครรภ์หน่อยค่ะ'
                      ];

$q = pg_exec($dbconn, "UPDATE users_register SET hospital_name = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q = pg_exec($dbconn, "INSERT INTO users_register(user_id,hospital_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019','','0020','0',NOW(),NOW())") or die(pg_errormessage());
 // }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
 //                 $replyToken = $event['replyToken'];
 //                 $messages = [
 //                        'type' => 'text',
 //                        'text' => 'กรุณาพิมพ์ใหม่ค่ะ'
 //                      ];     


###########################################################################################################


}elseif (is_numeric($_msg) !== false && $seqcode == "0021"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'เลขประจำตัวผู้ป่วยของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
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
                                  'text' => 'เลขประจำตัวผู้ป่วยของถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0022',$_msg,'0023','0',NOW(),NOW())") or die(pg_errormessage());

                    

 }elseif ($event['message']['text'] == "เลขประจำตัวผู้ป่วยของถูกต้อง" && $seqcode == "0022"  ) {
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
                 // $messages = [
                 //        'type' => 'text',
                 //        'text' => 'ขอทราบชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์หน่อยค่ะ'
                 //      ];

                $messages = [
                  'type'=> 'template',
                  'altText'=> 'this is a buttons template',
                  'template'=> [
                      'type'=> 'buttons',
                      'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
                      'title'=> 'Menu',
                      'text'=> 'Please select',
                      'actions'=> [
                          [
                            'type'=> 'postback',
                            'label'=> 'Buy',
                            'data'=> 'action=buy&itemid=123'
                          ],
                          [
                            'type'=> 'postback',
                            'label'=> 'Add to cart',
                            'data'=> 'action=add&itemid=123'
                          ],
                          [
                            'type'=> 'uri',
                            'label'=> 'View detail',
                            'uri'=> 'http://example.com/page/123'
                          ]
                      ]
                  ]
                ];



 $q = pg_exec($dbconn, "UPDATE users_register SET hospital_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0023','','0024','0',NOW(),NOW())") or die(pg_errormessage());
















###########################################################################################################
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