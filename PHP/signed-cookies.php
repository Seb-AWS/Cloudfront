<?php
require 'aws-autoloader.php';

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;

$cloudFront = new Aws\CloudFront\CloudFrontClient([
    'profile' => 'default',
    'version' => '2014-11-06',
    'region' => 'us-east-2'
]);

// Set up parameter values for the resource
$resourceKey = 'https://<DOMAIN_NAME.COM>/<PATH>/*'; //Change domain name and URI
$expires = time() + 300;

$customPolicy = "{\"Statement\": [{\"Resource\": \"{$resourceKey}\",\"Condition\": {\"DateLessThan\": {\"AWS:EpochTime\": {$expires}}}}]}";


// Create a signed cookie for the resource using a custom policy
$signedCookieCustomPolicy = $cloudFront->getSignedCookie([
    'policy' => $customPolicy,
    'private_key' => '/var/www/keys/cf-groups-key.pem', //Path to private key for script to use
    'key_pair_id' => 'K1234ABCDEFG' //Key Pair ID
]);

if ($_POST['user'] == 'user' && $_POST['pass'] == 'pass' ) { //set a sample username and password (eg. 'user' &'pass')

foreach ($signedCookieCustomPolicy as $name => $value) {
    setcookie($name, $value, $expires, "/", "<DOMAIN_NAME.COM>"); //Change domain name
}

header ('location: https://<DOMAIN_NAME.COM>/<PATH>/<index>.m3u8'); //Change domain name and URI

} else {

header ('cache-control: no-cache, no-store');
?>
<form method="post">
  <fieldset>
    <p>
      <input type="text" name="user" id="size_2" value="username">
      <label for="size_2">User</label>
    </p>
    <p>
      <input type="password" name="pass" id="size_3" value="password">
      <label for="size_3">Password</label>
    </p>
<p> <button type="submit">Validate</button> </p>
  </fieldset>
</form>
<?php
}
?>
