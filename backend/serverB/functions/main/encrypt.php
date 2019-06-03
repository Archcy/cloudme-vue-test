<?php

/**
 * 
 * Encrypt File ON server side
 * Need to Auth File Owner
 * php need to access openssl
 * 
 */
function FEncrypt($UID,$POST)
{
    $FID=$POST['FID'];
    //error_log("POSTIS: ".print_r($POST));
    //error_log("ENC: ".$POST['FID'].
    //" KEY: ".$POST['Fkey']." IV: ".$POST['FIV']);
    $SOURCE=UPLOAD_DIR . $UID . '/'  . $FID.'.unenc';
    $DEST=UPLOAD_DIR . $UID . '/'  . $FID.'.enc';
    $Shell='openssl enc -aes-256-cbc -in '
    .$SOURCE.' -out '
    .$DEST.' -K '
    .bin2hex(base64_decode($POST['Fkey'])).' -iv '
    .bin2hex(base64_decode($POST['FIV']));
    exec($Shell);
    unlink($SOURCE);
}
?>