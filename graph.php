<?php
require_once("dsn.php");

$id = filter_input(INPUT_POST,'id');
$koumoku = filter_input(INPUT_POST,'koumoku');
$date = strtotime(filter_input(INPUT_POST,'date'));

$link = new PDO($dsn['host'],$dsn['usr'],$dsn['pass']);
if($link==null){
 error_log("データベースに接続できませんでした。");
}
$link->query('SET NAMES utf8');

$sql = "SELECT hour(datetime) as hour ,".$koumoku." from WeatherInfo where ID = '".$id."' and datetime between '".date('Y-m-d 01:00:00',$date)."' and '".date('Y-m-d 00:59:59',strtotime("+1 days",$date))."' order by datetime";
$datalist = array();
//DB→配列
foreach($link->query($sql) as $row){
	$tmp = $row['hour'];
	$datalist[$tmp] = $row[$koumoku];
}
//graph header
$jsons = '{"cols":[{"id":"","label":"測定時間","pattern":"","type":"string"},{"id":"","label":"'.$id.'","pattern":"","type":"number"}],"rows":[';
//配列を回す。
for($i=1;$i<=24;$i++){
	$tmp = $datalist[$i];
	if(!$tmp){
		$tmp = "null";
	}
	$jsons .= '{"c":[{"v":"'.$i.'時","f":null},{"v":'.$tmp.',"f":null}]},';
}

$jsons = rtrim($jsons,",");
$jsons .= "]}";

echo $jsons;
?>
