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
<<<<<<< HEAD
 // echo $user_id;
$check = pg_query($dbconn,"SELECT user_weight FROM user_data  WHERE  user_id = '{$user_id}'  ");
                while ($row= pg_fetch_row($check)) {
              
                 $result = $row[0];
  
  
=======
$u = pg_escape_string($_msg);  
$check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at  FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $seqcode =  $row[0];
                  echo $sender = $row[2]; 
>>>>>>> caa6e6370dd30a544c9985062210a5b67d738c59
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
 $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0005','0',NOW(),NOW())") or die(pg_errormessage());

#################################### ผู้ใช้เลือกสนใจ #################################### 
  }elseif ($event['message']['text'] == "สนใจ" && $seqcode == "0004"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0005'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
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
  
  }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'กรุณาพิมพ์ใหม่นะคะ'
                      ];  
                    
}elseif ($event['message']['text'] == "ชื่อถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
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

###########################################################################################################
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
      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0005','{$u}','0007','0',NOW(),NOW())") or die(pg_errormessage());

########################################################################################################################################################
 
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007',$_msg,'0009','0',NOW(),NOW())") or die(pg_errormessage());

                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "อายุถูกต้อง" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
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

########################################################################################################################################################
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0009',$_msg,'0011','0',NOW(),NOW())") or die(pg_errormessage());

                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ส่วนสูงถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
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

########################################################################################################################################################

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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0011',$_msg,'0013','0',NOW(),NOW())") or die(pg_errormessage());

                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "น้ำหนักก่อนตั้งครรภ์ถูกต้อง"  ) {
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

########################################################################################################################################################

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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0013',$_msg,'0015','0',NOW(),NOW())") or die(pg_errormessage());

########################################################################################################################################################                   

 }elseif ($event['message']['text'] == "น้ำหนักปัจจุบันถูกต้อง"  ) {
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
                  'type'=> 'template',
                  'altText'=> 'this is a buttons template',
                  'template'=> [
                      'type'=> 'buttons',
                      //'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
                      'title'=> "คุณมีอายุครรภ์กี่สัปดาห์คะ?",
                      'text'=> "กรุณาเลือกตอบข้อใดข้อหนึ่งเพื่อให้ทางเราคำนวณอายุครรภ์ค่ะ",
                      'actions'=> [
                          [
                            'type'=> 'message',
                            'label'=> 'ครั้งสุดท้ายที่มีประจำเดือน',
                            'text'=> 'ครั้งสุดท้ายที่เป็นประจำเดือน'
                          ],
                          [
                            'type'=> 'message',
                            'label'=> 'กำหนดการคลอด',
                            'text'=> 'กำหนดการคลอด'
                          ]
                      ]
                  ]
                ];





//$q = pg_exec($dbconn, "UPDATE users_register SET user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0015','','0016','0',NOW(),NOW())") or die(pg_errormessage());

 // $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,$answer ,  NOW()) ") or die(pg_errormessage());  


########################################################################################################################################################


 }elseif ($event['message']['text'] == "ครั้งสุดท้ายที่มีประจำเดือน"  ) {
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


########################################################################################################################################################

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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0015', $week_preg ,'0017','0',NOW(),NOW())") or die(pg_errormessage());


########################################################################################################################################################
 }elseif ($event['message']['text'] == "อายุครรภ์ถูกต้อง"  ) {
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

 $check_q = pg_query($dbconn,"SELECT user_weight FROM users_register  WHERE user_id  = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $p_week = $row[0];  
                } 
 $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,$answer ,  NOW()) ") or die(pg_errormessage());  

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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0017',$_msg,'0019','0',NOW(),NOW())") or die(pg_errormessage());

########################################################################################################################################################                    

 }elseif ($event['message']['text'] == "เบอร์โทรศัพท์ถูกต้อง"  ) {
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
                        'type' => 'text',
                        'text' => 'ขอทราบ E-mail ของคุณหน่อยค่ะ'
                      ];

 $q = pg_exec($dbconn, "UPDATE users_register SET phone_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019','','0020','0',NOW(),NOW())") or die(pg_errormessage());


########################################################################################################################################################   

}elseif (is_numeric($_msg) !== false && $seqcode == "0019"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'E-mailของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
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
                                  'text' => 'E-mailถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019',$_msg,'0021','0',NOW(),NOW())") or die(pg_errormessage());

