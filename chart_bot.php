<?php
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user = $_GET["data"];
$user_id = pg_escape_string($user);
 // echo $user_id;
$check = pg_query($dbconn,"SELECT user_weight FROM user_data  WHERE  user_id = '{$user_id}'  ");
                while ($row= pg_fetch_row($check)) {
              
                 $result = $row[0];
  
                } 
$a =[];
$arrayName=[];
$check_q = pg_query($dbconn,"SELECT his_preg_week ,his_preg_weight FROM history_preg  WHERE  user_id = '{$user_id}'  ");
                while ($arr= pg_fetch_array($check_q)) {
                  $week = $arr[0];
                  $weight = $arr[1]-$result;
         
                  $arrayName[] = array( 'date' => $week,
                                      'duration'=> $weight);
                }   
$data = json_encode($arrayName);
// echo "var data = '$data';";
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<style>
#chartdiv {
  width   : 100%;
  height    : 500px;
  font-size : 11px;
}                            
</style>
<script type="text/javascript">
    
var chartData = generateChartData();
var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight": 80,
    "dataProvider": chartData,
    "balloon": {
        "cornerRadius": 6,
        "horizontalPadding": 15,
        "verticalPadding": 10
    },
    "valueAxes": [{
        "position": "left",
        "title": "weight"
    }],
    "graphs": [{
        "bullet": "square",
        "bulletBorderAlpha": 1,
        "bulletBorderThickness": 1,
        "fillAlphas": 0.3,
        "fillColorsField": "lineColor",
        "legendValueText": "[[value]]",
        "lineColorField": "lineColor",
        "title": "duration",
        "valueField": "duration"
    }],
    "chartScrollbar": {
    },
    "chartCursor": {
        "categoryBalloonDateFormat": "week",
        "cursorAlpha": 0,
        "fullWidth": true
    },
    
    "categoryField": "date",
    
    "categoryAxis": {
        "axisColor": "#555555",
        "gridAlpha": 0,
        "gridCount": 50
    },
    "export": {
        "enabled": true
    }
});
chart.addListener("dataUpdated", zoomChart);
function zoomChart() {
    // chart.zoomToDates(new Date(2012, 0, 3), new Date(2012, 0, 11));
}
// generate some random data, quite different range
function generateChartData() {
   var chartData = <?php 
$conn_string = "host=ec2-54-221-207-192.compute-1.amazonaws.com port=5432 dbname=ddarslmntab2u0 user=uuwabnobyyrnfe password=4d97f0b4150eb402dcfbd772910d388e127285bd85f3efea6184fe42da856142 ";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
$user = $_GET["data"];
$user_id = pg_escape_string($user);
 // echo $user_id;
$check = pg_query($dbconn,"SELECT user_weight FROM users  WHERE  user_id = '{$user_id}'  ");
                while ($row= pg_fetch_row($check)) {
              
                 $result = $row[0];
  
                } 
$arrayName=[];
$check_q = pg_query($dbconn,"SELECT preg_week ,preg_weight FROM recordofpregnancy WHERE  user_id = '{$user_id}' order by preg_week  ASC  ");
                while ($arr= pg_fetch_array($check_q)) {
                  $week = $arr[0];
           
                        $weight = $arr[1]-$result;
                        $weight1 = abs($weight);
                 
                  $arrayName[] = array( 'date' =>  $week ,
                                       'duration'=> $weight1);
                }  
echo $data = json_encode($arrayName);
 ?>;
    // chartData.push({
    //     "date": "2012-01-04",
    //     "duration": 408
    // }, {
    //     "date": "2012-02-04",
    //     "duration": 482
    // }, {
    //     "date": "2012-03-04",
    //     "duration": 562
    // }, {
    //     "date": "2012-04-04",
    //     "duration": 379
    // });
    return chartData;
}
</script>

<body>

<div id="chartdiv"></div>
</body>
</html>