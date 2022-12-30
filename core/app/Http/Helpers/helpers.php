<?php

use App\Models\brodev;
use App\Models\BvLog;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\SmsTemplate;
use App\Models\ureward;
use App\Models\User;
use App\Models\UserExtra;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
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
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
                $extra->right += 1;
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
                // $extra->free_left -= 1;
                $extra->paid_left += 1;
                $extra->left += 1;
            } else {
                // $extra->free_right -= 1;
                $extra->paid_right += 1;
                $extra->right += 1;
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

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
                $extra->paid_left += $count;
                $extra->left += $count;
            } else {
                // $extra->free_right -= $count;
                    $extra->paid_left -= $count;
                    $extra->left -= $count;
                $extra->paid_right += $count;
                $extra->right += $count;
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

function referralCommission2($user_id, $details,$qty)
{

    $user = User::find($user_id);
    $refer = User::find($user->ref_id);
    if ($refer) {
        $plan = Plan::find($refer->plan_id);
        if ($plan) {
            $uex = UserExtra::where('user_id',$refer->id)->first();
            if ($uex->left > 3 && $uex->right > 3 || $uex->is_gold == 1) {
                # code...
                $amount = 20000 * $qty;
            }else{
                $amount = 15000 * $qty;
            }

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

/*
===============TREEE===============
*/

function getPositionUser($id, $position)
{
    return User::where('pos_id', $id)->where('position', $position)->first();
}

function showTreePage($id)
{
    $res = array_fill_keys(array('b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o','p','q','r','s','t','u','v','w','x','y','z','aa','ab','ac','ad','ae'), null);
    $res['a'] = User::find($id);

    $res['b'] = getPositionUser($id, 1);
    if ($res['b']) {
        $res['d'] = getPositionUser($res['b']->id, 1);
        $res['e'] = getPositionUser($res['b']->id, 2);
    }
    if ($res['d']) {
        $res['h'] = getPositionUser($res['d']->id, 1);
        $res['i'] = getPositionUser($res['d']->id, 2);
    }
    if ($res['e']) {
        $res['j'] = getPositionUser($res['e']->id, 1);
        $res['k'] = getPositionUser($res['e']->id, 2);
    }
    $res['c'] = getPositionUser($id, 2);
    if ($res['c']) {
        $res['f'] = getPositionUser($res['c']->id, 1);
        $res['g'] = getPositionUser($res['c']->id, 2);
    }
    if ($res['f']) {
        $res['l'] = getPositionUser($res['f']->id, 1);
        $res['m'] = getPositionUser($res['f']->id, 2);
    }
    if ($res['g']) {
        $res['n'] = getPositionUser($res['g']->id, 1);
        $res['o'] = getPositionUser($res['g']->id, 2);
    }
    // $res['o'] = getPositionUser($id, 2);
    if ($res['o']) {
        $res['ad'] = getPositionUser($res['o']->id, 1);
        $res['ae'] = getPositionUser($res['o']->id, 2);
    }
    if ($res['n']) {
        $res['ab'] = getPositionUser($res['n']->id, 1);
        $res['ac'] = getPositionUser($res['n']->id, 2);
    }
    if ($res['m']) {
        $res['z'] = getPositionUser($res['m']->id, 1);
        $res['aa'] = getPositionUser($res['m']->id, 2);
    }
    if ($res['l']) {
        $res['x'] = getPositionUser($res['l']->id, 1);
        $res['y'] = getPositionUser($res['l']->id, 2);
    }
    if ($res['k']) {
        $res['v'] = getPositionUser($res['k']->id, 1);
        $res['w'] = getPositionUser($res['k']->id, 2);
    }
    if ($res['j']) {
        $res['t'] = getPositionUser($res['j']->id, 1);
        $res['u'] = getPositionUser($res['j']->id, 2);
    }
    if ($res['i']) {
        $res['r'] = getPositionUser($res['i']->id, 1);
        $res['s'] = getPositionUser($res['i']->id, 2);
    }
    if ($res['h']) {
        $res['p'] = getPositionUser($res['h']->id, 1);
        $res['q'] = getPositionUser($res['h']->id, 2);
    }
    return $res;
}


function showSingleUserinTree($user)
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
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;
        }else{
             $userType = "free-user";
             $stShow = "Paid";
            $planName = '';
            $test = $user->userExtra->is_gold;

        }

        $img = getImage('assets/images/user/profile/'. $user->image, null, true);

        $refby = getUserById($user->ref_id)->fullname ?? '';
        $posby = getUserById($user->pos_id)->username ?? '';
        $is_stockiest = $user->is_stockiest;
        $extraData = " data-name=\"$user->fullname\"";
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
            $loginTree = route('admin.users.login',$user->id);
            $detailTree = route('admin.users.detail',$user->id);
            $extraData .= " data-treeloginurl=\"$loginTree\"";
            $extraData .= " data-treedetailurl=\"$detailTree\"";
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }



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

        $res .= "<div class=\"user \" type=\"button\" >";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType $test  showDetails\" $extraData>";
        
        if (auth()->guard('admin')->user()) {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name\">$user->username</p>";
        } else {
            // if(auth()->user()->userExtra->is_gold){
            //     $res .= "<span class=\"badge badge-warning mt-n3\">$user->username</span>";
            // }else{
            //     $res .= "<span class=\"badge badge-light\">$user->username</span>";
            // }
            $res .= "<p class=\"user-name\">$user->username</p>";
        }
        $res .= "<p class=\" user-btn\" style=\"padding-top:0px;\"><a class=\"btn btn-sm\" style=\"background-color:#63bbf3;color:black;\" href=\"$hisTree\">Explore Tree</a></p>";

    } else {
        $img = getImage('assets/images/user/profile/', null, true);

        $res .= "<div class=\"user\" type=\"button\">";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">MP</p>";
    }

    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";

    return $res;

}
function showSingleUserinTree2($user,$id)
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
        $res .= "<p class=\"user-name\" style=\"font-size: 12px;font-weight: bold;\" style=\"$fs\"> $user->no_bro</p>";
        $res .= "<p class=\"user-name mt-n3\" style=\"font-size: 15px\" style=\"$fs\"> $user->username</p>";
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
function showSingleUserinTree3($user,$pos_id,$id)
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
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
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
    $res .= " <span class=\"line\"></span>";

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
            // $prefix = substr($now->year, -2) . $now->format('m');
            // $last = user::latest('no_bro')->first();
            // $lastNoBro = (int) substr($last?->no_bro ?: 0, -4);
            // $incr = str_pad($lastNoBro + 1, 4, '0', STR_PAD_LEFT);
            // $code = $prefix . $incr;
            $year = $now->format('y');
            $month = $now->month;
            $last = rand(1000, 9999);
            $code = 'M2929'.$year.$month.$last;
        } while (user::where("no_bro", "=", $code)->first());
        return $code;
    }

    function tree_created($email){
        $user = User::where('email',$email)->first();
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
                ".showSingleUserinTree3($tree['d'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3($tree['e'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3($tree['f'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-4 '>
                ".showSingleUserinTree3($tree['g'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->

        </div>
        <div class='row text-center justify-content-center llll'>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['h'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['i'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['j'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['k'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['l'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['m'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['n'],$user->pos_id,$user->id)."
            </div>
            <!-- <div class='col'> -->
            <div class='w-8'>
                ".showSingleUserinTree3($tree['o'],$user->pos_id,$user->id)."
            </div>


        </div>
        ";
         echo $response_tree;
    }

function nb($number){
    return number_format($number,0,',','.');
}
function nbk($number){
    return number_format($number,2,',','.');
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
        # code...
        return true;
    }
    return false;
}
