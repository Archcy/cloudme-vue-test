<?php
require './public/index.php';
require './functions/index.php';
header("Content-Type: application/json; charset=UTF-8");
$POSTDATA = file_get_contents('php://input');
$POST = !empty($POSTDATA) ? json_decode($POSTDATA, true) : array();
$conn = sql_connect();
controller($conn, $POST);
$conn->close();
function controller($conn, $POST)
{
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $UID = vtoken($_SERVER['HTTP_AUTHORIZATION']);
    } else {
        $UID = 'error';
    }
    if (isset($_SERVER['HTTP_APPFUNC'])) {
        switch ($_SERVER['HTTP_APPFUNC']) {
            case 1:
                reg($conn, $POST);
                break;
            case 2:
                login($conn, $POST);
                break;
            case 3:
                getFileIndex($conn, $UID);
                break;
            case 10:
                GenFID($conn, $UID);
                break;
            case 4:
                if ($UID != 'error') {
                    $uploader = new Uploader();
                    $uploader->saveUploadedFile($conn, $UID);
                }
                break;
            case 5:
                if ($UID != 'error') {
                    FDecrypt($UID, $POST);
                    ShareDownload($conn, $UID, $POST);
                }
                break;
            case 6:
                if ($UID != 'error') {
                    MTDownload($UID, $POST);
                }
                break;
            case 7:
                if ($UID != 'error') {
                    unshare($conn, $UID, $POST);
                    delmyfile($conn, $UID, $POST);
                }
                break;
            case 8:
                addFolder($conn, $UID, $POST);
                break;
            case 9:
                Fav($conn, $UID, $POST);
                break;
            case 11:
                unFav($conn, $UID, $POST);
                break;
            case 12:
                getusage($conn, $UID);
                break;
            case 13:
                if ($UID != 'error') {
                    echo getshare($conn, $UID, $POST);
                }
                break;
            case 14:
                if ($UID != 'error') {
                    unshare($conn, $UID, $POST);
                }
                break;
            case 15:
                myfilerename($conn, $UID, $POST);
                break;
        }
    }
}
?>