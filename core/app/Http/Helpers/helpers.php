<?php

use App\Http\Controllers\RekeningController;
use App\Models\brodev;
use App\Models\BvLog;
use App\Models\DailyGold;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\LogActivity;
use App\Models\MemberGrow;
use App\Models\Plan;
use App\Models\PoolLog;
use App\Models\Rank;
use App\Models\RankFounder;
use App\Models\rekening;
use App\Models\SmsTemplate;
use App\Models\Transaction;
use App\Models\ureward;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserGold;
use App\Models\UserPin;
use App\Models\WeeklyGold;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

function sidebarVariation(){

    /// for sidebar
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded';
    //for overlay

    $variation['overlay'] = 'overlay--dark';
    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;

}

function systemDetails()
{
    $system['name'] = 'bisurv';
    $system['version'] = '1.0';
    return $system;
}

function getLatestVersion()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/version/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function title(){
    $user = Auth::user();
    $kiri = $user->userExtra->left;
    $kanan = $user->userExtra->right;
    $master_gold = 103;
    $grand_master = 1003;
    if($kiri >= $master_gold && $kanan >= $master_gold){
        return 'masterGold';
    }elseif($kiri >= $grand_master && $kanan >= $grand_master){
        return 'grandMaster';
    }else{
        return false;
    }
    
}

function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}


function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}


function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if (!empty($old)) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $image = Image::make($file);
    if (!empty($size)) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1],function($constraint){
            $constraint->upsize();
        });
    }
    $image->save($location . '/' . $filename);

    if (!empty($thumb)) {

        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1],function($constraint){
            $constraint->upsize();
        })->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null){
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if (!empty($old)) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location,$filename);
    return $filename;
}

function makeDirectory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}


function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}


function activeTemplate($asset = false)
{
    $gs = GeneralSetting::first(['active_template']);
    $template = $gs->active_template;
    $sess = session()->get('template');
    if (trim($sess) != null) {
        $template = $sess;
    }
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $gs = GeneralSetting::first(['active_template']);
    $template = $gs->active_template;
    $sess = session()->get('template');
    if (trim($sess) != null) {
        $template = $sess;
    }
    return $template;
}

function reCaptcha()
{
    $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}

function analytics()
{
    $analytics = Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function tawkto()
{
    $tawkto = Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}

function fbcomment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status',1)->first();
    return  $comment ? $comment->generateScript() : '';
}

function getCustomCaptcha($height = 46, $width = '300px', $bgcolor = '#003', $textcolor = '#abc')
{
    $textcolor = '#'.GeneralSetting::first()->base_color;
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    if($captcha){
        $code = rand(100000, 999999);
        $char = str_split($code);
        $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
        $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
        foreach ($char as $value) {
            $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
        }
        $ret .= '</div>';
        $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
        $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
        return $ret;
    }else{
        return false;
    }
}


function captchaVerify($code, $secret)
{
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    if ($captchaSecret == $secret) {
        return true;
    }
    return false;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getAmount($amount, $length = 0)
{
    if(0 < $length){
        return round($amount + 0, $length);
    }
    return $amount + 0;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet, $amount, $crypto = null)
{

    $varb = $wallet . "?amount=" . $amount;
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
}

//moveable
function curlContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//moveable
function curlPostContent($url, $arr = null)
{
    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function str_slug($title = null)
{
    return \Illuminate\Support\Str::slug($title);
}

function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
function getIpInfo()
{
    $ip = null;
    $deep_detect = TRUE;

    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }


    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = request()->ip();
    $data['time'] = date('d-m-Y h:i:s A');


    return $data;
}

//moveable
function osBrowser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    $browser = "Unknown Browser";
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }

    $data['os_platform'] = $os_platform;
    $data['browser'] = $browser;

    return $data;
}

function siteName()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}


//moveable
function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image,$size = null, $isAvatar=false)
{
    $clean = '';
    $size = $size ? $size : 'undefined';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }elseif($isAvatar){
        return asset('assets/images/avatar.png');
    }else{
        return route('placeholderImage',$size);
    }
}

function notify($user, $type, $shortCodes = null)
{

    sendEmail($user, $type, $shortCodes);
    sendSms($user, $type, $shortCodes);
}


/*SMS EMIL moveable*/

function sendSms($user, $type, $shortCodes = [])
{
    $general = GeneralSetting::first(['sn', 'sms_api']);
    $sms_template = SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    if ($general->sn == 1 && $sms_template) {

        $template = $sms_template->sms_body;

        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }
        $template = urlencode($template);

        $message = shortCodeReplacer("{{number}}", $user->mobile, $general->sms_api);
        $message = shortCodeReplacer("{{message}}", $template, $message);
        $result = @curlContent($message);
    }
}

function sendEmail($user, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();
    // $user = user::find($us);
    $email_template = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$email_template) {
        return;
    }

    $message = shortCodeReplacer("{{name}}", $user->username, $general->email_template);
    $message = shortCodeReplacer("{{message}}", $email_template->email_body, $message);

    if (empty($message)) {
        $message = $email_template->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }
    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$email_template->subj, $message);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    }
}

function sendEmail2($us, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();
    $user = user::find($us);

    $email_template = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$email_template) {
        return;
    }

    $message = shortCodeReplacer("{{name}}", $user->username, $general->email_template);
    $message = shortCodeReplacer("{{message}}", $email_template->email_body, $message);

    if (empty($message)) {
        $message = $email_template->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }
    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$email_template->subj, $message);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $email_template->subj, $message,$general);
    }
}


function sendPhpMail($receiver_email, $receiver_name, $subject, $message)
{
    $gnl = GeneralSetting::first();
    $headers = "From: $gnl->sitename <$gnl->email_from> \r\n";
    $headers .= "Reply-To: $gnl->sitename <$gnl->email_from> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $subject, $message, $headers);
}


function sendSmtpMail($config, $receiver_email, $receiver_name, $subject, $message,$gnl)
{
    $mail = new PHPMailer(true);

    $gnl = GeneralSetting::first();
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $config->username;
        $mail->Password   = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($gnl->email_from, $gnl->sitetitle);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($gnl->email_from, $gnl->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendSendGridMail($config, $receiver_email, $receiver_name, $subject, $message,$gnl)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($gnl->email_from, $gnl->sitetitle);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        // echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}


function sendMailjetMail($config, $receiver_email, $receiver_name, $subject, $message,$gnl)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $gnl->email_from,
                    'Name' => $gnl->sitetitle,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function getPaginate($paginate = 20)
{
    return $paginate;
}


function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open text-warning';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open text-warning';
    } else {
        $class = 'active text-warning';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}
function routeActive($routeName)
{
    $class = 'active text-warning';
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}


function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw'=>[
            'path'=>'assets/images/verify/withdraw'
        ],
        'deposit'=>[
            'path'=>'assets/images/verify/deposit'
        ]
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ]
    ];
    $data['ticket'] = [
        'path' => 'assets/images/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64'
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315'
    ];
    $data['survey'] = [
        'path' => 'assets/images/survey',
        'size' => '360x190'
    ];
    $data['product'] = [
        'path' => 'assets/images/product',
        'size' => '900x1200'
    ];
    $data['profile'] = [
        'user'=> [
            'path'=>'assets/images/user/profile',
            'size'=>'350x300'
        ],
        'admin'=> [
            'path'=>'assets/admin/images/profile',
            'size'=>'400x400'
        ]
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'd M, Y h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

//moveable
function sendGeneralEmail($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();

    if ($general->en != 1 || !$general->email_from) {
        return;
    }


    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{name}}", $receiver_name, $message);
    $config  = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$subject, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$subject, $message, $general);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null,$orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->latest()->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if($orderById){
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        }else{
            $content = $article->where('data_keys', $data_keys)->latest()->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl(){
    return 'user.deposit';
}

function paginateLinks($data, $design = 'admin.partials.paginate'){
    return $data->appends(request()->all())->links($design);
}

function printEmail($email)
{
    $beforeAt = strstr($email, '@', true);
    $withStar = substr($beforeAt, 0, 2) . str_repeat("**", 5) . substr($beforeAt, -2) . strstr($email, '@');
    return $withStar;
}

/* MLM FUNCTION  */

function getUserById($id)
{
    return User::find($id);
}

function createBVLog($user_id, $lr, $amount, $details){
    $bvlog = new BvLog();
    $bvlog->user_id = $user_id;
    $bvlog->position = $lr;
    $bvlog->amount = $amount;
    $bvlog->trx_type = '-';
    $bvlog->details = $details;
    $bvlog->save();
}


function mlmWidth()
{
    return 2;
}

function mlmPositions()
{
    return array(
        '1' => 'Left',
        '2' => 'Right',
    );
}

function getPosition($parentid, $position)
{
    $childid = getTreeChildId($parentid, $position);
   
    if ($childid != "-1") {
        $id = $childid;
    } else {
        $id = $parentid;
    }
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $nextchildid = getTreeChildId($id, $position);
            if ($nextchildid == "-1") {
                break;
            } else {
                $id = $nextchildid;
            }
        } else break;
    }

    $res['pos_id'] = $id;
    $res['position'] = $position;
    return $res;
}

function getTreeChildId($parentid, $position)
{
    $cou = User::where('pos_id', $parentid)->where('position', $position)->count();
    $cid = User::where('pos_id', $parentid)->where('position', $position)->first();
    if ($cou == 1) {
        return $cid->id;
    } else {
        return -1;
    }
}

function isUserExists($id)
{
    $user = User::find($id);
    if ($user) {
        return true;
    } else {
        return false;
    }
}

function getPositionId($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->pos_id;
    } else {
        return 0;
    }
}

function getPositionLocation($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->position;
    } else {
        return 0;
    }
}

function updateFreeCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->free_left += 1;
            } else {
                $extra->free_right += 1;
            }
            $extra->save();

            $id = $posid;

        } else {
            break;
        }
    }

}

function updatePaidCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
                $extra->left += 1;
                $extra->p_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
                $extra->right += 1;
                $extra->p_right += 1;
            }
            $strong = $extra->right > $extra->left ? $extra->right : $extra->left;
            $low = $extra->right < $extra->left ? $extra->right : $extra->left;
           
            if($extra->is_gold){
                $extra->rank = 1; //M1
            }elseif(($strong > 100 && $strong <= 10000) && ($low >= 100 )&& $extra->is_gold){
                 $extra->rank = 2; //M10
            }elseif(($strong > 10000 && $strong <= 15000) && ($low >= 100 ) && $extra->is_gold){
                $extra->rank = 3; //M15
            }elseif(($strong > 15000 && $low >= 100 ) && $extra->is_gold){
                $extra->rank = 4; //MU
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

}
function updatePaidCount2($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->paid_left += 1;
                $extra->left += 1;
                $extra->p_left += 1;
                $extra->bv_left += 1;
               
            } else {
                // $extra->free_right -= 1;
                $extra->paid_right += 1;
                $extra->right += 1;
                $extra->p_right += 1;
                $extra->bv_right += 1;
            }
            $strong = $extra->right > $extra->left ? $extra->right : $extra->left;
            $low = $extra->right < $extra->left ? $extra->right : $extra->left;
           
            if($extra->is_gold){
                $extra->rank = 1; //M1
            }elseif(($strong > 100 && $strong <= 10000) && ($low >= 100 )&& $extra->is_gold){
                 $extra->rank = 2; //M10
            }elseif(($strong > 10000 && $strong <= 15000) && ($low >= 100 ) && $extra->is_gold){
                $extra->rank = 3; //M15
            }elseif(($strong > 15000 && $low >= 100 ) && $extra->is_gold){
                $extra->rank = 4; //MU
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

}

function updatePaidCountRO($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->paid_left += 1;
                $extra->bv_left += 1;
                $extra->p_left += 1;
               
            } else {
                 $extra->paid_right += 1;
                $extra->bv_right += 1;
                $extra->p_right += 1;
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }
}

function UpdateCountingUser($username,$pos,$counting = 0)
{
    // dd($counting);
    $user1 =User::where('username',$username)->first();
    $ux= UserExtra::where('user_id',$user1->id)->first();
    $data = [];
    $data[1] = [$user1->username]; 
    if ($pos == 1) {
        $ux->paid_left -= $counting;
        $ux->bv_left -= $counting;
        $ux->p_left -= $counting;
        $ux->left -= $counting;

        $data[1] += [
            '1st kiri'
        ];
    } else {
        $ux->paid_right -= $counting;
        $ux->bv_right -= $counting;
        $ux->p_right -= $counting;
        $ux->right -= $counting;

        $data[1] += [
            '1st kanan'
        ];
    }
    $ux->save();
    $data[1] += [
                    $ux->paid_right,
                    $ux->bv_right,
                    $ux->p_right,
                    $ux->right,
                ];
    $id = $user1->id;
    $count = 2;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);

            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();
            $data[$count] = [$extra->user->username]; 
            if ($position == 1) {
                $extra->paid_left-= $counting;
                $extra->bv_left-= $counting;
                $extra->p_left-= $counting;
                $extra->left-= $counting;
                $data[$count]+= [
                    $count. ' kanan'
                ];

            } else {
                $extra->paid_right-= $counting;
                $extra->bv_right-= $counting;
                $extra->p_right-= $counting;
                $extra->right-= $counting;
                $data[$count]+= [
                     $count. ' kanan'
                ];
            }
            $extra->save();
            $data[$count] += [
                    $extra->paid_right,
                    $extra->bv_right,
                    $extra->p_right,
                    $extra->right,
                ];
            $id = $posid;
            $count++;
        } else {
            break;
        }
    }
    return $data;

}

