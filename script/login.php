<?
/* Display Errors */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Include Page */
require_once('init.php');

// VIG Login
$formkey = '2SuIxXlV21tHW0QA';
$username = 'Brian@3pxusa.com';
$password = '3pxusa1688';
$url = 'http://www.vigfurniture.com/customer/account/loginPost/';
$requestURL = 'http://www.vigfurniture.com/aa615-165-white-room-divider.html';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'login[username]='.$username.'&login[password]='.$password);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
$result = curl_exec($ch);
echo $result;
curl_setopt($ch, CURLOPT_URL, $requestURL);
$content = curl_exec($ch);
echo $content;
curl_close($ch);
?>
