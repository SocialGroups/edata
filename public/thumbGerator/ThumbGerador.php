<?php

    $inFile = "http://s.glbimg.com/es/ge/f/original/2010/11/19/flamengo_65x65.png";
	$outFile = "test2-thumbnail.jpg";
	$image = new Imagick($inFile);
	$image->thumbnailImage(200, 200);
	$image->writeImage($outFile);
    // Método responsáfel por criar um box dentro de um grupo


?>