<?php

namespace SocialGroups\Application\AWS;

class S3API implements Routable
{	

	public function S3Config()
	{

		// Bucket Name
		$bucket="9lessonsDemos";
		if (!class_exists('S3'))

			//require_once('S3.php');

			use ProCorpo\CRM\AWS\S3;
					
		//AWS access info
		if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIJY3MIYAIY7T2CKQ');
		if (!defined('awsSecretKey')) define('awsSecretKey', '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');
					
		//instantiate the class
		$s3 = new S3(awsAccessKey, awsSecretKey);

		$s3->putBucket($bucket, S3::ACL_PUBLIC_READ);

	}


    public function get($acao = null, $arg1 = null)
    {

        if($acao == "S3Config") {

            return $this->S3Config();
        
        }else if($acao == "S3") {

            return $this->S3();
        
        }

            return $vars;

    }

    public function post($acao = null)
    {
        
        if($action == "teste") {

            $testando = $_POST;
            return $this->teste($testando);
        }
        
    }

}


?>
