<?php
header('Content-Type: application/json; charset=UTF-8');
$get = $_GET['ip'];
$get = htmlspecialchars($get);

$db_con = mysqli_connect('##DB_ADDRESS##', '##DB_ID##', '##DB_PASSWORD##', '##DB_NAME##');
$db_sql = "SELECT * FROM `ip` WHERE `query` LIKE '$get'"; 
$db_load_result = mysqli_query($db_con, $db_sql); 
$db_row = mysqli_fetch_array($db_load_result);
$db_query = $db_row['query'];

if(empty($get)){
    echo "값이 없습니다";
    exit;
}

if(!empty($db_query)){
    $db_country = $db_row['country'];
    $db_countryCode = $db_row['countryCode'];
    $db_regionName = $db_row['regionName'];
    $db_city = $db_row['city'];
    $db_timezone = $db_row['timezone'];
    $db_currency = $db_row['currency'];
    $db_isp = $db_row['isp'];
    $db_org = $db_row['org'];
    $db_a1s = $db_row['a1s'];
    $db_mobile = $db_row['mobile'];
    $db_proxy = $db_row['proxy'];
    $db_hosting = $db_row['hosting'];

    if(empty($db_countryCode)){
        echo "값이 틀렸습니다";
        exit;
    }

    $array = array(
            "query" => "$db_query",
            "country" => "$db_country",
            "countryCode" => "$db_countryCode",
            "regionName" => "$db_regionName",
            "city" => "$db_city",
            "timezone" => "$db_timezone",
            "currency" => "$db_currency",
            "isp" => "$db_isp",
            "org" => "$db_org",
            "as" => "$db_a1s",
            "mobile" => "$db_mobile",
            "proxy" => "$db_proxy",
            "hosting" => "$db_hosting"
    );
    
    $result_final = json_encode($array,JSON_UNESCAPED_UNICODE);
    echo $result_final;
    
}else {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/$get?fields=25423647");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    $output = json_decode($output, true);
    $query = $output['query'];
    $one_country = $output['country'];
    $countryCode = $output['countryCode'];
    $one_regionName = $output['regionName'];
    $one_city = $output['city'];
    $timezone = $output['timezone'];
    $one_isp = $output['isp'];
    $one_org = $output['org'];
    $as = $output['as'];
    $mobile = $output['mobile'];
    $proxy = $output['proxy'];
    $hosting = $output['hosting'];
    $currency = $output['currency'];
    curl_close($ch);

    $country_sql = "SELECT * FROM `country` WHERE `en` LIKE '$one_country'"; 
    $country_result = mysqli_query($db_con, $country_sql); 
    $country_row = mysqli_fetch_array($country_result);
    $country_db = $country_row['ko'];
    if(isset($country_db)){
        $country = $country_row['ko'];
    }else{
        $file = file_get_contents("https://api.winsub.kr/kakao/?target=kr&input=$one_country&key=winsub_service");
        $json = json_decode($file, true);
        $country = $json['result']['output'];
        $country_sql = "INSERT INTO `country` (`en`, `ko`) VALUES ('$one_country', '$country');"; 
        mysqli_query($db_con, $country_sql);
    }

    $regionname_sql = "SELECT * FROM `regionname` WHERE `en` LIKE '$one_regionName'"; 
    $regionname_result = mysqli_query($db_con, $regionname_sql); 
    $regionname_row = mysqli_fetch_array($regionname_result);
    $regionName_db = $regionname_row['ko'];
    if(isset($regionName_db)){
        $regionName = $regionname_row['ko'];
    }else{
        $file = file_get_contents("https://api.winsub.kr/kakao/?target=kr&input=$one_regionName&key=winsub_service");
        $json = json_decode($file, true);
        $regionName = $json['result']['output'];
        $regionname_sql = "INSERT INTO `regionname` (`en`, `ko`) VALUES ('$one_regionName', '$regionName');"; 
        mysqli_query($db_con, $regionname_sql);
    }

    $city_sql = "SELECT * FROM `city` WHERE `en` LIKE '$one_city'"; 
    $city_result = mysqli_query($db_con, $city_sql); 
    $city_row = mysqli_fetch_array($city_result);
    $city_db = $city_row['ko'];
    if(isset($city_db)){
        $city = $city_row['ko'];
    }else{
        $file = file_get_contents("https://api.winsub.kr/kakao/?target=kr&input=$one_city&key=winsub_service");
        $json = json_decode($file, true);
        $city = $json['result']['output'];
        $city_sql = "INSERT INTO `city` (`en`, `ko`) VALUES ('$one_city', '$city');"; 
        mysqli_query($db_con, $city_sql);
    }

    $isp_sql = "SELECT * FROM `isp` WHERE `en` LIKE '$one_isp'"; 
    $isp_result = mysqli_query($db_con, $isp_sql); 
    $isp_row = mysqli_fetch_array($isp_result);
    $isp_db = $isp_row['ko'];
    if(isset($isp_db)){
        $isp = $isp_row['ko'];
    }else{
        $isp = $one_isp;
        $isp_sql = "INSERT INTO `isp` (`en`, `ko`) VALUES ('$one_isp', '$isp');"; 
        mysqli_query($db_con, $isp_sql);
    }
    
    $org_sql = "SELECT * FROM `org` WHERE `en` LIKE '$one_org'"; 
    $org_result = mysqli_query($db_con, $org_sql); 
    $org_row = mysqli_fetch_array($org_result);
    $org_db = $org_row['ko'];
    if(isset($org_db)){
        $org = $org_row['ko'];
    }else{
        $org = $one_org;
        $org_sql = "INSERT INTO `org` (`en`, `ko`) VALUES ('$one_org', '$org');"; 
        mysqli_query($db_con, $org_sql);
    }

    if(empty($mobile)){
        $mobile = "false";
    }else{
        $mobile = "true";
    }

    if(empty($proxy)){
        $proxy = "false";
    }else{
        $proxy = "true";
    }

    if(empty($hosting)){
        $hosting = "false";
    }else{
        $hosting = "true";
    }

    $array = array(
        "query" => "$query",
        "country" => "$country",
        "countryCode" => "$countryCode",
        "regionName" => "$regionName",
        "city" => "$city",
        "timezone" => "$timezone",
        "currency" => "$currency",
        "isp" => "$isp",
        "org" => "$org",
        "as" => "$as",
        "mobile" => "$mobile",
        "proxy" => "$proxy",
        "hosting" => "$hosting",
    );
    
    $ip_sql = "INSERT INTO `ip` (`query`, `country`, `countryCode`, `regionName`, `city`, `timezone`, `currency`, `isp`, `org`, `a1s`, `mobile`, `proxy`, `hosting`) VALUES ('$query', '$country', '$countryCode', '$regionName', '$city', '$timezone', '$currency', '$isp', '$org', '$as', '$mobile', '$proxy', '$hosting');"; 
    $ip_result = mysqli_query($db_con, $ip_sql); 

    $result_final = json_encode($array,JSON_UNESCAPED_UNICODE);
    echo $result_final;

}




?>