function getRefId($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->ref_id;
    } else {
        return 0;
    }
}

function monolegSaving($id, $amount, $username, $type)
{
    $fromUser = User::find($id);
    $gnl = GeneralSetting::first();
    

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getRefId($id);
            if ($posid == "0") {
                break;
            }

            // $username = User::where('id',$id)->first();
            $posUser = User::find($posid);
            $posUserExtra = UserExtra::find($posid);
            $strong = $posUserExtra->paid_left > $posUserExtra->paid_right ? $posUserExtra->paid_left : $posUserExtra->paid_right;
            // $count++;
            if ($posUserExtra->is_gold == 1) {

                // $posUserExtra->monoleg_downline += $amount;
                // $posUserExtra->save();
                $payment = User::find($posUserExtra->user_id);
                $payment->balance += $amount;
                $payment->save();

                $trx = new Transaction();
                $trx->user_id = $payment->id;
                $trx->amount = $amount;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = $payment->balance;
                $trx->remark = 'monoleg_commission_downline';
                $trx->trx = getTrx();
                $trx->details = 'Paid Monoleg Commission from downline '.$username.' : ' . $amount . ' ' . $gnl->cur_text;
                $trx->save();  

                if ($posUser->position == 1) {
                    $posUserExtra->strong_leg = 'kiri';
                    $posUserExtra->monoleg_left = $strong;
                    $posUserExtra->save();        
                }else {
                    $posUserExtra->strong_leg = 'kanan';
                    $posUserExtra->monoleg_right = $strong;
                    $posUserExtra->save();
                }
            }
            $id = $posid;
        } else {
            break;
        }
    }

}

function  leaderComCounter($id)
{
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 75000;
    $count = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank == 0) {
                $id = $refid;
                continue;
            }
            if($user->rank == $userRef->rank && ($user->rank != 0 || $user->rank) != 1 && $user->id != $from){
                $id = $refid;
                continue;
            }
            $amount = $userRef->ranks->leader_bonus;
            $com = $com - $userRef->ranks->leader_bonus;
            if(($com - $userRef->ranks->leader_bonus) <= 0){
                $amount += $com;
            }
            
            if ($userRef->plan_id != 0 && $amount != 0) {
                $count++;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }

    return $count;
}