########################################################################################################################################################    

 }elseif ($event['message']['text'] == "E-mailถูกต้อง" ) {
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

$q = pg_exec($dbconn, "UPDATE users_register SET email = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q = pg_exec($dbconn, "INSERT INTO users_register(user_id,hospital_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0021','','0022','0',NOW(),NOW())") or die(pg_errormessage());

#########################################################################################################################################################

}elseif (is_numeric($_msg) !== false && $seqcode == "0021"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   

                  $u = pg_escape_string($_msg);
                  $ans = 'ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์คือ'.$_msg.'ใช่ไหมคะ' ;
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0021',$_msg,'0023','0',NOW(),NOW())") or die(pg_errormessage());


########################################################################################################################################################

 }elseif ($event['message']['text'] == "ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์ถูกต้อง" ) {
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
                        'text' => 'ขอทราบเลขประจำตัวผู้ป่วยของคุณหน่อยค่ะ'
                      ];

$q = pg_exec($dbconn, "UPDATE users_register SET hospital_name = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q = pg_exec($dbconn, "INSERT INTO users_register(user_id,hospital_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0023','','0024','0',NOW(),NOW())") or die(pg_errormessage());


########################################################################################################################################################

}elseif (is_numeric($_msg) !== false && $seqcode == "0023"){
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0023',$_msg,'0025','0',NOW(),NOW())") or die(pg_errormessage());




// ########################################################################################################################################################



// ########################################################################################################################################################

 }elseif ($event['message']['text'] == "เลขประจำตัวผู้ป่วยของถูกต้อง") {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0];

                }   

                
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];


                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณมีประวัติการแพ้ยาไหมคะ?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'มี',
                                  'text' => 'แพ้ยา'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่มี',
                                  'text' => 'ไม่แพ้ยา'
                              ],
                          ]
                      ]
                  ];        


$q = pg_exec($dbconn, "UPDATE users_register SET hospital_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0025','','0026','0',NOW(),NOW())") or die(pg_errormessage());
                
// ########################################################################################################################################################

// }elseif ($event['message']['text'] == "แพ้ยา"  ) {
//                $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($result)) {
//                   echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
// ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
//                 }   

                

//                   // $pieces = explode("", $answer);
//                   // $name =str_replace("","",$pieces[0]);
//                   // $surname =str_replace("","",$pieces[1]);
//                  $u = pg_escape_string($answer);

//                   // $u2 = pg_escape_string($surname);
//                  $replyToken = $event['replyToken'];
//                  $messages = [
//                         'type' => 'text',
//                         'text' => 'คุณแพ้ยาอะไรคะ?'
//                       ];





// // $q = pg_exec($dbconn, "UPDATE users_register SET hospital_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//  $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0026','','0027','0',NOW(),NOW())") or die(pg_errormessage());

// ########################################################################################################################################################

// }elseif (strpos($_msg) !== false && $seqcode == "0026"  {
//                $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($result)) {
//                   echo $answer = $row[0]; 
//                 }  
//                  $u = pg_escape_string($_msg); 

// $q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$_msg}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 

//       $replyToken = $event['replyToken'];
//                   $messages = [
//                       'type' => 'template',
//                       'altText' => 'this is a confirm template',
//                       'template' => [
//                           'type' => 'confirm',
//                           'text' =>'คุณมีประวัติการแพ้อาหารไหมคะ?' ,
//                           'actions' => [
//                               [
//                                   'type' => 'message',
//                                   'label' => 'มี',
//                                   'text' => 'แพ้อาหาร'
//                               ],
//                               [
//                                   'type' => 'message',
//                                   'label' => 'ไม่มี',
//                                   'text' => 'ไม่แพ้อาหาร'
//                               ],
//                           ]
//                       ]
//                   ];        

//  $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0026','','0027','0',NOW(),NOW())") or die(pg_errormessage());

// ########################################################################################################################################################

// }elseif ($event['message']['text'] == "แพ้อาหาร" ) {
//                $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($result)) {
//                   echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
// ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
//                 }   

               
//                   // $pieces = explode("", $answer);
//                   // $name =str_replace("","",$pieces[0]);
//                   // $surname =str_replace("","",$pieces[1]);
//                  $u = pg_escape_string($answer);
//                   // $u2 = pg_escape_string($surname);
//                  $replyToken = $event['replyToken'];
//                  $messages = [
//                         'type' => 'text',
//                         'text' => 'คุณแพ้อาหารอะไรคะ?'
//                       ];
//  $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0027','','0028','0',NOW(),NOW())") or die(pg_errormessage());


// ########################################################################################################################################################

