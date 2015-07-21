<?php

if (!class_exists('S3')) require_once 'S3.php';
if (!class_exists('AWSRoleCredential')) require_once 'AWSRoleCredential.php';

$cred = new AWSRoleCredential('aws-elasticbeanstalk-ec2-role');

print $cred->accessKeyId." ".$cred->secretAccessKey."\n";
print $cred->token."\n";

$s3 = new S3($cred->accessKeyId, $cred->secretAccessKey, true, 's3.amazonaws.com', $cred->token);
#$s3->sessionToken = $cred->token;
echo "S3::listBuckets(): ".print_r($s3->listBuckets(), 1)."\n";

?>
