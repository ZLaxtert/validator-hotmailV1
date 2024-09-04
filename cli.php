<?php

// DONT CHANGE THIS
/*==========> INFO 
 * CODE     : BY ZLAXTERT
 * SCRIPT   : HOTMAIL || OUTLOOK || MSN || LIVE || VALIDATOR
 * VERSION  : V1
 * TELEGRAM : t.me/zlaxtert
 * BY       : DARKXCODE
 */


//========> REQUIRE

require_once "function/function.php";
require_once "function/gangbang.php";
require_once "function/threesome.php";
require_once "function/settings.php";

//========> BANNER

echo banner();
echo banner2();

//========> GET FILE

enterlist:
echo "$WH [$GR+$WH] Your file ($YL example.txt $WH) $GR>> $BL";
$listname = trim(fgets(STDIN));
if (empty($listname) || !file_exists($listname)) {
    echo PHP_EOL . PHP_EOL . "$WH [$YL!$WH] $RD FILE NOT FOUND$WH [$YL!$WH]$DEF" . PHP_EOL . PHP_EOL;
    goto enterlist;
}
$lists = array_unique(explode("\n", str_replace("\r", "", file_get_contents($listname))));


//=========> THREADS

reqemail:
echo "$WH [$GR+$WH] Threads ($YL Max 30 $WH) ($YL Recommended 10-30 $WH) $GR>> $BL";
$reqemail = trim(fgets(STDIN));
$reqemail = (empty($reqemail) || !is_numeric($reqemail) || $reqemail <= 0) ? 15 : $reqemail;
if ($reqemail > 30) {
    echo PHP_EOL . PHP_EOL . "$WH [$YL!$WH] $RD MAX 30$WH [$YL!$WH]$DEF" . PHP_EOL . PHP_EOL;
    goto reqemail;
}

//=========> COUNT

$live = 0;
$die = 0;
$rto = 0;
$unknown = 0;
$limit = 0;
$no = 0;
$total = count($lists);
echo "\n\n$WH [$YL!$WH] TOTAL $GR$total$WH LISTS [$YL!$WH]$DEF\n\n";

//========> LOOPING

$rollingCurl = new \RollingCurl\RollingCurl();

foreach ($lists as $list) {
    // GET SETTINGS
    if (strtolower($mode_proxy) == "off") {
        $Proxies = "";
        $proxy_Auth = $proxy_pwd;
        $type_proxy = $proxy_type;
        $apikey = GetApikey($thisApikey);
        $APIs = GetApiS($thisApi);
    } else {
        $Proxies = GetProxy($proxy_list);
        $proxy_Auth = $proxy_pwd;
        $type_proxy = $proxy_type;
        $apikey = GetApikey($thisApikey);
        $APIs = GetApiS($thisApi);
    }
    // EXPLODE
    $email = multiexplode(array(":", "|", "/", ";", ""), $list)[0];
    $pass = multiexplode(array(":", "|", "/", ";", ""), $list)[1];
    //API
    $api = $APIs . "/validator/hotfams/?list=$email&proxy=$Proxies&proxyAuth=$proxy_Auth&type_proxy=$type_proxy&apikey=$apikey";
    //CURL
    $rollingCurl->setOptions(array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_MAXREDIRS => 10, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 200))->get($api);

}

//==========> ROLLING CURL

$rollingCurl->setCallback(function (\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
    global $listname, $no, $total, $live, $die, $unknown, $limit, $rto;
    $no++;
    parse_str(parse_url($request->getUrl(), PHP_URL_QUERY), $params);
    $list = $params["list"];
    //RESPONSE
    $x = $request->getResponseText();
    $js = json_decode($x, TRUE);
    $msg = $js['data']['msg'];
    $location = $js['data']['Location'];
    $device = $js['data']['device'];
    $app_Auth = $js['data']['app_Auth'];
    $jam = Jam();



    //============> COLLOR
    $BL = collorLine("BL");
    $RD = collorLine("RD");
    $GR = collorLine("GR");
    $YL = collorLine("YL");
    $MG = collorLine("MG");
    $DEF = collorLine("DEF");
    $CY = collorLine("CY");
    $WH = collorLine("WH");

    //============> RESPONSE

    if($device == ""){
        $template_ress = "";
        $template_save = "$list";
    }else {
        $template_ress = " [$YL DEVICE$DEF: $WH$device$DEF ] [$YL APP AUTH$DEF: $WH$app_Auth$DEF ] [$YL LOCATION$DEF: $WH$location$DEF ]";
        $template_save = "$list || $device || $app_Auth || $location";
    }


    if (strpos($x, '"status":"live"')) {
        $live++;
        save_file("result/live.txt", "$template_save");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$GR LIVE$DEF =>$BL $list$DEF |$template_ress [$YL MSG$DEF: $WH$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else if (strpos($x, '"msg":"Incorrect APIkey!"')) {

        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $list$DEF | [$YL MSG$DEF:$MG Incorrect APIkey!$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;

    } else if (strpos($x, '"status":"die"')) {
        $die++;
        save_file("result/die.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $list$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else if (strpos($x, '"status":"unknown"')) {
        $limit++;
        save_file("result/limit.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$CY LIMIT$DEF =>$BL $list$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else if ($x == "") {
        $rto++;
        save_file("result/RTO.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $list$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else if (strpos($x, 'Request Timeout')) {
        $rto++;
        save_file("result/RTO.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $list$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else if (strpos($x, 'Service Unavailable')) {
        $rto++;
        save_file("result/RTO.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $list$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    } else {
        $unknown++;
        save_file("result/unknown.txt", "$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$YL UNKNOWN$DEF =>$BL $list$DEF | BY$CY DARKXCODE$DEF (V1)" . PHP_EOL;
    }
})->setSimultaneousLimit((int) $reqemail)->execute();

//============> END

echo PHP_EOL;
echo "================[DONE]================" . PHP_EOL;
echo " DATE          : " . $date . PHP_EOL;
echo " LIVE          : " . $live . PHP_EOL;
echo " DIE           : " . $die . PHP_EOL;
echo " TIMEOUT       : " . $rto . PHP_EOL;
echo " UNKNOWN       : " . $unknown . PHP_EOL;
echo " TOTAL         : " . $total . PHP_EOL;
echo "======================================" . PHP_EOL;
echo "[+] RATIO VALID => $GR" . round(RatioCheck($live, $total)) . "%$DEF" . PHP_EOL . PHP_EOL;
echo "[!] NOTE : CHECK AGAIN FILE 'unknown.txt' or 'RTO.txt' [!]" . PHP_EOL;
echo "This file '" . $listname . "'" . PHP_EOL;
echo "File saved in folder 'result/' " . PHP_EOL . PHP_EOL;

// ==========> FUNCTION

function collorLine($col)
{
    $data = array(
        "GR" => "\e[32;1m",
        "RD" => "\e[31;1m",
        "BL" => "\e[34;1m",
        "YL" => "\e[33;1m",
        "CY" => "\e[36;1m",
        "MG" => "\e[35;1m",
        "WH" => "\e[37;1m",
        "DEF" => "\e[0m"
    );
    $collor = $data[$col];
    return $collor;
}
function multiexplode($delimiters, $string)
{
    $one = str_replace($delimiters, $delimiters[0], $string);
    $two = explode($delimiters[0], $one);
    return $two;
}

?>