// }elseif (strpos($_msg) !== false && $seqcode == "0027" ) {
//                $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($result)) {
//                   echo $answer = $row[0]; 
//                 }   
//             $u = pg_escape_string($_msg); 
// $q = pg_exec($dbconn, "UPDATE users_register SET  history_food = '{$_msg}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//       $replyToken = $event['replyToken'];














//  $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0027','','1001','0',NOW(),NOW())") or die(pg_errormessage());


// ########################################################################################################################################################

//         $messages = [
//           'type'=> 'template',
//           'altText'=> 'this is a buttons template',
//           'template'=> [
//               'type'=> 'buttons',
//               //'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
//               'title'=> "ช่วงระหว่างการตั้งครรภ์คุณออกกำลังกายในระดับไหน",
//               'text'=> "Please select",
//               'actions'=> [
//                   [
//                     'type'=> 'message',
//                     'label'=> 'เบา',
//                     'text'=> 'เบา'
//                   ],
//                   [
//                     'type'=> 'message',
//                     'label'=> 'ปานกลาง',
//                     'text'=> 'ปานกลาง'
//                   ],
//                   [
//                     'type'=> 'message',
//                     'label'=> 'หนัก',
//                     'text'=> 'หนัก'
//                   ]
//               ]
//           ]
//         ];

// /*.........................................................................................................................................*/


// ############################################################### Summary ####################################################################################
//  }elseif ($event['message']['text'] == "หนัก" || $event['message']['text'] == "ปานกลาง" || $event['message']['text'] == "เบา"  ) {
                 


//      $check_q2 = pg_query($dbconn,"SELECT user_weight, user_height, preg_week FROM users_register WHERE user_id = '{$user_id}' order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($check_q2)) {
            
//                   echo $weight = $row[0]; 
//                   echo $height = $row[1]; 
//                   echo $preg_week = $row[2]; 
//                 } 
//           $height1 =$height*0.01;
//                   $bmi = $weight/($height1*$height1);
//                   $bmi = number_format($bmi, 2, '.', '');




//                  // $messages2 = [
//                  //        'type' => 'text',
//                  //        'text' => 'ขอบคุณสำหรับข้อมูลนะคะ'
//                  //      ];


//    $check_q2 = pg_query($dbconn,"SELECT user_weight, user_height, preg_week FROM users_register WHERE user_id = '{$user_id}' order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($check_q2)) {
            
//                   echo $weight = $row[0]; 
//                   echo $height = $row[1]; 
//                   echo $preg_week = $row[2]; 
//                 } 
//                   $height1 =$height*0.01;
//                   $bmi = $weight/($height1*$height1);
//                   $bmi = number_format($bmi, 2, '.', '');

//         if ($bmi<18.5) {
//           $result="Underweight";
//         } elseif ($bmi>=18.5 && $bmi<24.9) {
//           $result="Nomal weight";
//         } elseif ($bmi>=24.9 && $bmi<=29.9) {
//           $result="Overweight";
//         }else{
//           $result="Obese";
//         }

//    $check_q3 = pg_query($dbconn,"SELECT user_weight,user_age,preg_week  FROM users_register WHERE user_id = '{$user_id}' order by updated_at desc limit 1   ");
//                 while ($row = pg_fetch_row($check_q3)) {
            
//                   echo $weight = $row[0]; 
//                   echo $age = $row[1];
//           echo $preg_week = $row[2];
//                 } 

//         if ($age>=10 && $age<=18) {
//           $cal=(13.384*$weight)+692.6;
//         }elseif ($age>=18 && $age<=30) {
//           $cal=(14.818*$weight)+486.6;
//         }else{
//           $cal=(8.126*$weight)+845.6;
//         }

//         if ($_msg=="หนัก" ) {
//           $total = $cal*2.0;
//         }elseif($_msg=="ปานกลาง") {
//           $total = $cal*1.7;
//         }else{
//           $total = $cal*1.4;
//         }

//         if ($preg_week >=13 && $preg_week<=40) {
//           $a = $total+300;
//         }else{
//           $a=$total; 
//         }
        
//   $check_q4 = pg_query($dbconn,"SELECT starches ,vegetables, fruits, meats, fats, lf_milk, c, p, f, g_protein  FROM meal_planing WHERE caloric_level <=$total");
//                 while ($row = pg_fetch_row($check_q4)) {
            
