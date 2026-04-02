<?php
session_start();
function create_number(&$calc){
    $im = imagecreate(40, 40);
    imagecolorallocatealpha($im, 255, 255, 255,127);

    for ($i = 0; $i < 30; $i++) {
        $color = imagecolorallocatealpha($im, mt_rand(127, 200), mt_rand(127, 200), mt_rand(127, 200), 0);
        imageline($im, mt_rand(0, 40), mt_rand(0, 50), mt_rand(0, 40), mt_rand(0, 50), $color);
    }

    $color = imagecolorallocate($im, mt_rand(0,97), mt_rand(0,97), mt_rand(0,97));

    $r = mt_rand(0,9);
    imagestring($im, 5, mt_rand(0,10), mt_rand(0,10), $r, $color);
    $calc .= $r;
    return imagerotate($im, mt_rand(-30,30), 0);
}

function create_calc(&$cal){
    $calc = ['+','-','*'];
    $im = imagecreate(40, 40);
    imagecolorallocatealpha($im, 255, 255, 255,127);

    for ($i = 0; $i < 30; $i++) {
        $color = imagecolorallocatealpha($im, mt_rand(187, 200), mt_rand(187, 200), mt_rand(187, 200), 0);
        imageline($im, mt_rand(0, 40), mt_rand(0, 50), mt_rand(0, 40), mt_rand(0, 50), $color);
    }

    $color = imagecolorallocate($im, mt_rand(0,90), mt_rand(0,90), mt_rand(0,90));

    $r = mt_rand(0,2);

    imagestring($im, 5, mt_rand(0,10), mt_rand(0,10), $calc[$r], $color);
    $cal .=$calc[$r];
    return imagerotate($im, mt_rand(-20,20), 0);
}

$calculation = "";

$imgparts = array(create_number($calculation), create_calc($calculation), create_number($calculation));
$img = imagecreate(120,50);
imagecolorallocate($img, 255, 255, 255);

for ($i = 0; $i < 30; $i++) {
    $color = imagecolorallocatealpha($img, mt_rand(187, 200), mt_rand(187, 200), mt_rand(187, 200), 0);
    imageline($img, mt_rand(0, 120), mt_rand(0, 50), mt_rand(0, 120), mt_rand(0, 50), $color);
}

mt_srand(time()+mt_rand(0,1000000));
$_SESSION["captcha"] = eval("return $calculation;");

for($i = 0; $i < count($imgparts); $i++){
    imagecopymerge($img,$imgparts[$i],40*$i,0,0,0,40,40,100);
}

header('Cache-Control: no-cache');
header('Cache-Control: no-store');
header("Content-Type: image/png");
imagepng($img);

session_write_close()
?>