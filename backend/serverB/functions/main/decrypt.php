<?php

/**
 * 
 * Encrypt File
 * Need to Auth File Owner
 * 
 */
function FDecrypt($UID,$POST)
{
    $FID=$POST['FID'];
    //error_log("POSTIS: ".print_r($POST));
    //error_log("ENC: ".$POST['FID'].
    //" KEY: ".$POST['Fkey']." IV: ".$POST['FIV']);
    $DEST=UPLOAD_DIR . $UID . '/'  . $FID.'.unenc';
    $SOURCE=UPLOAD_DIR . $UID . '/'  . $FID.'.enc';
    $Shell='openssl enc -d -aes-256-cfb -in '
    .$SOURCE.' -out '
    .$DEST.' -K '
    .$POST['Fkey'].' -iv '
    .$POST['FIV'];
    exec($Shell);
    /*$key = hex2bin($POST['Fkey']);
    $iv = hex2bin($POST['FIV']);
    $multipartChunkSize=2*1024*1024;
    $myfile = fopen($DEST, "a");
    $output="";
    $i=1;
    if ($fd = fopen($SOURCE, "rb")) {
       $multipartChunkSize=2*1024*1024;
       while (!feof($fd)) {
           $buffer = fread($fd, $multipartChunkSize*$i);
           $output = openssl_decrypt($buffer, 'AES-256-CFB', $key, OPENSSL_RAW_DATA, $iv);
           fwrite($myfile,$output);
           $i++;
        }

       fclose($fd);
    }
    fclose($myfile);*/

    
    //echo json_encode(array('FID'=>$POST['FID']));
}
?>