function  leaderCommission($id, $qty)
{

    // return true;
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 75000;
    $count = 0;
    $last = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank == 0 || $userRef->rank == 1) {
                $id = $refid;
                continue;
            }
            if($user->rank >= $userRef->rank && ($user->rank != 0 || $user->rank != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1) {
                $amount = $userRef->ranks->leader_bonus;
                $com = $com - $userRef->ranks->leader_bonus;
            }else{
                if (($userRef->rank - $user->rank) > 1) {
                    $selisih = Rank::find($last);
                    $amount = $userRef->ranks->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->ranks->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = Rank::find($last);
                    if($selisih->id >= $userRef->rank){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->ranks->leader_bonus - $user->ranks->leader_bonus;
                        $com = $com - ($userRef->ranks->leader_bonus - $user->ranks->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {
                // $userRef->balance += $amount * $qty;
                $userRef->b_balance += ($amount * $qty);
                $userRef->save();
                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->b_balance);
                $trx->remark = 'leadership_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid Leadership Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}
function  leaderCommissionFounder($id, $qty)
{

    // return true;
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 50000;
    $count = 0;
    $last = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank_founder == 0 || $userRef->rank_founder == 1) {
                $id = $refid;
                continue;
            }
            if($user->rank_founder >= $userRef->rank_founder && ($user->rank_founder != 0 || $user->rank_founder != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1) {
                $amount = $userRef->rankfounder->leader_bonus;
                $com = $com - $userRef->rankfounder->leader_bonus;
            }else{
                if (($userRef->rank_founder - $user->rank_founder) > 1) {
                    $selisih = RankFounder::find($last);
                    $amount = $userRef->rankfounder->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->rankfounder->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = RankFounder::find($last);
                    if($selisih->id >= $userRef->rank_founder){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus;
                        $com = $com - ($userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {

                $userRef->balance += ($amount * $qty);
                $userRef->save();

                if ($userRef->rank_founder == 2) {
                    $details = 'Privilege';
                }elseif($userRef->rank_founder == 3){
                    $details = 'Founder';
                }elseif($userRef->rank_founder == 4){
                    $details = 'Founder Nasional';
                }else{
                    $details = 'leadership_funder';
                }

                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->balance);
                $trx->remark = 'founder_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid '.$userRef->rankfounder->name.' Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank_founder;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}

function  leaderCommission2($id, $qty)
{
    // return true;
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 75000;
    $userfrom = user::find($id);
    $first = false;
    $last = 0;
    if ($userfrom->rank != 0 && $userfrom->rank != 1) {
            $amount = $userfrom->ranks->leader_bonus;
            $com = $com - $userfrom->ranks->leader_bonus;
            
            if ($userfrom->plan_id != 0 && $amount > 0) {
                // $userfrom->balance += $amount * $qty;

                $userfrom->b_balance += ($amount * $qty) ;
                $userfrom->save();

                $trx = new Transaction();
                $trx->user_id = $userfrom->id;
                $trx->amount = ($amount * $qty) ;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userfrom->b_balance);
                $trx->remark = 'leadership_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid Leadership Commission  ' . ($amount * $qty) . ' ' . $gnl->cur_text;
                $trx->save();  
                $first = true;

                $last = $userfrom->rank;
            }
    }

    $count = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank == 0 || $userRef->rank == 1) {
                $id = $refid;
                continue;
            }
            if($user->rank >= $userRef->rank && ($user->rank != 0 || $user->rank != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1 && $first == false ) {
                $amount = $userRef->ranks->leader_bonus;
                $com = $com - $userRef->ranks->leader_bonus;
            }else{
                if (($userRef->rank - $user->rank) > 1) {
                    $selisih = Rank::find($last);
                    $amount = $userRef->ranks->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->ranks->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = Rank::find($last);
                    if($selisih->id >= $userRef->rank){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->ranks->leader_bonus - $user->ranks->leader_bonus;
                        $com = $com - ($userRef->ranks->leader_bonus - $user->ranks->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {
                // $userRef->balance += $amount * $qty;
                $userRef->b_balance += ($amount * $qty);
                $userRef->save();
                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->b_balance);
                $trx->remark = 'leadership_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid Leadership Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}
function  leaderCommission2Founder($id, $qty)
{
    // return true;
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 50000;
    $userfrom = user::find($id);
    $first = false;
    $last = 0;
    if ($userfrom->rank_founder != 0 && $userfrom->rank_founder != 1) {
            $amount = $userfrom->rankfounder->leader_bonus;
            $com = $com - $userfrom->rankfounder->leader_bonus;
            
            if ($userfrom->plan_id != 0 && $amount > 0) {
                if ($userfrom->rank_founder == 2) {
                    $details = 'Privilege';
                }elseif($userfrom->rank_founder == 3){
                    $details = 'Founder';
                }elseif($userfrom->rank_founder == 4){
                    $details = 'Founder Nasional';
                }else{
                    $details = 'leadership_funder';
                }

                // $userfrom->balance += $amount * $qty;

                $userfrom->balance += ($amount * $qty) ;
                $userfrom->save();

                $trx = new Transaction();
                $trx->user_id = $userfrom->id;
                $trx->amount = ($amount * $qty) ;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userfrom->balance);
                $trx->remark = 'leadership_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid '.$userfrom->rankfounder->name.' Commission  ' . ($amount * $qty) . ' ' . $gnl->cur_text;
                $trx->save();  
                $first = true;

                $last = $userfrom->rank_founder;
            }
    }

    $count = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank_founder == 0 || $userRef->rank_founder == 1 || $userRef->rank_founder == null) {
                $id = $refid;
                continue;
            }
            if($user->rank_founder >= $userRef->rank_founder && ($user->rank_founder != 0 || $user->rank_founder != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1 && $first == false ) {
                $amount = $userRef->rankfounder->leader_bonus;
                $com = $com - $userRef->rankfounder->leader_bonus;
            }else{
                if (($userRef->rank_founder - $user->rank_founder) > 1) {
                    $selisih = RankFounder::find($last);
                    $amount = $userRef->rankfounder->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->rankfounder->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = RankFounder::find($last);
                    if($selisih->id >= $userRef->rank_founder){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus;
                        $com = $com - ($userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {
                
                if ($userRef->rank_founder == 2) {
                    $details2 = 'Privilege';
                }elseif($userRef->rank_founder == 3){
                    $details2 = 'Founder';
                }elseif($userRef->rank_founder == 4){
                    $details2 = 'Founder Nasional';
                }else{
                    $details2 = 'leadership_funder';
                }

                $userRef->balance += ($amount * $qty);
                $userRef->save();
                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->balance);
                $trx->remark = 'founder_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid '.$userRef->rankfounder->name.' Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank_founder;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}
function  leaderCommission2RO($id, $qty)
{
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 75000;
    $count = 0;
    $last = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank == 0 || $userRef->rank == 1) {
                $id = $refid;
                continue;
            }
            if($user->rank >= $userRef->rank && ($user->rank != 0 || $user->rank != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1) {
                $amount = $userRef->ranks->leader_bonus;
                $com = $com - $userRef->ranks->leader_bonus;
            }else{
                if (($userRef->rank - $user->rank) > 1) {
                    $selisih = Rank::find($last);
                    $amount = $userRef->ranks->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->ranks->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = Rank::find($last);
                    if($selisih->id >= $userRef->rank){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->ranks->leader_bonus - $user->ranks->leader_bonus;
                        $com = $com - ($userRef->ranks->leader_bonus - $user->ranks->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {
                // $userRef->balance += $amount * $qty;
                $userRef->b_balance += ($amount * $qty);
                $userRef->save();
                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->b_balance);
                $trx->remark = 'leadership_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid Leadership Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}
function  leaderCommission2ROFounder($id, $qty)
{
    $from = $id;
    $gnl = GeneralSetting::first();
    $com = 50000;
    $count = 0;
    $last = 0;
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $refid = getRefId($id);
            $user = user::find($id);
            $userRef = user::find($refid);
            if ($refid == "0") {
                break;
            }
            if ($userRef->rank_founder == 0 || $userRef->rank_founder == 1) {
                $id = $refid;
                continue;
            }
            if($user->rank_founder >= $userRef->rank_founder && ($user->rank_founder != 0 || $user->rank_founder != 1) && $user->id != $from){
                $id = $refid;
                continue;
            }
            $count++;
            if ($count == 1) {
                $amount = $userRef->rankfounder->leader_bonus;
                $com = $com - $userRef->rankfounder->leader_bonus;
            }else{
                if (($userRef->rank_founder - $user->rank_founder) > 1) {
                    $selisih = RankFounder::find($last);
                    $amount = $userRef->rankfounder->leader_bonus - $selisih->leader_bonus;
                    $com = $com - ($userRef->rankfounder->leader_bonus - $selisih->leader_bonus);
                }else{
                    $selisih = RankFounder::find($last);
                    if($selisih->id >= $userRef->rank_founder){
                        $id = $refid;
                        continue;
                    }else{
                        $amount = $userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus;
                        $com = $com - ($userRef->rankfounder->leader_bonus - $user->rankfounder->leader_bonus);
                    }
                }
            }
            
            if ($userRef->plan_id != 0 && $amount > 0) {
                // $userRef->balance += $amount * $qty;
                $userRef->balance += ($amount * $qty);
                $userRef->save();
                $trx = new Transaction();
                $trx->user_id = $userRef->id;
                $trx->amount = $amount * $qty;
                $trx->charge = 0;
                $trx->trx_type = '+';
                $trx->post_balance = getAmount($userRef->balance);
                $trx->remark = 'founder_com';
                $trx->trx = getTrx();
                $trx->details = 'Paid '.$userRef->rankfounder->name.' Commission  ' . $amount * $qty . ' ' . $gnl->cur_text;
                $trx->save();  

                $last = $userRef->rank_founder;
            }
            
            if ($com <= 0){
                break;
            }
            $id = $refid;
                
        } else {
            break;
        }
    }
}

function findBottomLeg($nodeId)
{
    $bottomLeg = [];
    $visited = [];

    // Mencari kaki terbawah secara rekursif
    $findBottomLegRecursive = function ($nodeId) use (&$bottomLeg, &$visited, &$findBottomLegRecursive) {
        $visited[] = $nodeId;
        $children = user::where('pos_id', $nodeId)->get();

        // Jika node tidak memiliki anak, itu adalah kaki terbawah
        if ($children->isEmpty()) {
            $bottomLeg[] = $nodeId;
        } else {
            foreach ($children as $child) {
                if (!in_array($child->id, $visited)) {
                    $findBottomLegRecursive($child->id);
                }
            }
        }
    };

    $findBottomLegRecursive($nodeId);

    return $bottomLeg;
}
function getQual($sid,$posisi)
{
    $fromUser = User::find($sid);
    $get = getPosition($sid, $posisi);
    $id = $get['pos_id'];
    $ids = null;
    $count = 1; 
        while ($id != "" || $id != "0") {
            if (isUserExists($id)) {
                $posid = getPositionId($id);
                    if ($posid == "0") {
                        break;
                    }
                    $user = user::where('id',$id)->first();
                    $posUser = UserExtra::where('user_id',$id)->first();;
                    if ($posUser->is_gold == 1){
                        // $count++;
                        $ids = $posUser->user_id;
                        break;
                    }
                    $id = $posid;
            } else {
                break;
            }
    }
    return $ids ;

}

function countingQ($sid,$posisi)
{
    $fromUser = User::find($sid);
    $get = getPosition($sid, $posisi);
    $id = $get['pos_id'];
    $count = 0; 
        while ($id != "" || $id != "0") {
            if (isUserExists($id)) {
                $posid = getPositionId($id);
                    if ($posid == "0") {
                        break;
                    }
                    $user = user::where('id',$id)->first();
                    $posUser = UserExtra::where('user_id',$id)->first();;
                    if ($posUser->is_gold == 1){
                        $count++;
                    }
                    $id = $posid;
            } else {
                break;
            }
    }
    return $count <= 0 ? 1 : $count ;

}

function monolegBonus($id,$bonus, $strong_text,$strong)
{
    $fromUser = User::find($id);
    $count = 0; 
    $gnl = GeneralSetting::first();
    $user = UserExtra::find($id);

    $payment = User::find($id);
    $payment->balance += $bonus;
    $payment->save();

    $trx = new Transaction();
    $trx->user_id = $payment->id;
    $trx->amount = $bonus;
    $trx->charge = 0;
    $trx->trx_type = '+';
    $trx->post_balance = $payment->balance;
    $trx->remark = 'monoleg_commission';
    $trx->trx = getTrx();
    $trx->details = 'Paid Monoleg Commission ' . $bonus . ' ' . $gnl->cur_text;
    $trx->save();  

    if($strong_text == 'kiri'){
        $user->strong_leg = $strong_text;
        $user->monoleg_left = $strong;
        $user->save();
    }else{
        $user->strong_leg = $strong_text;
        $user->monoleg_right = $strong;
        $user->save();
    }

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getRefId($id);
                if ($posid == "0") {
                    break;
                }
                $users = user::where('id',$id)->first();
                $posUser = UserExtra::find($posid);
                if ($posUser->is_gold == 1){
                    $payment = User::find($posid);
                    $payment->balance += $bonus;
                    $payment->save();

                    $trx = new Transaction();
                    $trx->user_id = $payment->id;
                    $trx->amount = $bonus;
                    $trx->charge = 0;
                    $trx->trx_type = '+';
                    $trx->post_balance = $payment->balance;
                    $trx->remark = 'monoleg_commission';
                    $trx->trx = getTrx();
                    $trx->details = 'Paid Monoleg Commission ' . $bonus . ' ' . $gnl->cur_text;
                    $trx->save();  

                    if($strong_text == 'kiri'){
                        $posUser->strong_leg = $strong_text;
                        $posUser->monoleg_left = $strong;
                        $posUser->save();
                    }else{
                        $posUser->strong_leg = $strong_text;
                        $posUser->monoleg_right = $strong;
                        $posUser->save();
                    }

                }
                $id = $posid;
        } else {
            break;
        }
    }
}

function userRefaDay($id){
    $ref = Transaction::where('remark', 'referral_commission')
    ->whereDate('created_at', now()->toDateString())
    ->where('user_id', $id)
    ->count();

    return $ref ?? 0;
}

function updatePaidCount3($id, $count)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                // $extra->free_left -= 1;
                    $extra->paid_right -= $count;
                    $extra->right -= $count;
                    $extra->p_right -= $count;
                $extra->paid_left += $count;
                $extra->left += $count;
                $extra->p_left += $count;
            } else {
                // $extra->free_right -= $count;
                    $extra->paid_left -= $count;
                    $extra->left -= $count;
                    $extra->p_left -= $count;
                $extra->paid_right += $count;
                $extra->right += $count;
                $extra->p_right += $count;
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

}


function updateBV($id, $bv, $details)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $posUser = User::find($posid);
            if ($posUser->plan_id != 0) {
                $position = getPositionLocation($id);
                $extra = UserExtra::where('user_id', $posid)->first();
                $bvlog = new BvLog();
                $bvlog->user_id = $posid;

                if ($position == 1) {
                    $extra->bv_left += $bv;
                    $bvlog->position = '1';
                } else {
                    $extra->bv_right += $bv;
                    $bvlog->position = '2';
                }
                $extra->save();
                $bvlog->amount = $bv;
                $bvlog->trx_type = '+';
                $bvlog->details = $details;
                $bvlog->save();
            }
            $id = $posid;
        } else {
            break;
        }
    }

}

function monolegTree($sid, $pin , $posisi)
{
    return true;

    $fromUser = User::find($sid);
    $gnl = GeneralSetting::first();
    $uexs = UserExtra::where('user_id',$fromUser->id)->first();
    // $strong_n1 = $uexs->left > $uexs->right ? 1 : 2;
    $get = getPosition($sid, $posisi);
    // if ($uexs->is_gold != 1) {
    //     $id = $sid;
    // }else{
    //     if ($sid == $get['pos_id']) {
    //         # code...
    //         $id = $get['pos_id'];
    //     }else{
    //         $id = $sid;
    //     }
    // }
    $fromUser2 = getQual($get['pos_id'],$posisi);
    $id = $fromUser2;

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            // $refid = getRefId($id);
            $refid = getPositionId($id);
            if ($refid == "0") {
                break;
            }
            $user = $fromUser2;
            $user2 = $fromUser2;
            $uex = UserExtra::where('user_id',$fromUser->id)->first();
                
                if ($uex->rank == 1 ) {
                    $bonus = (($pin)*5000)/(countingQ($user,$posisi)) ;
                }elseif ($uex->rank == 2){
                    $bonus = (($pin)*10000)/(countingQ($user,$posisi)) ;
                }elseif ($uex->rank == 3 ){
                    $bonus = (($pin)*15000)/(countingQ($user,$posisi)) ;
                }elseif ($uex->rank == 4 ){
                    $bonus = (($pin)*20000)/(countingQ($user,$posisi)) ;
                }


                $posUser = User::find($id);
                $posUserExtra = UserExtra::where('user_id',$id)->first();
                if ($posUserExtra->is_gold == 1) {
                    # code...
                    $flushOut = '';
                    if ($bonus > 2500000) {
                        $flushOut = '(Flush Out)';
                        $bonus = 2500000;
                    }                           

                    $payment = User::find($posUserExtra->user_id);
                    $payment->balance += $bonus;
                    $payment->save();

                    $trx = new Transaction();
                    $trx->user_id = $payment->id;
                    $trx->amount = $bonus;
                    $trx->charge = 0;
                    $trx->trx_type = '+';
                    $trx->post_balance = $payment->balance;
                    $trx->remark = 'monoleg_commission';
                    $trx->trx = getTrx();
                    $trx->details = $flushOut.' Paid Monoleg Commission : ' . $bonus . ' ' . $gnl->cur_text;
                    $trx->save();

                }

            $id = $refid;
        } else {
            break;
        }
    }

}


function treeComission($id, $amount, $details)
{
    $fromUser = User::find($id);

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }

            $posUser = User::find($posid);
            if ($posUser->plan_id != 0) {

                $posUser->balance  += $amount;
                $posUser->total_binary_com += $amount;
                $posUser->save();

               $posUser->transactions()->create([
                    'amount' => $amount,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => $details,
                    'remark' => 'binary_commission',
                    'trx' => getTrx(),
                    'post_balance' => getAmount($posUser->balance),
                ]);


            }
            $id = $posid;
        } else {
            break;
        }
    }

}

function treeFilter($id, $upid)
{
    $fromUser = User::find($id);

    if($id==$upid){
        return true;
    }

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }

            $posUser = User::find($posid);
            if ($posid == $upid) {
                return true;
                break;
            }
            $id = $posid;
        } else {
            break;
        }
    }
    return false;

}

function referralCommission($user_id, $details)
{

    $user = User::find($user_id);
    $refer = User::find($user->ref_id);
    if ($refer) {
        $plan = Plan::find($refer->plan_id);
        if ($plan) {
            $amount = $plan->ref_com;
            $refer->balance += $amount;
            $refer->total_ref_com += $amount;
            $refer->save();

            $trx = $refer->transactions()->create([
                'amount' => $amount,
                'charge' => 0,
                'trx_type' => '+',
                'details' => $details,
                'remark' => 'referral_commission',
                'trx' => getTrx(),
                'post_balance' => getAmount($refer->balance),

            ]);

            $gnl = GeneralSetting::first();

            notify($refer, 'referral_commission', [
                'trx' => $trx->trx,
                'amount' => getAmount($amount),
                'currency' => $gnl->cur_text,
                'username' => $user->username,
                'post_balance' => getAmount($refer->balance),
            ]);

        }

    }


}

function referralCommission2($user_id, $details)
{

    $user = User::find($user_id);
    $refer = User::find($user->ref_id);
    if ($refer) {
        $plan = Plan::find($refer->plan_id);
        if ($plan) {
            $uex = UserExtra::where('user_id',$refer->id)->first();
            if ($uex->is_gold == 1) {
                $amount = $plan->ref_com;
            }else{
                $amount = $plan->ref_com;
            }

            $refer->b_balance += $amount;
            $refer->total_ref_com += $amount;
            $refer->save();

            $trx = $refer->transactions()->create([
                'amount' => $amount,
                'charge' => 0,
                'trx_type' => '+',
                'details' => $details,
                'remark' => 'referral_commission',
                'trx' => getTrx(),
                'post_balance' => getAmount($refer->b_balance),

            ]);

            $gnl = GeneralSetting::first();

            notify($refer, 'referral_commission', [
                'trx' => $trx->trx,
                'amount' => getAmount($amount),
                'currency' => $gnl->cur_text,
                'username' => $user->username,
                'post_balance' => getAmount($refer->b_balance),
            ]);

        }

    }


}

/*
===============TREEE===============
*/

function getPositionUser($id, $position)
{
    return User::where('pos_id', $id)->where('position', $position)->first();
}

function showTreePage($id)
{
    $res = array_fill_keys(array('b', 'c', 'd', 'e', 'f', 'g'), null);
    $user = User::with('ranks')->find($id);
    $res['left'] = ['user'=>getPositionUser($id, 1),'upline'=>'2','pos'=>1];
    $res['right'] =['user'=>getPositionUser($id, 2),'upline'=>'2','pos'=>1];

    $res['a'] = ['user'=>$user,'upline'=>null,'pos'=>$user->position];

    $res['b'] = ['user'=>getPositionUser($id, 1),'upline'=>$res['a']['user'],'pos'=>1];
    if ($res['b']['user']) {
        $res['d'] = ['user'=>getPositionUser($res['b']['user']->id, 1),'upline'=>$res['b']['user'],'pos'=>1];
        $res['e'] = ['user'=>getPositionUser($res['b']['user']->id, 2),'upline'=>$res['b']['user'],'pos'=>2];
    }else{
        $res['d'] = ['user'=>null,'upline'=>null,'pos'=>1];
        $res['e'] = ['user'=>null,'upline'=>null,'pos'=>2];
    }
    // if ($res['d']) {
    //     $res['h'] = getPositionUser($res['d']->id, 1);
    //     $res['i'] = getPositionUser($res['d']->id, 2);
    // }
    // if ($res['e']) {
    //     $res['j'] = getPositionUser($res['e']->id, 1);
    //     $res['k'] = getPositionUser($res['e']->id, 2);
    // }
    $res['c'] = ['user'=>getPositionUser($id, 2),'upline'=>$res['a']['user'],'pos'=>2];
    if ($res['c']['user']) {
        $res['f'] = ['user'=>getPositionUser($res['c']['user']->id, 1),'upline'=>$res['c']['user'],'pos'=>1];
        $res['g'] = ['user'=>getPositionUser($res['c']['user']->id, 2),'upline'=>$res['c']['user'],'pos'=>2];
    }else{
        $res['f'] = ['user'=>null,'upline'=>null,'pos'=>1];
        $res['g'] = ['user'=>null,'upline'=>null,'pos'=>2];
    }
    // if ($res['f']) {
    //     $res['l'] = getPositionUser($res['f']->id, 1);
    //     $res['m'] = getPositionUser($res['f']->id, 2);
    // }
    // if ($res['g']) {
    //     $res['n'] = getPositionUser($res['g']->id, 1);
    //     $res['o'] = getPositionUser($res['g']->id, 2);
    // }
    // // $res['o'] = getPositionUser($id, 2);
    // if ($res['o']) {
    //     $res['ad'] = getPositionUser($res['o']->id, 1);
    //     $res['ae'] = getPositionUser($res['o']->id, 2);
    // }
    // if ($res['n']) {
    //     $res['ab'] = getPositionUser($res['n']->id, 1);
    //     $res['ac'] = getPositionUser($res['n']->id, 2);
    // }
    // if ($res['m']) {
    //     $res['z'] = getPositionUser($res['m']->id, 1);
    //     $res['aa'] = getPositionUser($res['m']->id, 2);
    // }
    // if ($res['l']) {
    //     $res['x'] = getPositionUser($res['l']->id, 1);
    //     $res['y'] = getPositionUser($res['l']->id, 2);
    // }
    // if ($res['k']) {
    //     $res['v'] = getPositionUser($res['k']->id, 1);
    //     $res['w'] = getPositionUser($res['k']->id, 2);
    // }
    // if ($res['j']) {
    //     $res['t'] = getPositionUser($res['j']->id, 1);
    //     $res['u'] = getPositionUser($res['j']->id, 2);
    // }
    // if ($res['i']) {
    //     $res['r'] = getPositionUser($res['i']->id, 1);
    //     $res['s'] = getPositionUser($res['i']->id, 2);
    // }
    // if ($res['h']) {
    //     $res['p'] = getPositionUser($res['h']->id, 1);
    //     $res['q'] = getPositionUser($res['h']->id, 2);
    // }
    return $res;
}

function inTreeUser($id){
    $res = [];
    $user = User::find($id);
    // $res[] = $user1;
    if($user){
        $res[] = $user;
        $user3 = getPositionUser($user->id,1);
        
        if($user3){
            $res[] = $user3;
            // dd($user3);
            $user4 = getPositionUser($user3->id,1);
            if($user4){
                $res[] = $user4;
                $usera = getPositionUser($user4->id,1);
                if($usera){
                    $res[] = $usera;

                }
                $userb = getPositionUser($user4->id,2);
                if($userb){
                    $res[] = $userb;

                }
            }
            $user5 = getPositionUser($user3->id,2);
            if($user5){
                $res[] = $user5;
                $userc = getPositionUser($user5->id,1);
                if($userc){
                    $res[] = $userc;

                }
                $userd = getPositionUser($user5->id,2);
                if($userd){
                    $res[] = $userd;

                }
            }
        }
        
        $user6 = getPositionUser($user->id,2);
        if($user6){
            $res[] = $user6;
            $user7 = getPositionUser($user6->id,1);
            if($user7){
                $res[] = $user7;
                $useraa = getPositionUser($user7->id,1);
                if($useraa){
                    $res[] = $useraa;
                }
                $userbb = getPositionUser($user7->id,1);
                if($userbb){
                    $res[] = $userbb;
                }
            }
            $user8 = getPositionUser($user7->id,2);
            if($user8){
                $res[] = $user8;
                $usercc = getPositionUser($user7->id,1);
                if($usercc){
                    $res[] = $usercc;
                }
                $userdd = getPositionUser($user7->id,1);
                if($userdd){
                    $res[] = $userdd;
                }
            }
        }
    }
    return $res;
}

function showSingleUserinTree($resp)
{
    // dd($resp);
    $res = '';
    $user = $resp['user'];
    if(auth()->user()->userExtra->is_gold){
        $disable = '';
    }else{
        $disable = 'disabled';
    }
    $uplines = $resp['upline'];
    if($uplines){
        $upline = $uplines->username;
        $bro =   $uplines->username;
        $uname = $uplines->username;
    }else{
        $bro = '';
        $upline = '';
        $uname = '';
    }
    $pos = $resp['pos'];
    if ($user) {
        if($user->userExtra->is_gold){
           
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';
        }
        $countDownline = User::where('pos_id',$resp['user']->id)->count();

        if($countDownline <= 1){
            $classSee = 'btnCantSeeUser';

        }else{
            $classSee = 'btnSeeUser';
        }


        // $img = getImage('assets/images/user/profile/'. $user->image, null, true);
        $img = getImage($user->ranks->logo);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }
        $hisTree = route('user.other.tree', $user->username);



        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user  $classSee\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';
        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
        if($upline){
             
            if ($upline == auth()->user()->no_bro && auth()->user()->userExtra->is_gold || $pos == 2) {
                $img = getImage('assets/images/add2.jpg', null, true);

                # code...
                $addList = 'btnUser';
            }else{
                 $img = getImage('assets/images/add2.jpg', null, true);
                

                $addList = 'btnUser';
            }
           
        }else{
            $img = getImage('assets/images/add2.jpg', null, true);
            
            $addList = 'noUser';
        }

        // $img = getImage('assets/images/add2.jpg', null, true);
        // $img = getImage('assets/images/bg.png', null, true);
        // $addList = 'noUser';
        $res .= '<div class="user '.$addList.' " data-upline="'.$uname.'" data-pos="'.$pos.'" data-up="'.$uname.'" type="button">';
        // $res .= '<div class="user btnUser" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser'.$pos.$bro.'" style="border-radius:50%">';


    }
    $res .= " </div>";
    $res .= " <span class=\"line\" ></span>";
    return $res;

}
function showSingleUserNoLine($resp)
{
    $res = '';
    $user = $resp['user'];
    $uplines = $resp['upline'];
    if($uplines){
        $upline = $uplines->username;
        $uname = $uplines->username;
    }else{
        $upline = '';
        $uname = '';

    }
    $pos = $resp['pos'];
    if ($user) {
        // if ($user->plan_id == 0) {
        //     $userType = "free-user";
        //     $stShow = "Free";
        //     $planName = '';
        // } else {
        //     $userType = "paid-user";
        //     $stShow = "Paid";
        //     $planName = $user->plan->name;
        // }
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        $img = getImage($user->ranks->logo, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }

            $hisTree = route('user.other.tree', $user->username);


        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user btnSeeUser\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
            if ($uname != '') {
                $img = getImage('assets/images/add2.jpg', null, true);
                # code...
                $addList = 'btnUser';
            }else{
                 $img = getImage('assets/images/bg.png', null, true);
                # code...
                $addList = 'btnUser';
            }
        // $img = getImage('assets/images/bg.png', null, true);
        //         # code...
        //         $addList = 'noUser';
        $res .= '<div class="user '.$addList.' " data-upline="'.$uname.'" data-pos="'.$pos.'" data-up="'.$uname.'" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser'.$pos.$upline.'" style="border-radius:50%">';


    }
    $res .= " </div>";
    // $res .= " <span class=\"line\" ></span>";
    $res .= "<div class='mb-5'></div>";
    return $res;
}
function showSingleUserNoLineInsideLeft($resp)
{
$res = '';
    
    $user = $resp['user'];
    $uplines = $resp['upline'];

    // checkSebelah
    
    if($uplines){
        $upline = $uplines->username;
        $uname = $uplines->username;
        
        $upuser = User::where('username',$uplines->username)->first();
        $checkLeft = User::where('pos_id',$upuser->id)->where('position',1)->first();

    }else{
        $upline = '';
        $uname = '';
        $checkLeft = false;

    }
    $pos = $resp['pos'];
    if ($user) {
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        $img = getImage($user->ranks->logo, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }

            $hisTree = route('user.other.tree', $user->username);


        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user btnSeeUser\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-boldshowSingleUserNoLineInside textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
            if ($uname != '' &&  $checkLeft) {
                $img = getImage('assets/images/add2.jpg', null, true);
                # code...
                $addList = 'btnUser';
            }else{
                 $img = getImage('assets/images/bg.png', null, true);
                # code...
                $addList = 'noUser';
            }
        $res .= '<div class="user '.$addList.' " data-upline="'.$uname.'" data-pos="'.$pos.'" data-up="'.$uname.'" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser'.$pos.$upline.'" style="border-radius:50%">';


    }
    $res .= " </div>";
    // $res .= " <span class=\"line\" ></span>";
    $res .= "<div class='mb-5'></div>";
    return $res;
}
function showSingleUserNoLineInsideRight($resp)
{
$res = '';
    $user = $resp['user'];
    $uplines = $resp['upline'];
   
    // dd($upuser);

    if($uplines){
        $upline = $uplines->username;
        $uname = $uplines->username;
        $upuser = User::where('username',$uplines->username)->first();
        $checRight = User::where('pos_id',$upuser->id)->where('position',2)->first();
    }else{
        $upline = '';
        $uname = '';
        $checRight  = false;

    }
    $pos = $resp['pos'];
    if ($user) {
        // if ($user->plan_id == 0) {
        //     $userType = "free-user";
        //     $stShow = "Free";
        //     $planName = '';
        // } else {
        //     $userType = "paid-user";
        //     $stShow = "Paid";
        //     $planName = $user->plan->name;
        // }
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        $img = getImage($user->ranks->logo, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }

            $hisTree = route('user.other.tree', $user->username);


        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user btnSeeUser\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-boldshowSingleUserNoLineInside textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
            if ($uname != '' &&  $checRight) {
                $img = getImage('assets/images/add2.jpg', null, true);
                $addList = 'btnUser';

            }else{
                 $img = getImage('assets/images/bg.png', null, true);
                # code...
                $addList = 'noUser';
            }
        $res .= '<div class="user '.$addList.' " data-upline="'.$uname.'" data-pos="'.$pos.'" data-up="'.$uname.'" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser'.$pos.$upline.'" style="border-radius:50%">';


    }
    $res .= " </div>";
    // $res .= " <span class=\"line\" ></span>";
    $res .= "<div class='mb-5'></div>";
    return $res;
}

function showLastUserLeft()
{
    $pos = getPosition(auth()->user()->id,1);

    $res = '';
    $user = User::find($pos['pos_id']);
    if ($user) {
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }

            $hisTree = route('user.other.tree', $user->username);


        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user btnSeeUser\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
        
        // $img = getImage('assets/images/', null, true);
        $img = getImage('assets/images/bg.png', null, true);

        $addList = 'noUser';
        $res .= '<div class="user '.$addList.' " type="button">';
        // $res .= '<div class="user btnUser" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser">';


    }
    $res .= " </div>";
    // $res .= " <span class=\"line\" ></span>";
    $res .= "<div class='mb-5'></div>";
    return $res;

}
function showLastUserRight()
{
    $pos = getPosition(auth()->user()->id,2);

    $res = '';
    $user = User::find($pos['pos_id']);
    if ($user) {
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            // $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            // $hisTree = route('user.other.tree', $user->username);
        }

            $hisTree = route('user.other.tree', $user->username);


        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-username=\"$user->username\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-email=\"$user->email\"";
        $extraData .= " data-mobile=\"$user->mobile\"";
        $extraData .= " data-bro=\"$user->no_bro\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-posby=\"$posby\"";
        $extraData .= " data-is_stockiest=\"$is_stockiest\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user btnSeeUser\" data-username=\"$user->username\" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test $bg showDetails \" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name font-weight-bold\">$user->username</p>";
            // $res .= "<p class=\"user-name \"><small>$user->no_bro</small></p>";
            $res .= '<p class="user-name font-weight-bold textInf">'.$user->userExtra->left.' | '.$user->userExtra->right.'</p>';

        }
        // $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\" style=\"position: absolute; z-index:-1;\">Explore Tree</a></p>";

    } else {
        $img = getImage('assets/images/', null, true);
        $addList = 'noUser';
        $res .= '<div class="user '.$addList.' " type="button">';
        // $res .= '<div class="user btnUser" type="button">';
        $res .= '<img src="'.$img.'" alt="*"  class="no-user imgUser">';


    }
    $res .= " </div>";
    // $res .= " <span class=\"line\" ></span>";
    $res .= "<div class='mb-5'></div>";
    return $res;

}


function showSingleUserinTree2($user,$id)
{
    $res = '';
    if ($user) {
        if ($user->plan_id == 0) {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
        } else {
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = $user->plan->name;
        }

        if ($user->id == $id) {
            $userType = "active-user";
            $stShow = "Free";
            $planName = '';
            $fs ="font-weight: 700;font-size:18px;
            color: #070707;";
        } else {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
            $fs="";
        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        if(Auth::user()->pos_id == $user->id){
            $res .= "<div class=\"user showDetails select_tree\" $extraData>";
        }else{
        $res .= "<div class=\"user showDetails select_tree\" onclick='f1(\"$user->id\")' type=\"button\" $extraData>";
        }
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
        $res .= "<p class=\"user-name\" style=\"$fs\"> $user->no_bro</p>";
        if(Auth::user()->pos_id == $user->id){

        }elseif(Auth::user()->id == $user->id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#4cbe04;color:black;\"'>Leader (You)</a> </p>";
            if (Auth::user()->id == $id) {
                $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")'>Selected Parent</a> </p>";
            }
        }elseif($user->id == $id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")'>Selected Parent</a> </p>";
        }
        else{
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" onclick='f1(\"$user->id\")'>Explore Tree</a> </p>";
        }

    } else {

        $img = getImage('assets/images/user/profile/', null, true);
        $res .= "<div class=\"user\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">MP</p>";
        // $users = user::where('pos_id',$id)->first();
        // if($users){

        // }else{
        //     $res .= "<p class=\"user-name\"><a class=\"btn btn-sm\" style=\"background-color:#47bc52;\" href='posisi'>Select Position</a></p>";
        // }
    }

    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";

    return $res;

}
function showSingleUserinTree2Update($user,$id)
{
    $res = '';
    if ($user) {
        // if ($user->plan_id == 0) {
        //     $userType = "free-user";
        //     $stShow = "Free";
        //     $planName = '';
        // } else {
        //     $userType = "paid-user";
        //     $stShow = "Paid";
        //     $planName = $user->plan->name;
        // }
        if($user->userExtra->is_gold){
            $bg = 'bg-gold';
            $disable = '';
        }else{
            $bg = 'bg-pink';
            $disable = 'disabled';
        }

        if ($user->id == $id) {
            $userType = "active-user";
            $stShow = "Free";
            $planName = '';
            $fs ="font-weight: 700;font-size:18px;
            color: #070707;";
            $bg = 'bg-pink';


        } else {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
            $fs="";
            $bg = 'bg-pink';

        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        if(Auth::user()->pos_id == $user->id){
            $res .= "<div class=\"user showDetails select_tree \" $extraData>";
        }else{
        $res .= "<div class=\"user showDetails select_tree\" onclick='f1(\"$user->id\")' type=\"button\" $extraData>";
        }
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $bg\">";
        $res .= "<p class=\"user-name\" style=\"font-size: 12px;font-weight: bold;\" style=\"$fs\"> $user->username</p>";
        if(Auth::user()->pos_id == $user->id){

        }elseif(Auth::user()->id == $user->id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#4cbe04;color:black;\"'>Leader (You)</a> </p>";
            if (Auth::user()->id == $id) {
                $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm selectedParent\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")' data-parent=\"$user->id\" data-bro\"$user->no_bro\">Selected Parent</a> </p>";
            }
        }elseif($user->id == $id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm selectedParent\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")' data-parent=\"$user->id\" data-bro\"$user->no_bro\">Selected Parent</a> </p>";
        }

    } else {
        $img = getImage('assets/images/user/profile/', null, true);

        $res .= "<div class=\"user\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">MP</p>";
        // $users = user::where('pos_id',$id)->first();
        // if($users){

        // }else{
        //     $res .= "<p class=\"user-name\"><a class=\"btn btn-sm\" style=\"background-color:#47bc52;\" href='posisi'>Select Position</a></p>";
        // }
    }

    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";

    return $res;

}

function showSingleUserinTree2NoLine($user,$id)
{
    $res = '';
    if ($user) {
        // if ($user->plan_id == 0) {
        //     $userType = "free-user";
        //     $stShow = "Free";
        //     $planName = '';
        // } else {
        //     $userType = "paid-user";
        //     $stShow = "Paid";
        //     $planName = $user->plan->name;
        // }
        if($user->userExtra->is_gold){
            $bg = 'bg-gold';
        }else{
            $bg = 'bg-pink';
        }

        if ($user->id == $id) {
            $userType = "active-user";
            $stShow = "Free";
            $planName = '';
            $fs ="font-weight: 700;font-size:18px;
            color: #070707;";
            $bg = 'bg-pink';


        } else {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
            $fs="";
            $bg = 'bg-pink';

        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        if(Auth::user()->pos_id == $user->id){
            $res .= "<div class=\"user showDetails select_tree \" $extraData>";
        }else{
        $res .= "<div class=\"user showDetails select_tree\" onclick='f1(\"$user->id\")' type=\"button\" $extraData>";
        }
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $bg\">";
        $res .= "<p class=\"user-name\" style=\"font-size: 12px;font-weight: bold;\" style=\"$fs\"> $user->username</p>";
        // $res .= "<p class=\"user-name mt-n3\" style=\"font-size: 15px\" style=\"$fs\"> $user->username</p>";
        if(Auth::user()->pos_id == $user->id){

        }elseif(Auth::user()->id == $user->id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#4cbe04;color:black;\"'>Leader (You)</a> </p>";
            if (Auth::user()->id == $id) {
                $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm selectedParent\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")' data-parent=\"$user->id\" data-bro\"$user->no_bro\">Selected Parent</a> </p>";
            }
        }elseif($user->id == $id){
            $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm selectedParent\" style=\"background-color:#fb00e5;color:black;\" onclick='f1(\"$user->id\")' data-parent=\"$user->id\" data-bro\"$user->no_bro\">Selected Parent</a> </p>";
        }
        else{
            // $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" onclick='f1(\"$user->id\")'>Explore Tree</a> </p>";
            
        }

    } else {
        $img = getImage('assets/images/user/profile/', null, true);

        $res .= "<div class=\"user\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">MP</p>";
        // $users = user::where('pos_id',$id)->first();
        // if($users){

        // }else{
        //     $res .= "<p class=\"user-name\"><a class=\"btn btn-sm\" style=\"background-color:#47bc52;\" href='posisi'>Select Position</a></p>";
        // }
    }

    $res .= " </div>";
    // $res .= " <span class=\"line\"></span>";
    $res .= "<div class='mb-5'></div>";

    return $res;

}

function showSingleUserinTree3($users,$pos_id,$id)
{
    // dd($users);
    $res = '';
    $user = $users['user'];
    $uplines = $users['upline'];
    if($uplines){
        $upline = $uplines->no_bro;
    }else{
        $upline = '';
    }
    // $pos = $user['pos'];
    if ($user) {
         if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        if ($user->id == $id) {
            $userType = "select-user";
            $stShow = "Free";
            $planName = '';
            $fs ="font-weight: 700;font-size:18px;
            color: #070707;";
        } else {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
            $fs="";
        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        if(Auth::user()->pos_id == $user->id){
            $res .= '<div class="user" '.$extraData.'>';
        }else{
            $res .= '<div class="user" '.$extraData.'>';
        }
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $bg\">";
        $res .= "<p class=\"user-name\" style=\"$fs\"> $user->username</p>";
        // if(Auth::user()->pos_id == $user->id){

        // }elseif(Auth::user()->id == $user->id){
        //     $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#4cbe04;color:black;\"'>Leader (You)</a> </p>";
        //     if (Auth::user()->id == $id) {
        //         $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#47bc52;color:black;\" onclick='f1(\"$user->id\")'>Selected Parent</a> </p>";
        //     }
        // }elseif($user->id == $id){
        //     $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#47bc52;color:black;\" onclick='f1(\"$user->id\")'>Selected Position</a> </p>";
        // }
        // else{
        //     // $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" onclick='f1(\"$user->id\")'>Explore Tree</a> </p>";
        // }

    } else {
        $img = getImage('assets/images/user/profile/', null, true);

        $res .= "<div class=\"user\" >";
        $res .= '<img src="'.$img.'" alt="*"  class="no-user">';
        $res .= "<p class=\"user-name\">MP</p>";
        // $users = user::where('pos_id',$id)->first();
        // if($users){

        // }else{
        //     $res .= "<p class=\"user-name\"><a class=\"btn btn-sm\" style=\"background-color:#47bc52;\" href='posisi'>Select Position</a></p>";
        // }
    }

    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";

    return $res;

}
function showSingleUserinTree3NoLine($users,$pos_id,$id)
{
    // dd($users);
    $res = '';
    $user = $users['user'];
    $uplines = $users['upline'];
    if($uplines){
        $upline = $uplines->no_bro;
    }else{
        $upline = '';
    }
    // $pos = $user['pos'];
    if ($user) {
        // if ($user->plan_id == 0) {
        //     $userType = "free-user";
        //     $stShow = "Free";
        //     $planName = '';
        // } else {
        //     $userType = "paid-user";
        //     $stShow = "Paid";
        //     $planName = $user->plan->name;
        // }
        if($user->userExtra->is_gold){
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-gold';
        }else{
            $userType = "free-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
            $bg = 'bg-pink';

        }

        if ($user->id == $id) {
            $userType = "select-user";
            $stShow = "Free";
            $planName = '';
            $fs ="font-weight: 700;font-size:18px;
            color: #070707;";
        } else {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
            $fs="";
        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-id=\"$user->id\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        if(Auth::user()->pos_id == $user->id){
            $res .= "<div class=\"user\" $extraData>";
        }else{
        $res .= "<div class=\"user\"  $extraData>";
        }
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $bg\">";
        $res .= "<p class=\"user-name\" style=\"$fs\"> $user->username</p>";
        // if(Auth::user()->pos_id == $user->id){

        // }elseif(Auth::user()->id == $user->id){
        //     $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#4cbe04;color:black;\"'>Leader (You)</a> </p>";
        //     if (Auth::user()->id == $id) {
        //         $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#47bc52;color:black;\" onclick='f1(\"$user->id\")'>Selected Parent</a> </p>";
        //     }
        // }elseif($user->id == $id){
        //     $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#47bc52;color:black;\" onclick='f1(\"$user->id\")'>Selected Position</a> </p>";
        // }
        // else{
        //     // $res .= "<p class=\"user-name\" ><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" onclick='f1(\"$user->id\")'>Explore Tree</a> </p>";
        // }

    } else {
        $img = getImage('assets/images/user/profile/', null, true);

        $res .= "<div class=\"user\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">MP</p>";
        // $users = user::where('pos_id',$id)->first();
        // if($users){

        // }else{
        //     $res .= "<p class=\"user-name\"><a class=\"btn btn-sm\" style=\"background-color:#47bc52;\" href='posisi'>Select Position</a></p>";
        // }
    }

    $res .= " </div>";
    $res .= "<div class='mb-5'></div>";


    return $res;

}


/*
===============TREE AUTH==============
*/
function treeAuth($whichID, $whoID){

    if($whichID==$whoID){
        return true;
    }
    $formid = $whichID;
    while($whichID!=""||$whichID!="0"){
        if(isUserExists($whichID)){
            $posid = getPositionId($whichID);
            if($posid=="0"){
                break;
            }
            $position = getPositionLocation($whichID);
            if($posid==$whoID){
                return true;
            }
            $whichID = $posid;
        } else {
            break;
        }
    }
    return 0;
}

function displayRating($val)
{
    $result = '';
    for($i=0; $i<intval($val); $i++){
        $result .= '<i class="la la-star text--warning"></i>';
    }
    if(fmod($val, 1)==0.5){
        $i++;
        $result .='<i class="las la-star-half-alt text--warning"></i>';
    }
    for($k=0; $k<5-$i ; $k++){
        $result .= '<i class="lar la-star text--warning"></i>';
    }
    return $result;
}
function randomNumber($length) {
    $result = '';
    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}

function generateUniqueNoBro()
    {
        do {
            $now = Carbon::now();
            $year = $now->format('y');
            $month = $now->month;
            $last = rand(1000, 9999);
            $code = 'BRO-MP'.$year.$month.$last;
        } while (user::where("no_bro", "=", $code)->first());
        return $code;
    }

    function tree_created($username){
        $user = User::where('username',$username)->first();
        $tree = showTreePage($user->pos_id);
        // $cek_awal = User::where('pos_id',$user->id)->first();
        // $cek_awal_kiri = User::where('pos_id',$user->id)->where('position',1)->first();
        // $cek_awal_kanan = User::where('pos_id',$user->id)->where('position',2)->first();

        $response_tree ="
        <h4 class='row text-center justify-content-center'>Preview position selected of user ".$user->username." </h4>
        <div class='row text-center justify-content-center llll'>
        <!-- <div class='col'> -->
        <div class='w-1'>
            ".showSingleUserinTree3($tree['a'],$user->pos_id,$user->id)."
        </div>
        </div>
        <div class='row text-center justify-content-center llll'>
            <!-- <div class='col'> -->
            <div class='w-2 pleft'>
                ".showSingleUserinTree3($tree['b'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-2 pright'>
                ".showSingleUserinTree3($tree['c'],$user->pos_id,$user->id)."
            </div>
        </div>
        <div class='row text-center justify-content-center'>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3NoLine($tree['d'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3NoLine($tree['e'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3NoLine($tree['f'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3NoLine($tree['g'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->

        </div>
       
        ";
         echo $response_tree;
    }

function nb($number){
    return number_format($number,0,',','.');
}
function num($number){
    return number_format($number,0,'.',',');
}
function nbk($number){
    return number_format($number,3,',','.');
}

function brodev($user_id, $bro_qty){

    $user = user::where('id',$user_id)->first();

    $brod = new brodev();
    $brod->trx = getTrx();
    $brod->user_id = $user_id;
    $brod->bro_qty = $bro_qty;
    $brod->alamat = $user->address->address .', '. $user->address->city.', '. $user->address->state;
    $brod->status = 2;
    $brod->save();

    // $s = $user->address->address .', '. $user->address->city.', '. $user->address->state;

    // dd($s);
}

function cekReward($id){
    $ure = ureward::where('user_id',Auth::user()->id)->where('reward_id',$id)->first();
    if ($ure) {
        return true;
    }
    return false;
}

function userRegiteredChart(){
    $lastFiveMonths = collect([]);

        // Mengambil data 5 bulan terakhir
    for ($i = 0; $i < 12; $i++) {
        $lastFiveMonths->push(now()->subMonths($i)->format('Y-m'));
    }

    // Query untuk mendapatkan jumlah pengguna terdaftar dalam 5 bulan terakhir
    $registrations = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") AS month'), DB::raw('COUNT(*) as total'))
        ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $lastFiveMonths)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

    $response = [
        'month' => [],
        'total' => []
    ];

    foreach ($registrations as $registration) {
        $month = \Carbon\Carbon::parse($registration->month);
        $monthYear = $month->translatedFormat('F Y');
        $response['month'][] = $monthYear;
        $response['total'][] = $registration->total;
    }

    return $response;
}

function omsetThisMonth(){
    $currentMonth = Carbon::now()->format('m');
    $currentYear = Carbon::now()->format('Y');
    $omset = Transaction::whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->where('remark', 'purchased_plan')
        ->orWhere('remark', 'repeat_order')
        ->whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->sum('amount');

    return $omset;
}
function omsetLastMonth(){
    $startDate = Carbon::now()->subMonth()->startOfMonth();
    $endDate = Carbon::now()->subMonth()->endOfMonth();

    // Mendapatkan bulan lalu
    $currentMonth = $startDate->format('m');
    $currentYear = $startDate->format('Y');

    // Mendapatkan omset dari bulan lalu
    $omset = Transaction::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('remark', 'purchased_plan')
                ->orWhere('remark', 'repeat_order')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('amount');

    return $omset;
}

function registerThisMount()
{
    $currentMonth = Carbon::now()->format('m');
    $currentYear = Carbon::now()->format('Y');

    $startDate = Carbon::now()->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();

    $dates = [];
    $totals = [];

    // Initialize dates array
    for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
        $dates[] = $date->format('d');
    }

    // Get user registrations count for each date
    $userRegistrations = Transaction::whereYear('created_at', $currentYear)
        ->where('remark','purchased_plan')
        ->whereMonth('created_at', $currentMonth)
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->groupBy('date')
        ->get();

    // Initialize totals array
    foreach ($dates as $date) {
        $totals[$date] = 0;
    }

    // Fill totals array with user registration counts
    foreach ($userRegistrations as $userRegistration) {
        $date = Carbon::parse($userRegistration->date)->format('d');
        $totals[$date] = $userRegistration->total;
    }
    $month_date = addMonthNames($dates);
    $response = [
        'month' => $month_date,
        'total' => array_values($totals)
    ];

   return $response;
}


function memberGrowId($id){
    $mm = User::find($id);
    $startDate = Carbon::now()->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();
    $user = [];
    $dates = [];
    $totals = [];

    // Initialize dates array
    for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
        $dates[] = $date->format('d');
    }
       

    $dates = [];
    $totals = [];

    // Initialize dates array
    for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
        $dates[] = $date->format('d');
    }
    $mm2 = MemberGrow::with(['user'])->where('user_id',$mm->id)
        ->selectRaw('DATE(created_at) as date, grow_l as total_left,grow_r as total_right')
        ->get();

    foreach ($dates as $date) {
        $totals[$date] = 0;
    }

    foreach ($mm2 as $userRegistration) {
        $date = Carbon::parse($userRegistration->date)->format('d');
        $totals[$date] = $userRegistration->total_left;
    }
    $user[] = [
        'name' => 'left',
        'data' => array_values($totals)
    ];
    foreach ($mm2 as $userRegistration) {
        $date = Carbon::parse($userRegistration->date)->format('d');
        $totals[$date] =  $userRegistration->total_right;
    }
    $user[] = [
        'name' => 'right',
        'data' => array_values($totals)
    ];
    $month_date = addMonthNames($dates);
    $response = [
        'date' => $month_date,
        'series' => $user
    ];
    return $response;
}



function addMonthNames($array) {
    $result = array();

    foreach ($array as $value) {
        $month = date('F'); // Get the current month
        $result[] = $value . ' ' . $month;
    }

    return $result;
}

function sumPurchasedPlan()
{
    $totalAmount = DB::table('transactions')
        ->where('remark', 'purchased_plan')
        ->sum('amount');

    return $totalAmount;
}
function sumRefComm()
{
    $totalAmount = DB::table('transactions')
        ->where('remark', 'referral_commission')
        ->sum('amount');
    return $totalAmount;
}

function totalWdGold(){
    $totalAmount = DB::table('transactions')
        ->where('details', 'LIKE', '%Withdrawl Gold%')
        ->get();
        $id  = [];
    foreach($totalAmount as $t){
        $id[] += $t->user_id; 
    }
    $user = User::join('user_extras','users.id','=','user_extras.user_id')
        ->where('firstname','!=','ptmmi')
        ->where('firstname','!=','masterplan')
        ->where('user_extras.is_gold',1)
        // ->whereNotIn('users.id',$id)
        ->get();
    $total = $user->count();
    // dd($total);
    $totalAmount = $total * goldToday();
    return $totalAmount;
}


function totalMpProd(){
    $userCount = User::whereNotNull('no_bro')->whereNotBetween('id', [16, 213])->count();

    return $userCount * 15000;
    
}
function totalBinnaryCom(){
    return  User::sum('total_binary_com');
}

function totalGlobalPayout(){
    $hp = rewardHp();
    $ref = sumRefComm();
    $dailyGold = totalWdGold();
    $tarikEmas = tarik_emas();
    $bin = totalBinnaryCom();
    $shring = sharingProfit();

    $total = $hp + $ref + $dailyGold + $tarikEmas + $bin + $shring;

    return $total;

}
function adminLeaderSellPin($inputDate){
    $results= DB::table('user_pin as p')
        ->select(DB::raw('CASE 
                            WHEN p.pin_by IS NULL THEN "admin"
                            ELSE u.username
                        END as username'), 
                DB::raw('SUM(p.pin) as total_pin'),
                DB::raw('CONCAT(CASE MONTH(p.created_at)
                                WHEN 1 THEN "January"
                                WHEN 2 THEN "February"
                                WHEN 3 THEN "March"
                                WHEN 4 THEN "April"
                                WHEN 5 THEN "May"
                                WHEN 6 THEN "June"
                                WHEN 7 THEN "July"
                                WHEN 8 THEN "August"
                                WHEN 9 THEN "September"
                                WHEN 10 THEN "October"
                                WHEN 11 THEN "November"
                                WHEN 12 THEN "December"
                                ELSE ""
                              END," ",YEAR(p.created_at) ) as month_year')
                )
        ->leftJoin('users as u', 'u.id', '=', 'p.pin_by')
        ->where(function ($query) {
            $query->whereNull('p.pin_by');
                // ->orWhere('p.pin_by', 1)
                // ->orWhere('p.pin_by', 442);
        })
        ->where('p.type', '+')
        ->whereMonth('p.created_at', '=', date('m', strtotime($inputDate)))
        ->whereYear('p.created_at', '=', date('Y', strtotime($inputDate)))
        ->groupBy('username')
        ->get();

    return $results;
}
function totalColagenProd(){
    $userCount = User::whereNotNull('no_bro')->whereNotBetween('id', [16, 213])->count();

    return $userCount * 35000;
}

function addToLog($subject)
{
    $info = json_decode(json_encode(getIpInfo()), true);
    $log = [];
    $log['subject'] = $subject;
    $log['url'] = Request::fullUrl();
    $log['method'] = Request::method();
    $log['agent'] = Request::header('user-agent');
    $log['location'] = '';  //@implode(',', $info['city']) . (" - " . @implode(',', $info['area']) . "- ") . @implode(',', $info['country']) . (" - " . @implode(',', $info['code']) . " ");
    $log['user_id'] = auth()->check() ? auth()->user()->id : 1;
    LogActivity::create($log);
}
function chekWeeklyClaim($userId){
    $lastWeek = WeeklyGold::orderByDesc('id')->first();
    $lastWeekClaim = UserGold::where(['user_id'=>$userId,'type'=>'weekly'])->orderByDesc('id')->first();
    $claim = $lastWeekClaim->week??0;
    $lastWek = $lastWeek->week;
    // return $claim .'=='.$lastWek.'id='.$user->id;
    if( $lastWek == $claim){
        return false;
    }else{
        return true;
    }
}
function countGold(){
    $gold= UserExtra::join('users','user_extras.user_id','=','users.id')->where('user_extras.is_gold',1)->where('users.comp',0)->count('user_extras.id');
    $silver = UserExtra::join('users','user_extras.user_id','=','users.id')->where('user_extras.is_gold',0)->where('users.no_bro','!=','')->where('users.comp',0)->count('users.id');
    return [
        'gold' => $gold,
        'silver'=> $silver
    ];
}
function rewardHp(){
    $hp = ureward::where('reward_id',3)->count('id');
    return $hp * 600000;
}
function countAllBonus(){
    $binary_com =  auth()->user()->total_binary_com;
    $sharing_profit = Transaction::where('remark','porfit_sharing')->where('user_id',auth()->user()->id)->sum('amount');
    $last_ro = auth()->user()->userExtra->last_ro;
    return  ($binary_com + $sharing_profit) - $last_ro;
   
}
function oncreate(){
    $on_create = auth()->user()->created_at;
    $tanggalPembuatan = new DateTime($on_create);
    $hariIni = new DateTime();
    // dd()
    $selisih = $tanggalPembuatan->diff($hariIni)->days;
    
    if ($selisih >= 30) {
        return 100;
    } else {
        return round(($selisih / 30) * 100);
    }
}
function umurakun(){
    $on_create = auth()->user()->created_at;
    $tanggalPembuatan = new DateTime($on_create);
    $hariIni = new DateTime();
    // dd()
    return $tanggalPembuatan->diff($hariIni)->days;
}
function goldNum(){
    $code = Auth::user()->no_bro;
    return str_replace('M', 'SN', $code);
}





function getMonthlyPinTotals($userId=1)
{
    $startDate = Carbon::now()->startOfMonth()->toDateString();
    $endDate = Carbon::now()->endOfMonth()->toDateString();

    $data = UserPin::with('user')
        ->select('user_id', DB::raw('DATE(created_at) AS date'), DB::raw('SUM(pin) as pin'))
        ->where('user_id', 1)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('user_id', 'date')
        ->orderBy('date')
        ->get()
        ->toArray();
    $data2 = UserPin::with('user')
        ->select('user_id', DB::raw('DATE(created_at) AS date'), DB::raw('SUM(pin) as pin'))
        ->where('user_id', 442)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('user_id', 'date')
        ->orderBy('date')
        ->get()
        ->toArray();

    // Create an array with all dates in the current month
    $dates = [];
    $currentDate = Carbon::now()->startOfMonth();
    $lastDate = Carbon::now()->endOfMonth();

    while ($currentDate <= $lastDate) {
        $dates[] = $currentDate->format('d M');
        $currentDate->addDay();
    }

    // Create an array to hold the pin data for each user
    $pinData = [];

    // Iterate through the data and build the pin data array
    foreach ($data as $row) {
        $userId = $row['user']['id'];
        $username = $row['user']['username'];
        $pin = $row['pin'];
        $date = $row['date'];

        if (!isset($pinData[$userId])) {
            $pinData[$userId] = [
                'username' => $username,
                'data' => array_fill(0, count($dates), 0),
            ];
        }

        $dateIndex = array_search($date, $dates);
        $pinData[$userId]['data'][$dateIndex] = $pin;
    }
    foreach ($data2 as $row) {
        $userId = $row['user']['id'];
        $username = $row['user']['username'];
        $pin = $row['pin'];
        $date = $row['date'];

        if (!isset($pinData[$userId])) {
            $pinData[$userId] = [
                'username' => $username,
                'data' => array_fill(0, count($dates), 0),
            ];
        }

        $dateIndex = array_search($date, $dates);
        $pinData[$userId]['data'][$dateIndex] = $pin;
    }

    // Prepare the final result array
    $result = [
        'date' => $dates,
        'pin' => array_values($pinData),
    ];

    return $result;
}

function getWeekStartAndEndDates($year, $month)
{
 $weekDates = [];

    // Get the first day of the specified month
    $firstDayOfMonth = Carbon::create($year, $month, 1);

    // Calculate the start and end dates for week 1 (days 1 to 7)
    $week1StartDate = $firstDayOfMonth->copy();
    $week1EndDate = $week1StartDate->copy()->addDays(6);

    $weekDates[1] = [
        'start' => $week1StartDate->format('Y-m-d'),
        'end' => $week1EndDate->format('Y-m-d')
    ];

    // Calculate the start and end dates for week 2 (days 8 to 15)
    $week2StartDate = $week1EndDate->copy()->addDay();
    $week2EndDate = $week2StartDate->copy()->addDays(7);

    $weekDates[2] = [
        'start' => $week2StartDate->format('Y-m-d'),
        'end' => $week2EndDate->format('Y-m-d')
    ];

    // Calculate the start and end dates for week 3 (days 16 to 23)
    $week3StartDate = $week2EndDate->copy()->addDay();
    $week3EndDate = $week3StartDate->copy()->addDays(7);

    $weekDates[3] = [
        'start' => $week3StartDate->format('Y-m-d'),
        'end' => $week3EndDate->format('Y-m-d')
    ];

    // Calculate the start and end dates for week 4 (days 24 to end of the month)
    $week4StartDate = $week3EndDate->copy()->addDay();
    $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
    $week4EndDate = $lastDayOfMonth->copy()->min($week4StartDate->copy()->endOfWeek());

    $weekDates[4] = [
        'start' => $week4StartDate->format('Y-m-d'),
        'end' => $lastDayOfMonth->format('Y-m-d'),
    ];

    return $weekDates;
}


function sumPinByWeek()
{
    // Get the current month and year
    $currentMonth = now()->format('m');
    $currentYear = now()->format('Y');

    // Retrieve the start and end dates for all four weeks
    $weekDates = getWeekStartAndEndDates($currentYear, $currentMonth);

    // Assign the variables for each week
    $week1Start = $weekDates[1]['start'];
    $week1End = $weekDates[1]['end'];
    $week2Start = $weekDates[2]['start'];
    $week2End = $weekDates[2]['end'];
    $week3Start = $weekDates[3]['start'];
    $week3End = $weekDates[3]['end'];
    $week4Start = $weekDates[4]['start'];
    $week4End = $weekDates[4]['end'];

    $week1 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',1)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week1Start,$week1End])
        ->first();
    $week2 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',1)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week2Start,$week2End])
        ->first();
    $week3 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',1)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week3Start,$week3End])
        ->first();
    $week4 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',1)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week4Start,$week4End])
        ->first();
    $w1r= (int)$week1->sum_pin ??0;
    $w2r= (int)$week2->sum_pin ??0;
    $w3r= (int)$week3->sum_pin ??0;
    $w4r= (int)$week4->sum_pin ??0;

    $totr = $w1r + $w2r + $w3r+ $w4r;
    $reborn = [
        'name' => 'Reborn',
        'week1' => $w1r,
        'week2' => $w2r,
        'week3' => $w3r,
        'week4' => $w4r,
        'total' => $totr
    ];
    $week11 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',442)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week1Start,$week1End])
        ->first();
    $week12 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',442)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week2Start,$week2End])
        ->first();
    $week13 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',442)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week3Start,$week3End])
        ->first();
    $week14 = DB::table('user_pin')
        ->select(DB::raw('SUM(pin) as sum_pin'))
        ->where('user_id',442)
        ->where('ket','like','%Admin Added%')
        ->whereBetween('created_at',[$week4Start,$week4End])
        ->first();
    $w1q = (int)$week11->sum_pin ??0;
    $w2q = (int)$week12->sum_pin ??0;
    $w3q = (int)$week13->sum_pin ??0;
    $w4q = (int)$week14->sum_pin ??0;
    $totq = $w1q + $w2q + $w3q + $w4q;
    // dd($totq);
    $queen = [
        'name' => 'Queen01',
        'week1' => $w1q,
        'week2' => $w2q,
        'week3' => $w3q,
        'week4' => $w4q,
        'total' => $totq
    ];
    // dd($queen);
    return [
        $reborn,
        $queen
    ];
}

function findWeek($w)
{
    $currentMonth = now()->format('m');
    $currentYear = now()->format('Y');

    // Retrieve the start and end dates for all four weeks
    $weekDates = getWeekStartAndEndDates($currentYear, $currentMonth);
    return '('. date('d',strtotime($weekDates[$w]['start'])) . ' - '. date('d',strtotime($weekDates[$w]['end'])) . ')';
}

function getMonthArray()
{
    $startMonth = Carbon::create(2023, 5, 1)->startOfMonth();
    $currentMonth = Carbon::now()->startOfMonth();

    $months = [];

    while ($startMonth <= $currentMonth) {
        $months[] = $startMonth->format('F Y');
        $startMonth->addMonth();
    }
    return $months;
}
function getSharingProvitUserByMonth($userID){
    
    $data = [];
    foreach (getMonthArray() as $i) {
        $date = Carbon::createFromFormat('F Y', $i)->startOfMonth();
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $trx = Transaction::where('user_id', $userID)
            ->where('remark', 'profit_sharing')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();
        if($trx){
            $data[] = $trx->amount;
        }else{
            $data[] = 0;
        }
    }
    return $data;
   
}
function goldDeliver(){
    $gold_del = UserExtra::where(['is_gold'=>1,'bonus_deliver'=>1])->count('id');
    $gold_un =  DB::table('user_extras')
                ->where('is_gold', 1)
                ->where(function ($query) {
                    $query->whereNull('bonus_deliver')
                        ->orWhere('bonus_deliver', 0);
                })
                ->count('id');
    return ['true'=>$gold_del,'false'=>$gold_un];
}
function SellingOmset(){
    $deliver = User::where('no_bro','!=','')->count();
    return $deliver * 330000;
}

function goldToday(){
    $gold = DailyGold::orderByDesc('id')->first();
    return $gold->per_gram;
}

function emas25(){
    //joinkan dengan users yg emas = 1
    $user = Auth::user();
    $rek= rekening::where('user_id',$user->id)->first();
    if (!$rek) {
       return false;
    }
    // $sameuser = rekening::selectRaw('COUNT(*) AS result')
    //                 ->join('users','rekenings.user_id','=','users.id')
    //                 ->where(['rekenings.nama_bank'=>$rek->nama_bank,'rekenings.nama_akun'=>$rek->nama_akun,'rekenings.no_rek'=>$rek->no_rek,'users.emas'=>1])
    //                 ->groupBy('user_id','username')
    //                 ->get();
    $sameuser = DB::table('rekenings as r')
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->select('u.username', 'r.user_id', DB::raw('MAX(r.nama_bank) as nama_bank'), DB::raw('MAX(r.nama_akun) as nama_akun'), DB::raw('MAX(r.no_rek) as no_rek'))
            ->where('nama_bank', $rek->nama_bank)
            ->where('nama_akun', $rek->nama_akun)
            ->where('no_rek', $rek->no_rek)
            ->where('u.emas','=',1)
            ->where('u.no_bro','!=','')
            ->groupBy('r.user_id', 'u.username')
            ->get();
// dd($sameuser);
    $countUser = $sameuser->count();
    $jml1Gr = 40;
    $jmlGrmEmas = (int)($countUser / $jml1Gr);
    $id = [];
    if($countUser >= 40){
        $sisaUser = $countUser % $jml1Gr;
        $includeUser = $jmlGrmEmas * $jml1Gr; 
        if($sisaUser != 0){
            // $sameuser2 = rekening::join('users','rekenings.user_id','=','users.id')->where(['rekenings.nama_bank'=>$rek->nama_bank,'rekenings.nama_akun'=>$rek->nama_akun,'rekenings.no_rek'=>$rek->no_rek,'users.emas'=>1])->where('rekenings.user_id','!=',$user->id)->take($includeUser)->get(); //where not user_id;
            //jika ada sisa user. maka emas user yg wd tidak termasuk
            $sameuser2 = DB::table('rekenings as r')
                ->join('users as u', 'r.user_id', '=', 'u.id')
                ->select('u.username', 'r.user_id', DB::raw('MAX(r.nama_bank) as nama_bank'), DB::raw('MAX(r.nama_akun) as nama_akun'), DB::raw('MAX(r.no_rek) as no_rek'))
                ->where('nama_bank', $rek->nama_bank)
                ->where('nama_akun', $rek->nama_akun)
                ->where('no_rek', $rek->no_rek)
                ->where('r.user_id','!=',$user->id)
                ->where('u.emas','=',1)
                ->where('u.no_bro','!=','')
                ->groupBy('r.user_id', 'u.username')
                ->take($includeUser)
                ->get();
            foreach ($sameuser2 as $key => $value) {
                // dd($value->user_id);
                $id[] += $value->user_id;
            }
            return ['userId'=>$id,'gold'=>$jmlGrmEmas,'same'=>$countUser,'totuser'=>$includeUser,'sisa'=>$sisaUser,'id'=>$user->id,'status'=>1];
        }else{
            foreach ($sameuser as $key => $value) {
                $id[] += $value->user_id;
            }
            return ['userId'=>$id,'gold'=>$jmlGrmEmas,'same'=>$countUser, 'totuser'=>$includeUser,'sisa'=>$sisaUser,'id'=>$user->id,'status'=>1];
        }
    }else{
        foreach ($sameuser as $key => $value) {
            $id[] += $value->user_id;
        }
        return ['userId'=>$id,'gold'=>0,'same'=>$countUser,'sisa'=>0,'totuser'=>$countUser,'minus' => 40-$countUser, 'id'=>$user->id,'status'=>0];
    }
}
function checkGems(){
    $rek = rekening::where('user_id',auth()->user()->id)->first();
    
    $checksame = rekening::where('nama_bank', $rek->nama_bank)
                ->where('nama_akun', $rek->nama_akun)
                ->where('no_rek', $rek->no_rek)
                ->get();
    $group = [];
    foreach ($checksame as $key => $value) {
        $group[] += $value->user_id; 
    }
    $gems = User::whereIn('id',$group)
            ->where('gems',1)
            ->get();
    $xgems = User::whereIn('id',$group)
            ->where('gems',1)
            ->get();
    if ($gems->count() >= 7) {
      return true;
    }elseif (auth()->user()->username=="masterplan16") {
       return true;
    }elseif (auth()->user()->gems_dlv) {
       return true;
    }else{
        return false;
    }
}
function checkxgems(){
    $rek = rekening::where('user_id',auth()->user()->id)->first();
    
    $checksame = rekening::where('nama_bank', $rek->nama_bank)
                ->where('nama_akun', $rek->nama_akun)
                ->where('no_rek', $rek->no_rek)
                ->get();
    $group = [];
    foreach ($checksame as $key => $value) {
        $group[] += $value->user_id; 
    }
    $gems = User::whereIn('id',$group)
            ->where('gems',1)
            ->get();
    $xgems = User::whereIn('id',$group)
            ->where('xgems',1)
            ->get();
    if(in_array($gems->count(), [7, 14, 21,28,35,42])) {
      return true;
    }elseif (auth()->user()->gems_dlv) {
       return true;
    }else{
        return false;
    }
}
function tarikGems(){
    $rek = rekening::where('user_id',auth()->user()->id)->first();
    
    $checksame = rekening::where('nama_bank', $rek->nama_bank)
                ->where('nama_akun', $rek->nama_akun)
                ->where('no_rek', $rek->no_rek)
                ->get();
    $group = [];
    $groupID = [];
    foreach ($checksame as $key => $value) {
        $group[] += $value->user_id; 
    }

    $gems = User::whereIn('id',$group)
            ->where('gems',1)
            ->get();
    foreach ($gems as $key => $id) {
        $groupID[] += $id->id; 
    }
    $count = $gems->count(); //7
    $hasil = floor($count / 7); //1
    $totgems = $hasil * 7 * 350000; //
    $bonus = $totgems * 42/100;
    $deliver = $totgems + $bonus;

    if(auth()->user()->gems_dlv){
        $deliver    = 350000 + (350000*0.42);
        $count      = 1;
    }else{
        $deliver    = $totgems + $bonus;
        $count      = $hasil*$count;
    }

    return ['gems' => $deliver,'id'=>$groupID,'count'=>$count];
}

function tarik_emas(){
    $emas = User::where('emas','=',1)->where('firstname','!=','ptmmi')->where('firstname','!=','masterplan')->get();
    $count = $emas->count() * 0.025;
    return goldToday() * $count;
}

function sharingProfit(){
    $chek = Transaction::where('remark','profit_sharing')
            ->sum('amount');
    return $chek;
}
function clearSymbol($phoneNumber){
    // Method 1: Using str_replace
    $cleanedPhoneNumber = str_replace(['+', ' ', '-'], '', $phoneNumber);

    // Method 2: Using regular expression
    $cleanedPhoneNumberRegex = preg_replace('/[\s\+\-]/', '', $phoneNumber);

    return $cleanedPhoneNumber;
}

function sendWa($msg){
    $apiEndpoint = 'https://wa.srv5.wapanels.com/send-message';
    $postData = [
        'api_key' => env('WA_API_KEY'), // isi api key di menu profile -> setting
        'sender' => env('WA_SENDER'), // isi no device yang telah di scan
        'number' => env('WA_NUMBER'), // isi no pengirim
        'message' => $msg // isi pesan
    ];
    $response = Http::post($apiEndpoint, $postData);
    $res = json_decode($response->body(),true);
    return $res;
}
function demoUrl(){
    $username = auth()->user()->username=='masterplan01'?'bozgems':auth()->user()->username;
    return url('login-ayam') . '?username=' .$username.'.ai';
}
function check100Days($created_at){
    $differenceInDays = Carbon::now()->diffInDays($created_at);
    // dd('diffDay'.$differenceInDays);
    // dd($differenceInDays);
    // Check if the difference is less than 100 days
    if ($differenceInDays <= 100) {
        return true;
    } else {
        return false;
    }
}

function check100Week($created_at){
    $createdAt = Carbon::parse($created_at);

    // Calculate the difference in weeks between created_at and current date
    $differenceInWeeks = Carbon::now()->diffInWeeks($createdAt);

    // Check if the difference is less than 100 weeks
    if ($differenceInWeeks <= 100) {
        return true;
    } else {
        return false;
    }
}
function check100Gold($user_id,$type){
    // dd(Auth::user());
    $checkDaily = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
        ->where('user_id', auth()->user()->id)
        ->where('type', $type)
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();
    // $checkDaily = UserGold::select( DB::raw('COUNT(*) as days'),DB::raw('SUM(golds) as gold'))->where(['user_id'=>$user_id,'type'=>$type])->groupBy('user_id')->first();
    if(!$checkDaily){
        return ['type'=>true,'day'=>1];
    }
    if($checkDaily->count() < 100){
        return ['type'=>true,'day'=>$checkDaily->count() + 1];
    }else{
        return ['type'=>false,'day'=>$checkDaily->count()];
    }
}
function check100GoldID($user_id,$type){
    // dd(Auth::user());
    $checkDaily = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
        ->where('user_id',$user_id)
        ->where('type', $type)
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();
    // $checkDaily = UserGold::select( DB::raw('COUNT(*) as days'),DB::raw('SUM(golds) as gold'))->where(['user_id'=>$user_id,'type'=>$type])->groupBy('user_id')->first();
    if(!$checkDaily){
        return ['type'=>true,'day'=>1];
    }
    if($checkDaily->count() < 100){
        return ['type'=>true,'day'=>$checkDaily->count() + 1];
    }else{
        return ['type'=>false,'day'=>$checkDaily->count()];
    }
}
function deliverDailyWeekly($user){
    $type = checkClaimDailyWeekly($user);
    if(!$type){
        return 0;
    }
    $dailyCheck = check100Gold($user->id,$type);
    $day = $dailyCheck['day'];
    UserGold::create([
        'user_id'   => $user->id,
        'day'       => $type=='daily'?$day:0,
        'golds'     => 0.005,
        'grams'     => 0,
        'type'      => $type,
        'week'      => $type=='weekly'?$day:0,
    ]);
    return 1;
}


function deliverDailyGold($user_id){
    $dailyCheck = check100Gold($user_id,'daily');
    if(!$dailyCheck['type']){
        return false;
    }
    $day = $dailyCheck['day'];
    UserGold::create([
        'user_id'   => $user_id,
        'day'       => $day,
        'golds'     => 0.005,
        'grams'     => 0,
        'type'      => 'daily',
        'week'      => 0
    ]);
}

function deliverWeeklyGold($user_id){
    $user = User::find($user_id);
    //check gold user;
    $dailyCheck = check100Gold($user->id,'weekly');
    if(!$dailyCheck['type']){
        return false;
    }
    //check umur account;
    $umur = check100Week($user->created_at);
    if(!$umur){
        return false;
    }
    $day = $dailyCheck['day'];
    UserGold::create([
        'user_id'   => $user->id,
        'day'       => $day,
        'golds'     => 0.005,
        'grams'     => 0,
        'type'      => 'weekly',
        'week'      => 0
    ]);
}
function typeClaimGold($user){
    if(check100Gold($user->created_at,'daily')['type'] && !Auth::user()->wd_gold){
        return 'daily';
    }
    if(check100Week($user->created_at)){   
        return 'weekly';
    }
}

function checkClaimDailyWeekly($users){
    // return false;
    $user = User::where('id',$users->id)->first();
    if(!$user) {
        return false;
    }
    if(!check100Gold($user->id,'daily')['type'] || $user->wd_gold){
        if(!check100Gold($user->id,'weekly')['type']){
            return false;
        }
        if(check100Week($user->created_at)){   
            // dd('weekly100');
            //    check last created weekly;
            $latesDaily = UserGold::where(['user_id'=>auth()->user()->id,'type'=>'daily'])->orderByDesc('created_at')->first();
            
            $checkLastCreate = UserGold::where(['user_id'=>$user->id,'type'=>'weekly'])
            ->where('created_at','>',$latesDaily->created_at)
            ->orderByDesc('id')->first();
        
            if ($checkLastCreate) {
                $created_at = date('Y-m-d', strtotime($checkLastCreate->created_at));
                // Convert the created_at string to a Carbon instance
                $createdAt = Carbon::parse($created_at);

                // Calculate the difference in weeks between created_at and current date
                $differenceInWeeks = Carbon::now()->diffInWeeks($createdAt);
                if ($differenceInWeeks >= 1) {
                    return 'weekly';
                }else{
                    return false;
                } 
            }else{
                return 'weekly';
            }
        }
    }else{
        $checkLastDaily = UserGold::where(['user_id' => $user->id, 'type' => 'daily'])->orderByDesc('id')->first();
        // dd($checkLastDaily);
        if ($checkLastDaily) {
            $created_at = date('Y-m-d', strtotime($checkLastDaily->created_at));
            // Convert the created_at string to a Carbon instance
            $createdAt = Carbon::parse($created_at);
            // Calculate the difference in days between created_at and current date
            $differenceInDays = Carbon::now()->diffInDays($createdAt);
            //  dd($differenceInDays);
            if ($differenceInDays >= 1) {
                return 'daily';
            } else {
                return false;
            }
        } else {
            return 'daily';
        }
    }
}

function checkWdGold($user){
    // dd(check100Week($user->created_at));
    if(!$user->userExtra->is_gold)
    {
        return false;
    }
    // dd
    if(!$user->wd_gold && !check100Gold($user->id,'daily')['type']){
        return 'daily';
    }
    if(!check100Gold($user->id,'weekly')['type']){
        return 'weekly';
    }
    return false;
    // dd(check100Days($user->created_at));
}


function todayGold(){
    $goldToday      = DailyGold::orderByDesc('id')->first(); //harga emas terakhir
    $goldSell       = $goldToday->per_gram - ($goldToday->per_gram*8/100); // harga_emas sekarang - harga_emas - 8%
    return  $goldSell;
}

function withdrawGold(){
    $user = auth()->user();
    $gold_user = $user->wd_gold == 0 ? userGold()['daily']:userGold()['weekly']; 

    $userGold       = $gold_user;
    $goldToday      = todayGold();
    $platfrom_fee_pr = 5/100; //palform_fee
    $harga_total    = $userGold * $goldToday;
    $platfrom_fee   = $harga_total * $platfrom_fee_pr;
    $totalWd        = $harga_total - $platfrom_fee;
    $notif          = checkWdGold($user)=='daily'?'100 Hari':'100 Minggu';
    return [
        'user_gold'         => $userGold,
        'gold_today'        => $goldToday,
        'platform_fee'      => $platfrom_fee,
        'harga_total'       => $harga_total,
        'total_wd'          => $totalWd,
        'type'              => checkWdGold($user),
        'notif'             => $notif
    ];
}
function userGoldSum($type){
    if ($type == 'weekly') {
        $latesDaily = UserGold::where(['user_id'=>auth()->user()->id,'type'=>'daily'])->orderByDesc('created_at')->first();
        if($latesDaily){
            $sql = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
                ->where('user_id', auth()->user()->id)
                ->where('type', $type)
                ->where('created_at','>',$latesDaily->created_at)
                ->groupBy('date')
                ->orderByDesc('date')
                ->get();
        }else{
             $sql = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
            ->where('user_id', auth()->user()->id)
            ->where('type', $type)
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
        }
    }else{
        $sql = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
        ->where('user_id', auth()->user()->id)
        ->where('type', $type)
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();
    }
    
        
    return $sql->count() * 0.005;
}

function userGold(){
    
    $typeGold = typeClaimGold(auth()->user());
    $user = auth()->user();
    $daily = userGoldSum('daily');
    $weekly = userGoldSum('weekly');
    if($daily > 0.5){
        $daily = 0.5;
    }
    if($weekly > 0.5){
        $weekly = 0.5;
    }
    if($user->wd_gold){
        $daily=0;
    }
    $total = $daily + $weekly;
    $equal = todayGold() * $total;
    return [
        'total' => $total,
        'daily' => $daily,
        'weekly'=> $weekly,
        'equal' => $equal
    ];
}
function userGoldSumID($userId,$type){
      $sql = UserGold::selectRaw('DATE(created_at) AS date, MAX(created_at) AS created_at')
        ->where('user_id', $userId)
        ->where('type', $type)
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();
        
    return $sql->count() * 0.005;
}
function userGoldID($userID){
    $user = User::find($userID);
    $daily = userGoldSumID($userID,'daily');
    $weekly = userGoldSumID($userID,'weekly');
    if($daily > 0.5){
        $daily = 0.5;
    }
    if($weekly > 0.5){
        $weekly = 0.5;
    }
    if($user->wd_gold){
        $daily=0;
    }
    $total = $daily + $weekly;
    $equal = todayGold() * $total;
    return [
        'total' => $total,
        'daily' => $daily,
        'weekly'=> $weekly,
        'equal' => $equal
    ];
}
function tanggal($date){
    Carbon::setLocale('id'); // Mengatur bahasa ke bahasa Indonesia

    // Memformat tanggal dengan nama hari dalam bahasa Indonesia
    $formattedDate = $date->isoFormat('dddd, DD/MM/YY');

    echo $formattedDate;
}
function minigold($key = 'total'){
    $count = User::where('emas','=',1)->where('firstname','!=','ptmmi')->where('firstname','!=','masterplan')->count();
    $data = [
        'count' => $count,
        'emas' => $count * 0.025,
        'equal' => ($count * 0.025) * todayGold()
    ];
    return $data[$key];
}
function promoSept($key=1){
    $result = DB::table('user_extras as x')
        ->join('users as u', 'x.user_id', '=', 'u.id')
        ->select(
            'x.user_id',
            'u.username',
            DB::raw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(mark_lf, "$.left")), 0) AS left_value'),
            DB::raw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(mark_lf, "$.right")), 0) AS right_value'),
            'x.left AS left_now',
            'x.right AS right_now',
            DB::raw('x.left - COALESCE(JSON_UNQUOTE(JSON_EXTRACT(mark_lf, "$.left")), 0) AS grow_left'),
            DB::raw('x.right - COALESCE(JSON_UNQUOTE(JSON_EXTRACT(mark_lf, "$.right")), 0) AS grow_right'),
            DB::raw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(mark_lf, "$.date")), u.created_at) AS date')
        )
        ->where('u.id', auth()->id())
        ->orderByDesc('grow_left')
        ->orderByDesc('grow_right')
        ->first();
    $givenDate = Carbon::parse($result->date);
    $currentDate = Carbon::now();
    
    $diff =  $currentDate->diffInDays($givenDate);
    if($diff < 30){
        $mark = 1;
    }elseif($diff >= 60 && $diff < 90){
        $mark = 2;
    }else{
        $mark = 3;
    }
    $return = [
        'left'  => nb($result->grow_left),
        'right' => nb($result->grow_right),
        'date'  => $result->date,
        'mark'  => $mark
    ];
    return $return[$key];
}

function updateCycleNasional($newRegisterUserID){
   $active = UserExtra::with('user')
    ->where('n_cyle', 0)
    ->whereHas('user', function ($query) {
        $query->where('plan_id', 1);
    })
    ->orderBy('id', 'asc')
    ->first();
    $no1 = $active->national_1;
    $no2 = $active->national_2;
    $no3 = $active->national_3;
    $no4 = $active->national_4;

    if($no1 == null){
        $active->national_1 = $newRegisterUserID;
        $active->save();
        return $active;
    }else if($no2 == null){
        $active->national_2 = $newRegisterUserID;
        $active->save();
        return $active;
    }else if($no3 == null){
        $active->national_3 = $newRegisterUserID;
        $active->save();
        return $active;
    }elseif($no4 == null) {
        $active->national_4 = $newRegisterUserID;
        $active->n_cyle = true;
        $active->save();
        return $active;
    }else{
        return $active;
    }
}

function sendCommision($userID, $amount, $details,$remark)
{
    $user  = User::find($userID);
    if($user){
        $user->balance  += $amount;
        $user->save();
        $user->transactions()->create([
                'amount' => $amount,
                'charge' => 0,
                'trx_type' => '+',
                'details' => $details,
                'remark' => $remark,
                'trx' => getTrx(),
                'post_balance' => getAmount($user->balance),
        ]);
    }
}

function cekOngkir($kodepos,$berat){
    
    $options = [
        'cache_wsdl'     => WSDL_CACHE_NONE,
        'trace'          => 1,
        'stream_context' => stream_context_create(
            [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]
        )
    ];
    $ongkir = 0;

    try {
    $wsdl="http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
    libxml_disable_entity_loader(false);
    $client = new \SoapClient($wsdl,$options);
    $username = env('USER_RPX');
    $password  = env('PASS_RPX');
    $format = 'json';

    $result = $client->getRatesPostalCode($username,$password,env('POSCODE_RPX'),$kodepos,'RGP',$berat/1000,'0',$format);
    $response = json_decode($result,true);
    if (!$response) {
        $notify[] = ['error', 'Invalid destination address'];
        return redirect()->back()->withNotify($notify);
    }else{
        $ongkir = (int)$response['RPX']['DATA']['PRICE'];
    }
        
    }
    catch ( \Exception $e ) {
        $notify[] = ['error', $e->getMessage()];
        return redirect()->back()->withNotify($notify);
    }

    return $ongkir;

}
function PoolLog($amount) {
    $currentDate = Carbon::now();

    // Mendefinisikan nama bulan secara manual dalam bahasa Indonesia
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    // Mendapatkan nama bulan berdasarkan nomor bulan saat ini
    $bulan = $namaBulan[$currentDate->month];

    $pool = new PoolLog();
    $pool->bulan = $bulan;
    $pool->tahun = $currentDate->year;
    $pool->amount = $amount;
    $pool->save();
}