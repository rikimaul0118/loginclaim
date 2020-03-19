<?php
error_reporting(0);
function request($url, $token = null, $data = null, $pin = null, $otpsetpin = null, $uuid = null){

$header[] = "Host: api.gojekapi.com";
$header[] = "User-Agent: okhttp/3.10.0";
$header[] = "Accept: application/json";
$header[] = "Accept-Language: id-ID";
$header[] = "Content-Type: application/json; charset=UTF-8";
$header[] = "X-AppVersion: 3.49.2";
$header[] = "X-UniqueId: ".time()."57".mt_rand(1000,9999);
$header[] = "Connection: keep-alive";
$header[] = "X-User-Locale: id_ID";
if ($pin):
$header[] = "pin: $pin";
endif;
if ($token):
$header[] = "Authorization: Bearer $token";
  endif;
if ($otpsetpin):
$header[] = "otp: $otpsetpin";
endif;
if ($uuid):
$header[] = "User-uuid: $uuid";
endif;
$c = curl_init("https://api.gojekapi.com".$url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    if ($data):
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_POST, true);
    endif;
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_HEADER, true);
    curl_setopt($c, CURLOPT_HTTPHEADER, $header);
    $response = curl_exec($c);
    $httpcode = curl_getinfo($c);
    if (!$httpcode)
        return false;
    else {
        $header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE));
        $body   = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE));
    }
    $json = json_decode($body, true);
    return $json;
}
function save($filename, $content)
{
        $save = fopen($filename, "a");
        fputs($save, "$content\r\n");
        fclose($save);
}

function nama()
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $ex = curl_exec($ch);
        // $rand = json_decode($rnd_get, true);
        preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
        return $name[2][mt_rand(0, 14) ];
        }
function register($no)
        {
        $nama = nama();
        $email = str_replace(" ", "", $nama) . mt_rand(100, 999);
        $data = '{"name":"' . $nama . '","email":"' . $email . '@yahoo.com","phone":"+' . $no . '","signed_up_country":"ID"}';
        $register = request("/v5/customers", "", $data);
        //print_r($register);
        if ($register['success'] == 1)
                {
                return $register['data']['otp_token'];
                }
          else
                {
      save("error_log.txt", json_encode($register));
                return false;
                }
        }
function verif($otp, $token)
        {
        $data = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $token . '"},"client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e"}';
        $verif = request("/v5/customers/phone/verify", "", $data);
        if ($verif['success'] == 1)
                {
                return $verif['data']['access_token'];
                }
          else
                {
      save("error_log.txt", json_encode($verif));
                return false;
                }
        }
        function login($no)
        {
        $nama = nama();
        $email = str_replace(" ", "", $nama) . mt_rand(100, 999);
        $data = '{"phone":"+'.$no.'"}';
        $register = request("/v4/customers/login_with_phone", "", $data);
        //print_r($register);
        if ($register['success'] == 1)
                {
                return $register['data']['login_token'];
                }
          else
                {
      save("error_log.txt", json_encode($register));
                return false;
                }
        }
function veriflogin($otp, $token)
        {
        $data = '{"client_name":"gojek:cons:android","client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e","data":{"otp":"'.$otp.'","otp_token":"'.$token.'"},"grant_type":"otp","scopes":"gojek:customer:transaction gojek:customer:readonly"}';
        $verif = request("/v4/customers/login/verify", "", $data);
        if ($verif['success'] == 1)
                {
                return $verif['data']['access_token'];
                }
          else
                {
      save("error_log.txt", json_encode($verif));
                return false;
                }
        }
function claim2($token)
        {
        $data = '{"promo_code":"BELANJALAGI"}';
        $claim2 = request("/go-promotions/v1/promotions/enrollments", $token, $data);
        if ($claim2['success'] == 1)
                {
                return $claim2['data']['message'];
                }
          else
                {
      save("error_log.txt", json_encode($claim2));
                return false;
                }
         }
function claim($token)
        {
        $data = '{"promo_code":"KEALFAYUK"}';
        $claim = request("/go-promotions/v1/promotions/enrollments", $token, $data);
        if ($claim['success'] == 1)
                {
                return $claim['data']['message'];
                }
          else
                {
      save("error_log.txt", json_encode($claim));
                return false;
                }
        }
function color($color = "default" , $text)
        {
        $arrayColor = array(
            'grey'      => '1;30',
            'red'       => '1;31',
            'green'     => '1;32',
            'yellow'    => '1;33',
            'blue'      => '1;34',
            'purple'    => '1;35',
            'nevy'      => '1;36',
            'white'     => '1;0',
        );
        return "\033[".$arrayColor[$color]."m".$text."\033[0m";
    }
function fetch_value($str,$find_start,$find_end)
    {
        $start = @strpos($str,$find_start);
        if ($start === false) {
            return "";
        }
        $length = strlen($find_start);
        $end    = strpos(substr($str,$start +$length),$find_end);
        return trim(substr($str,$start +$length,$end));
    }
function getStr($a,$b,$c)
    {
        $a = @explode($a,$c)[1];
        return @explode($b,$a)[0];
    }
function getStr1($a,$b,$c,$d)
    {
            $a = @explode($a,$c)[$d];
            return @explode($b,$a)[0];
    }

echo color("green","======== BOT AUTO CLAIM VOCHER==========\n");
echo color("green","===========Free for everyone=========\n");
no:
echo color("green"," Nomer di Mulai dari 62\n");
echo color("red"," LOGIN MASUKKAN NO : ");
$nope = trim(fgets(STDIN));
$login = login($nope);
if ($login == false)
        {
        echo color("red","NO BELUM TERDAFTAR \n");
        goto no;
        }
  else
        {
       otp:
        echo color("green", $login. "\n");
        echo color("blue",  "MASUKKAN OTP : ");
        // echo color("blue","Enter Number: "));
        $otp = trim(fgets(STDIN));
        $verif = veriflogin($otp, $login);
        if ($verif == false)
                {
                echo "OTP nya Bener kagak !?\n";
                goto otp;
                }
          else
                {
                echo color("green", $verif. "\n");
                echo color("red","===========(RENDEEM VOUCHER)===========");                                   
                echo "\n".color("yellow","!] Claim voc ALFAMART");
                echo "\n".color("yellow","!] Please wait");
                for($a=1;$a<=3;$a++){
                echo color("yellow",".");
         sleep(1); 
                 }
                $claim = claim($verif);
                if ($claim == false)
                        {
                        echo color("red","GAGAL COBA REDEM MANUAL\n");
                        $h=fopen("zonk.txt","a");
                fwrite($h,json_encode(array('token' => $verif, 'voc' => $claim, 'no hp' => $nope,))."\n");
                fclose($h);
                        }
                  else
                        {
                        echo color("green", $claim . "\n");
                        $h=fopen("alfa.txt","a");
                fwrite($h,json_encode(array('token' => $verif, 'voc' => $claim, 'no hp' => $nope,))."\n");
                fclose($h);
                        }
                 next;
        echo color("red","===========(RENDEEM VOUCHER)===========");
        echo "\n".color("yellow","!] Claim voc GOFOOD");
        echo "\n".color("yellow","!] Please wait");
        for($a=1;$a<=3;$a++){
        echo color("yellow",".");
         sleep(1);
          }
         sleep(3);
       $claim2 = claim2($verif);
                if ($claim2 == false)
                        {
                        echo color("red","GAGAL COBA REDEEM MANUAL\n");
                        }
                  else
                        {
                        echo color("green", $claim2 . "\n");
                        }
      
    
  }

  }?>

