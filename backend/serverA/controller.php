<?php
require './public/index.php';
require './functions/index.php';
header("Content-Type: application/json; charset=UTF-8");
$POSTDATA = file_get_contents('php://input');
$POST = !empty($POSTDATA) ? json_decode($POSTDATA, true) : array();
$conn=sql_connect();
controller($conn,$POST);
$conn->close();
function controller($conn,$POST)
{
    if(isset($_SERVER['HTTP_AUTHORIZATION'])){
        $UID=vtoken($_SERVER['HTTP_AUTHORIZATION']);
    }
    else {
        $UID='error';
    }
    if(isset($_SERVER['HTTP_APPFUNC'])){
        switch ($_SERVER['HTTP_APPFUNC'])
        {
            case 1:
                reg($conn,$POST);
                break;
            case 2:
                login($conn,$POST);
                break;
            case 3:
                userinfo($conn,$UID);
                break;
            case 4:
                genkey($conn,$UID,$POST);
                break;
            case 5:
                getfilekey($conn, $UID, $POST);
                break;
            case 7:
                delfilekey($conn, $UID, $POST);
                break;
        }
    }
}
