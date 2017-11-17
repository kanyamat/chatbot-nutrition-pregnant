<?php  


}elseif (strlen($_msg) == 5 && $seqcode == "0015") {
    // $birth_years =  str_replace("วันที่","", $_msg);
    $pieces = explode(" ", $_msg);
    $date = str_replace("","",$pieces[0]);
    $month  = str_replace("","",$pieces[1]);
   
            $today_years= date("Y") ;
            $today_month= date("m") ;
            $today_day  = date("d") ;
          




            // if(($month>$today_month && $month<=12 && $date<=31) || ($month==$today_month && $date>$today_day)  ){
                $years = $today_years-1;
                $days = $today_day-1;
                $strDate1 = $years."-".$month."-".$date;
                $strDate2=date("Y-m-d");
                

                $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 )-1;
                $week = $date_pre/7;
                $week2 = $week-39;   

                $week_preg = number_format($week2);
                $day = $date_pre%7;
                $day2 = 7-$day

                $day_preg = number_format($day2);

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
?>