//           //echo $caloric = $row[0]; 
//           echo $starches = $row[0];
//           echo $vegetables = $row[1];
//           echo $fruits = $row[2];
//           echo $meats = $row[3];
//           echo $fats = $row[4];
//           echo $lf_milk = $row[5];
//           echo $c = $row[6];
//           echo $p = $row[7];
//           echo $f = $row[8];
//           echo $g_protein  = $row[9];

//                 } 

//                   $bbb = "-ข้าววันละ". $starches ."ทัพพี". "\n".
//                           "-ผักวันละ". $vegetables. "ทัพพี"."\n".
//                           "-ผลไม้วันละ".$fruits."ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)"."\n".
//                           "-เนื้อวันละ" .$meats. "ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)"."\n".
//                           "-ไขมันวันละ" .$fats. "ช้อนชา"."\n".
//                           "-นมไขมันต่ำวันละ" .$lf_milk. "แก้ว";

//                 if ($total <= 1600) {
//                   $aaa=$bbb;
//                 } elseif ($total >= 1601 && $total<=1700) {
//                   $aaa=$bbb;
//                 }elseif ($total >=1701 && $total<=1800) {
//                   $aaa=$bbb;
//                 }elseif ($total >=1801 && $total<=1900) {
//                  $aaa=$bbb;
//                 }elseif ($total >=1901 && $total<=2000) {
//                   $aaa=$bbb;
//                 }elseif ($total >=2001 && $total<=2100 ) {
//                   $aaa=$bbb;
//                 }elseif ($total >= 2101 && $total<=2200) {
//                   $aaa=$bbb;
//                 }elseif ($total >= 2201 && $total <=2300) {
//                   $aaa=$bbb;
//                 }elseif ($total >= 2301 && $total <=2400) {
//                   $aaa=$bbb;
//                 }elseif ($total >= 2401 && $total <=2500) {
//                  $aaa=$bbb;
//                 }else {
//                   $aaa=$bbb;
//                 }
                




//       $replyToken = $event['replyToken'];
//                     $messages = [
                                                              
//                         'type' => 'template',
//                         'altText' => 'template',
//                         'template' => [
//                             'type' => 'buttons',
//                             'thumbnailImageUrl' => 'https://chatbot-nutrition-pregnant.herokuapp.com/week/'.$preg_week .'.jpg',
//                             'title' => 'ลูกน้อยของคุณ',
//                             'text' =>  'ขณะนี้คุณมีอายุครรภ์'.$preg_week.'สัปดาห์',
//                             'actions' => [
//                                           // [
//                                           //     'type' => 'postback',
//                                           //     'label' => 'good',
//                                           //     'data' => 'value'
//                                           // ],
//                                    [
//                                     'type' => 'uri',
//                                     'label' => 'กราฟ',
//                                     'uri' => 'https://chatbot-nutrition-pregnant.herokuapp.com/chart_bot.php?data='.$user_id
//                                     ]
//                                     ,
//                                     //                               [
//                                     // 'type' => 'message',
//                                     // 'label' => 'รายละเอียดเพิ่มเติม',
//                                     // 'text' => 'รายละเอียดเพิ่มเติม'
//                                     // ]

//                                       ]
//                                   ]
//                               ];


//             // $eatProtein=$weight+25;

//         // $messages3 = [
//     //                     'type' => 'text',
//     //                     'text' =>  'ขณะนี้คุณมีอายุครรภ์'.$preg_week. 'สัปดาห์'
//     //                   ];
//                 $messages2 = [
//                         'type' => 'text',
//                         'text' =>  'ค่าดัชนีมวลกายของคุณคือ'.$bmi. ' อยู่ในเกณฑ์ '.$result
//                       ];

//                 $messages3 = [
//                         'type' => 'text',
//                         'text' =>  'จำนวนแคลอรี่ที่คุณต้องการต่อวันคือ '.$total
//                       ];
//                 $messages4 = [
//                         'type' => 'text',
//                         'text' =>  $aaa
//                       ];
//                 // $messages5 = [
//                 //         'type' => 'text',
//                 //         'text' =>  'โปรตีนที่ต้องการ'.$eatProtein
//                 //       ];

//     $url = 'https://api.line.me/v2/bot/message/reply';
//          $data = [
//           'replyToken' => $replyToken,
//           'messages' => [$messages,$messages2,$messages3,$messages4],
//          ];
//          error_log(json_encode($data));
//          $post = json_encode($data);
//          $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
//          $ch = curl_init($url);
//          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//          curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
//          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//          $result = curl_exec($ch);
//          curl_close($ch);
//          echo $result . "\r\n";
    


#########################################################################END Summary#################################################################                   






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

