<?php

/**
 * Token Generate and Verify
 * 
 * 
 */


define('TOKEN_CIPHER', 'AES-128-CBC');
define('TOKEN_KEY', 'y3IlBHQ9T6LHieoS9pE8i7qLXxPIVFMCsQjRn/lfg6k=');
define('TOKEN_IV', "TxzH2rTbwM/LZDN82CHObg==");

function gtoken($Level, $ID)
{
    $value = array(
        "Auth" => "AuthDrive",
        "Exp" => time(),
        "Level" => $Level,
        "ID" => $ID
    );
    $out = json_encode($value);
    return openssl_encrypt($out, TOKEN_CIPHER, base64_decode(TOKEN_KEY), 0, base64_decode(TOKEN_IV));
}

function vtoken($in)
{
    $enc_tmp = openssl_decrypt($in, TOKEN_CIPHER, base64_decode(TOKEN_KEY) , 0, base64_decode(TOKEN_IV));
    $data=json_decode($enc_tmp, true);
    if ($data['Auth']=="AuthDrive") {
        return $data['ID'];
    } else {
        return 'error';
    }
    
}
?>