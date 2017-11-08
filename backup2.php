<?php
//database
$conn_string = "host=ec2-54-163-233-201.compute-1.amazonaws.com port=5432 dbname=dchdrsngrf50pd user=njppbbukwreesq password=c6b890bd6e0dccc4a5db3308869ba5e2735fe0e5df7a3f0de6f114cc24752e04";
$dbconn = pg_pconnect($conn_string);
//
$access_token = 'GKg1wAZ/gjMr6yh3dGmPjuq8HnkDQEZsOdPEfyur3h7JmjdT2JihbEBHL6S4BrLnHCuu0Cv2fSbvwv0/xZqYw+TEjmmqW2mjC5NB9BcVGguZq3CIHX+Vt+fvPcNwtcT2ER0LLVXSwhNN4aVJT0Q08QdB04t89/1O/w1cDnyilFU=';
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
$curr_years = date("Y");
$curr_y = ($curr_years+ 543);
$_msg = $events['events'][0]['message']['text'];
$user = $events['events'][0]['source']['userId'];
$user_id = pg_escape_string($user);
  
$check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at  FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $seqcode =  $row[0];
                  echo $sender = $row[2]; 
                } 
//****************ทดสอบ
       $d = date("D");
       $h = date("H:i");
//****************ทดสอบ จบ
// Validate parsed JSON data
if (!is_null($events['events'])) {
 // Loop through each event
 foreach ($events['events'] as $event) {
  // Reply only when message sent is in 'text' format
  if ($event['message']['text'] == "ต้องการผู้ช่วย") {
      $replyToken = $event['replyToken'];
      $text = "สวัสดีค่ะ คุณสนใจมีผู้ช่วยใช่ไหม";
   
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
   $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0006','0',NOW(),NOW())") or die(pg_errormessage());
  }elseif ($event['message']['text'] == "สนใจ" && $seqcode == "0004"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0006'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1];
                }   
                 //$text = 'ขอเริ่มสอบถามข้อมูลเบื้องต้นก่อนนะคะ ขอทราบพ.ศ.เกิดของคุณเพื่อคำนวณอายุ (ตัวอย่างการพิมพ์ เกิด2530)';
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];
                $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0006','','0007','0',NOW(),NOW())") or die(pg_errormessage());
   
  }elseif ($event['message']['text'] == "ไม่สนใจ" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ:) หากคุณสนใจในภายหลังให้พิมพ์ว่า"ต้องการผู้ช่วย"'
                      ];          
  
           
    
  }elseif (is_numeric($_msg) !== false && $seqcode == "0006"  && strlen($_msg) == 4 && $_msg < $curr_y && $_msg > "2500" ) {
  
    $birth_years = $_msg;
    $curr_years = date("Y"); 
    $age = ($curr_years+ 543)- $birth_years;
    $age_mes = 'ปีนี้คุณอายุ'.$age.'ปีถูกต้องหรือไม่คะ' ;
    $replyToken = $event['replyToken'];
    $messages = [
        'type' => 'template',
        'altText' => 'this is a confirm template',
        'template' => [
            'type' => 'confirm',
            'text' => $age_mes ,
            'actions' => [
                [
                    'type' => 'message',
                    'label' => 'ถูกต้อง',
                    'text' => 'อายุถูกต้อง'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่ถูกต้อง',
                    'text' => 'ไม่ถูกต้อง'
                ],
            ]
        ]
    ];     
      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0006', $age ,'0007','0',NOW(),NOW())") or die(pg_errormessage());
           
       // $q = pg_exec($dbconn, "INSERT INTO user_data(user_id,user_age,user_weight,user_height,preg_week )VALUES('{$user_id}',$age,'0','0','0') ") or die(pg_errormessage());      
  }elseif ($event['message']['text'] == "อายุถูกต้อง" ) {
      $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบครั้งสุดท้ายที่คุณมีประจำเดือนเพื่อคำนวณอายุครรภ์ค่ะ (กรุณาตอบประมาณวันที่และเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)
'
                      ];
           $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0008','','0009','0',NOW(),NOW())") or die(pg_errormessage());
           $q1 = pg_exec($dbconn, "INSERT INTO users(user_id,user_age,user_weight,user_height,preg_week,status,updated_at)VALUES('{$user_id}',$answer,'0','0','0','1',NOW()) ") or die(pg_errormessage());   
  }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'กรุณาพิมพ์ใหม่'
                      ];  
  }elseif (strlen($_msg) == 5 && $seqcode == "0008") {
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
                              'text' =>  $age_pre ,
                              'actions' => [
                                  [
                                      'type' => 'message',
                                      'label' => 'ถูกต้อง',
                                      'text' => 'อายุครรภ์ถูกต้อง'
                                  ],
                                  [
                                      'type' => 'message',
                                      'label' => 'ไม่ถูกต้อง',
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
                            'text' =>  $age_pre ,
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => 'ถูกต้อง',
                                    'text' => 'อายุครรภ์ถูกต้อง'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'ไม่ถูกต้อง',
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
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0008', $week_preg ,'0009','0',NOW(),NOW())") or die(pg_errormessage());
    
  }elseif ($event['message']['text'] == "อายุครรภ์ถูกต้อง" ) {
    $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขออนุญาตถามน้ำหนักปกติก่อนตั้งครรภ์ของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม)'
                      ];
    $q1 = pg_exec($dbconn, "UPDATE users SET preg_week = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage());   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0010', '','0011','0',NOW(),NOW())") or die(pg_errormessage());
    $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id,preg_week,preg_weight,updated_at  )VALUES('{$user_id}',$answer ,'0',NOW()) ") or die(pg_errormessage());   
  }elseif ($event['message']['text'] == "น้ำหนักก่อนตั้งครรภ์ถูกต้อง" ) {
         $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขออนุญาตถามน้ำหนักปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม)'
                      ];  
    $q1 = pg_exec($dbconn, "UPDATE users SET  user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage());   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0012', '','0013','0',NOW(),NOW())") or die(pg_errormessage());
  }elseif (is_numeric($_msg) !== false && $seqcode == "0010"  )  {
                 $weight =  $_msg;
                 $weight_mes = 'ก่อนตั้งครรภ์ คุณมีน้ำหนัก'.$weight.'กิโลกรัมถูกต้องหรือไม่คะ';
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $weight_mes ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ถูกต้อง',
                                            'text' => 'น้ำหนักก่อนตั้งครรภ์ถูกต้อง'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ถูกต้อง',
                                            'text' => 'ไม่ถูกต้อง'
                                        ],
                                    ]
                                 ]     
                             ];   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0010', $weight,'0011','0',NOW(),NOW())") or die(pg_errormessage()); 
}elseif ($event['message']['text'] == "น้ำหนักปัจจุบันถูกต้อง" ) {
         $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขออนุญาตถามส่วนสูงปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยเซ็นติเมตร)'
                      ];  
    $q1 = pg_exec($dbconn, "UPDATE recordofpregnancy SET preg_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage());   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0014', '','0015','0',NOW(),NOW())") or die(pg_errormessage());
  }elseif (is_numeric($_msg) !== false && $seqcode == "0012"  )  {
                 $weight =  $_msg;
                 $weight_mes = 'ปัจจุบัน คุณมีน้ำหนัก'.$weight.'กิโลกรัมถูกต้องหรือไม่คะ';
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $weight_mes ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ถูกต้อง',
                                            'text' => 'น้ำหนักปัจจุบันถูกต้อง'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ถูกต้อง',
                                            'text' => 'ไม่ถูกต้อง'
                                        ],
                                    ]
                                 ]     
                             ];   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0012', $weight,'0013','0',NOW(),NOW())") or die(pg_errormessage()); 
  
  }elseif ($event['message']['text'] == "ส่วนสูงถูกต้อง"  ) {
   $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
    $q1 = pg_exec($dbconn, "UPDATE users SET user_height = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage());   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0014', '$answer','0015','0',NOW(),NOW())") or die(pg_errormessage());
    $check_q = pg_query($dbconn,"SELECT  user_age, user_weight ,user_height ,preg_week  FROM users WHERE  user_id = '{$user_id}' ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $answer1 = $row[0]; 
                  echo $weight = $row[1]; 
                  echo $height = $row[2]; 
                  echo $answer4 = $row[3];  
                } 
                $height1 =$height*0.01;
                $bmi = $weight/($height1*$height1);
                $bmi = number_format($bmi, 2, '.', '');
                    $replyToken = $event['replyToken'];
                    $text = "ฉันไม่เข้าใจค่ะ";
                    $messages = [
                        'type' => 'text',
                        'text' =>  'ปัจจุบันคุณอายุ'.$answer1
                      ];
                    $messages1 = [
                        'type' => 'text',
                        'text' =>  'ค่าดัชนีมวลกาย'.$bmi
                      ];
                    $messages2 = [
                        'type' => 'text',
                        'text' => 'คุณมีอายุครรภ์'.$answer4.'สัปดาห์'
                      ];
    
                    $messages3 = [
                                                              
                                  'type' => 'template',
                                  'altText' => 'template',
                                  'template' => [
                                      'type' => 'buttons',
                                      'thumbnailImageUrl' => 'https://bottest14.herokuapp.com/week/'.$answer4 .'.jpg',
                                      'title' => 'ลูกน้อยของคุณ',
                                      'text' =>  'อายุ'.$answer4.'สัปดาห์',
                                      'actions' => [
                                          // [
                                          //     'type' => 'postback',
                                          //     'label' => 'good',
                                          //     'data' => 'value'
                                          // ],
                                          [
                                              'type' => 'uri',
                                              'label' => 'กราฟ',
                                              'uri' => 'https://bottest14.herokuapp.com/graph.php?data='.$user_id
                                          ]
                                      ]
                                  ]
                              ];
        
         $des_preg = pg_query($dbconn,"SELECT  descript,img FROM pregnants WHERE  week = $answer4  ");
              while ($row = pg_fetch_row($des_preg)) {
                  echo $des = $row[0]; 
                  echo $img = $row[1]; 
 
                } 
                    $messages4 = [
                        'type' => 'text',
                        'text' =>  $des
                      ];
         $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages, $messages1, $messages2,$messages3,$messages4],
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
  }elseif (is_numeric($_msg) !== false && $seqcode == "0014"  ) {
                 $height =  str_replace("ส่วนสูง","", $_msg);
                 $height_mes = 'ปัจจุบัน คุณสูง '.$height.'ถูกต้องหรือไม่คะ';
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $height_mes ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ถูกต้อง',
                                            'text' => 'ส่วนสูงถูกต้อง'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ถูกต้อง',
                                            'text' => 'ไม่ถูกต้อง'
                                        ],
                                    ]
                                 ]     
                             ];   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0014', $height,'0015','0',NOW(),NOW())") or die(pg_errormessage()); 
}elseif ($event['message']['text'] == "น้ำหนักถูกต้อง" && $seqcode ='0017') {
    $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
             
    $check = pg_query($dbconn,"SELECT preg_week FROM recordofpregnancy WHERE user_id = '{$user_id}' order by updated_at desc limit 1 ");
            while ($row = pg_fetch_row($check)) {
                echo  $p_week =  $row[0]+1;
                } 
    $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,$answer ,  NOW()) ") or die(pg_errormessage());  
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0000', '' ,'0000','0',NOW(),NOW())") or die(pg_errormessage()); 
$replyToken = $event['replyToken'];
                 $messages = [                            
                                  'type' => 'template',
                                  'altText' => 'template',
                                  'template' => [
                                      'type' => 'buttons',
                                      'thumbnailImageUrl' => 'https://bottest14.herokuapp.com/week/'.$p_week .'.jpg',
                                      'title' => 'ลูกน้อยของคุณ',
                                      'text' =>  'อายุ'.$p_week .'สัปดาห์',
                                      'actions' => [
                                          // [
                                          //     'type' => 'postback',
                                          //     'label' => 'good',
                                          //     'data' => 'value'
                                          // ],
                                          [
                                              'type' => 'uri',
                                              'label' => 'กราฟ',
                                              'uri' => 'https://bottest14.herokuapp.com/graph.php?data='.$user_id
                                          ]
                                      ]
                                  ]
                              ]; 
  }elseif (is_numeric($_msg) !== false && $seqcode == "0017"  )  {
                 $weight =  $_msg;
                 $weight_mes = 'สัปดาห์นี้คุณมีน้ำหนัก'.$weight.'กิโลกรัมถูกต้องหรือไม่คะ';
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $weight_mes ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ถูกต้อง',
                                            'text' => 'น้ำหนักถูกต้อง'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ถูกต้อง',
                                            'text' => 'ไม่ถูกต้อง'
                                        ],
                                    ]
                                 ]     
                             ];   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0017', $weight,'','0',NOW(),NOW())") or die(pg_errormessage()); 
}elseif(strpos($_msg, 'แพ้ท้อง') !== false || strpos($_msg, 'ตั้งครรภ์') !== false || strpos($_msg, 'คนท้อง') !== false || strpos($_msg, 'ปวดท้อง') !== false || strpos($_msg, 'ท้องแข็ง') !== false || strpos($_msg, 'ปวด') !== false || strpos($_msg, 'กิน') !== false || strpos($_msg, 'ทาน') !== false || strpos($_msg, 'ดื่ม') !== false || strpos($_msg, 'อาหาร') !== false || strpos($_msg, 'ฝากครรภ์') !== false || strpos($_msg, 'ฝากท้อง') !== false || strpos($_msg, 'หมอ') !== false || strpos($_msg, 'ยา') !== false || strpos($_msg, 'สมุนไพร') !== false || strpos($_msg, 'น้ำนม') !== false|| strpos($_msg, 'เลือดออก') !== false)  {
    $replyToken = $event['replyToken'];
    // $x_tra = str_replace("","", $_msg);
    $url = 'https://www.googleapis.com/customsearch/v1?&cx=011030528095328264272:_0c9oat4ztq&key=AIzaSyBgzyv2TiMpaZxxthxX1jYNdskfxi7ah_4&q='.$_msg.'ตั้งครรภ์';
    // $url2 = 'https://www.googleapis.com/customsearch/v1?&cx=014388729015054466439:gqr4m9bfx0i&key=AIzaSyDmVU8aawr5mNpqbiUdYMph8r7K-siKn-0&q='.$x_tra;
    $json= file_get_contents($url);
    // $json= file_get_contents($url2);
    
    $events = json_decode($json, true);
    $title= $events['items'][0]['title'];
    $title2= $events['items'][1]['title'];
    $title3= $events['items'][2]['title'];
    
    $link = $events['items'][0]['link'];
    $link2 = $events['items'][1]['link'];
    $link3 = $events['items'][2]['link'];
  
$messages = [
  'type'=> 'template',
  'altText'=> 'this is a carousel template',
  'template'=> [
      'type'=> 'carousel',
      'columns'=> [
          [
            'thumbnailImageUrl'=> 'https://ptcdn.info/pantip/pantip_logo_02.png',
            'title'=> $_msg,
            'text'=>  $title,
            'actions'=> [
                // [
                //     'type'=> 'postback',
                //     'label'=> 'link',
                //     'data'=> 'link'
                // ],
                // [
                //     'type'=> 'postback',
                //     'label'=> 'Add to cart',
                //    'data'=> 'action=add&itemid=111'
                // ],
                [
                    'type'=> 'uri',
                    'label'=> 'View detail',
                    'uri'=> $link
                ]
            ]
          ],
          [
            'thumbnailImageUrl'=> 'https://ptcdn.info/pantip/pantip_logo_02.png',
            'title'=>  $_msg,
            'text'=> $title2,
            'actions'=> [
                // [
                //     'type'=> 'postback',
                //     'label'=> 'link',
                //     'data'=>  'link'
                // ],
                // [
                //     'type'=> 'postback',
                //     'label'=> 'Add to cart',
                //    'data'=> 'action=add&itemid=111'
                // ],
                [
                    'type'=> 'uri',
                    'label'=> 'View detail',
                    'uri'=> $link2
                ]
            ]
          ],
          [
            'thumbnailImageUrl'=> 'https://ptcdn.info/pantip/pantip_logo_02.png',
            'title'=>  $_msg,
            'text'=> $title3,
            'actions'=> [
                // [
                //     'type'=> 'postback',
                //     'label'=> 'link',
                //     'data'=>  'link'
                // ],
                // [
                //     'type'=> 'postback',
                //     'label'=> 'Add to cart',
                //    'data'=> 'action=add&itemid=111'
                // ],
                [
                    'type'=> 'uri',
                    'label'=> 'View detail',
                    'uri'=> $link3
                ]
            ]
          ]
      ]
  ]
];
}elseif($event['message']['text'] == "Clear" ){
      $replyToken = $event['replyToken'];
      $text = "cleared!";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
    $sql =pg_exec($dbconn,"DELETE FROM users WHERE user_id = '{$user_id}' ");
    $sql1 =pg_exec($dbconn,"DELETE FROM recordofpregnancy WHERE user_id = '{$user_id}' ");
   
}elseif($event['message']['text'] == "x" ){
      $replyToken = $event['replyToken'];
      $text = "ออกจากการสอบถาม";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
   $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0000', '','0000','0',NOW(),NOW())") or die(pg_errormessage()); 
}elseif($event['message']['text'] == "Ramiหยุด" ){
      $replyToken = $event['replyToken'];
      $text = "RAMIหยุดการส่งข้อความให้คุณแล้วค่ะ";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
   pg_exec($dbconn, "UPDATE users SET status= 0 WHERE user_id = '{$user_id}' ") or die(pg_errormessage());
}elseif($event['message']['text'] == "Ramiทำงาน" ){
      $replyToken = $event['replyToken'];
      $text = "RAMIจะกลับมาส่งข้อความให้คุณอีกครั้ง";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
   pg_exec($dbconn, "UPDATE users SET status= 1 WHERE user_id = '{$user_id}' ") or die(pg_errormessage());
}elseif($events['events'][0]['message']['type'] == 'location') {
    $x_tra = str_replace("Unnamed Road","", $_msg);
    $url = 'https://www.googleapis.com/customsearch/v1?&cx=014388729015054466439:e_gyj6qnxr8&key=AIzaSyDmVU8aawr5mNpqbiUdYMph8r7K-siKn-0&q='.$x_tra;
    $json= file_get_contents($url);
    $events = json_decode($json, true);
    $address = $events['events'][0]['message']['address'] ;
    $replyToken = $event['replyToken'];
     // Build message to reply back
      $messages = [
          'type' => 'text',
          'text' => $address
        ];
// $location = $events['events'][0]['message']['type']; 
// $user_id = pg_escape_string($user);
//   "message": {
//     "id": "325708",
//     "type": "location",
//     "title": "my location",
//     "address": "〒150-0002 東京都渋谷区渋谷２丁目２１−１",
//     "latitude": 35.65910807942215,
//     "longitude": 139.70372892916203
//   }
}elseif ($event['type'] == 'message' && $event['message']['type'] == 'text'){
    
      $replyToken = $event['replyToken'];
      $text = "ดิฉันไม่เข้าใจค่ะ";
      $messages = [
          'type' => 'text',
          'text' => $text
        ];
 
}else{
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
     $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0006','0',NOW(),NOW())") or die(pg_errormessage());         
  }
  
  
 }
}
  // Make a POST Request to Messaging API to reply to sender
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
?>