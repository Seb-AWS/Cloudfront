<?php

require '/Users/psb/vendor/autoload.php';

$cloudFront = new Aws\CloudFront\CloudFrontClient([
    'region'  => 'eu-west-1',
    'version' => '2014-11-06'
]);

$hostUrl = 'https://mydomain.com';  // Remove the brackets (<>) and replace with your domain.
$resourceKey = '*';            // Specify the folder/* or folder/123* etc.
$expires = time() + 60*60;             // Set the expiration (Currently set to 1 hour from now).

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++BEGIN CLOUDFRONT SIGNED URL CANNED POLICY METHOD+++++++++++++++++++++
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
print "=========================================================================";
print "\n";
// Create a signed URL for the resource using a canend policy
// The Canned policy will only allow access to a specific object.
print "SIGNED URL FOR THE RESOURCE USING CANNED POLICY:";
print "\n \n";

print $signedUrlCannedPolicy = $cloudFront->getSignedUrl([
    'url'         => $hostUrl.'/index.html',    // Specifying that specific object.
    'expires'     => $expires,                  // Setting the Expiration of the URL
    'private_key' => '/PATH/TO/CLOUDFRONT/KEY/pk-APKAXXXXXXXXXXKEYID.pem', // Specifying the Key Pair to use generated in the Security Credentials page using Root Acc.
    'key_pair_id' => 'APKAXXXXXXXXXXKEYID' // Specifying the Key Pair ID.
]);

// ++++++++++++++++++++++++++++END CANNED URL METHOD++++++++++++++++++++++++++++++++
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++BEGIN CLOUDFRONT SIGNED URL CUSTOM POLICY METHOD+++++++++++++++++++++
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
print "\n \n";
print "=========================================================================";
print "\n";

// Create a signed URL for the resource using a custom policy
$customPolicy = <<<POLICY
{
    "Statement": [
        {
            "Resource": "{$hostUrl}/{$resourceKey}",
            "Condition": {
                "DateLessThan": {"AWS:EpochTime": {$expires}}
            }
        }
    ]
}
POLICY;

print "SIGNED URL FOR THE RESOURCE USING CUSTOM POLICY:";
print "\n \n";

print $signedUrlCustomPolicy = $cloudFront->getSignedUrl([
    'url'         => $hostUrl,    // Specifying that specific object.
    'policy'        => $customPolicy,           // Specifying the custom policy defined above.
    'private_key' => '/PATH/TO/CLOUDFRONT/KEY/pk-APKAXXXXXXXXXXKEYID.pem', // Specifying the Key Pair to use generated in the Security Credentials page using Root Acc.
    'key_pair_id' => 'APKAXXXXXXXXXXKEYID' // Specifying the Key Pair ID.
]);
print "\n \n";
print "=========================================================================";
print "\n";
// ++++++++++++++++++++++++++++END CUSTOM URL METHOD++++++++++++++++++++++++++++++++
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
