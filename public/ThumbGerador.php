<?php
	
namespace ProCorpo\CRM\Controller;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use ProCorpo\CRM\Controller\s3class;


    // Método responsável por criar um box dentro de um grupo

 			//  $urlImagem = $_GET['imgurl'];

    //          $inFile 	= $urlImagem;

    //          $extencao = strrchr($urlImagem, '.');
    //          $codificaNome = md5('/thumb/thumb-'.date('Y-m-d H:i:s'));

    //          $outFile 	= $codificaNome.$extencao;
			 // $image 	= new Imagick($inFile);

			 // $image->cropImage(400,400, 30,10);
			 // $image->writeImage($outFile);



			$inFile = "https://github.global.ssl.fastly.net/images/modules/home/gh-app-windows.png";
			$outFile = "test-cropped.jpg";
			$image = new Imagick($inFile);
			$image->cropImage(400,400, 30,10);
			$image->writeImage($outFile);

    // Método responsáfel por criar um box dentro de um grupo


?>