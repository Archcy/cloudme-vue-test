<?php

/**
 * Gen key for Files Upload
 * PHP Version 7
 * 
 */
function GenFkey()
{
    $bytes = openssl_random_pseudo_bytes(32);
    return base64_encode($bytes);
}
function GenFIV()
{
    $bytes = openssl_random_pseudo_bytes(16);
    return base64_encode($bytes);
}

function genkey($conn, $UID, $POST)
{
    if ($UID != 'error') {
        $CUID = $UID;
        $FID = $POST['FID'];
        $Fkey = GenFkey();
        $FIV = GenFIV();
        $stmt = $conn->prepare("INSERT INTO A_User_Key (UserID,FID,Fkey,Fiv) VALUES (?,?,?,?)");
        $stmt->bind_param('iiss', $CUID, $FID, $Fkey, $FIV);
        $stmt->execute();
        echo json_encode(array('FID' => $FID, 'Fkey' => bin2hex(base64_decode($Fkey)), 'FIV' => bin2hex(base64_decode($FIV))));
    } else {
        echo 'error';
    }
}
?>