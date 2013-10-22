<?php
// ---------------------------------------------------------
//  初期設定
// ---------------------------------------------------------
// サーバーネームの取得
$url = $_SERVER['SERVER_NAME'].'/';
// ユーザーエージェントの取得
$useragent = (!isset($_SERVER['HTTP_USER_AGENT']))? "" :$_SERVER['HTTP_USER_AGENT'];
// 現在のディレクトリを取得
$requetUri = $_SERVER['REQUEST_URI'];
$requetUri = ltrim($requetUri, '/');
$tempUrl   = explode('/',$requetUri);
$temp      = explode('?',$requetUri);

// gps 情報
$gpsinfo = '';

$gps_content_flag = "non_gps";

// ---------------------------------------------------------
//  GPSを取得するリンクを作成するためのフラグ
// ---------------------------------------------------------
// docomo
if(preg_match("/^DoCoMo/", $useragent)){
    $gps_content_flag = "docomo";
}
// softbank
elseif(preg_match("/^J-PHONE/",$useragent)
    || preg_match("/^Vodafone/",$useragent)
    || preg_match("/^SoftBank/",$useragent)){
    $gps_content_flag = "softbank";
}
// au
else if(preg_match("/^UP.Browser/",$useragent)
        || preg_match("/^KDDI/",$useragent)){
    $gps_content_flag = "au";
}

// ---------------------------------------------------------
//  GPSからの情報を受け取って処理する
// ---------------------------------------------------------
// au,docomo
if( isset($_GET['lat']) and isset($_GET['lon'] )){
    $gpsinfo = preg_replace('/ /', '',trim($_GET['lat'],'+').'c'.trim($_GET['lon'],'+'));
    header('Location: http://'.$url.$temp[0].'?gps_info='.$gpsinfo);
    exit;
}
// ソフトバンク
else if( isset($_GET['pos'] ) ) {
    $gpsinfo = $_GET['pos'];
    header('Location: http://'.$url.$temp[0].'?gps_info='.$gpsinfo);
    exit;
}
// ---------------------------------------------------------
//  位置情報を受け取ったら
// ---------------------------------------------------------
if( !empty($_GET['gps_info']) ) {
    require_once dirname(__FILE__).'/KeitaiGPS.php';
    $gps = new KeitaiGPS($_GET['gps_info']);
    $data['lat'] = $gps->data['lat'];
    $data['lon'] = $gps->data['lon'];
}

// ---------------------------------------------------------
//  表示を行う
// ---------------------------------------------------------
// view  を表示する
require_once dirname(__FILE__).'/OutputView.php';
$out_put = new OutputView();
$data['url']              = $url;
$data['temp']             = $temp;
$data['gps_content_flag'] = $gps_content_flag;
$data['meta_encode']      = "shift_jis";
$out_put->view_echo('./gps_view.php',$data,$data['meta_encode']);
