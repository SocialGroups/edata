<?php
// Bucket Name
$bucket="9lessonsDemos";
if (!class_exists('S3'))require_once('S3.php');
			
//AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIJY3MIYAIY7T2CKQ');
if (!defined('awsSecretKey')) define('awsSecretKey', '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');
			
//instantiate the class
$s3 = new S3(awsAccessKey, awsSecretKey);

$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);

?>