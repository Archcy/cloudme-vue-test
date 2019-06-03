<?php
/**
 * get file key to download file
 */

function getfilekey($conn, $UID, $POST)
{
    if ($UID != 'error') {

        $FID = $POST['FID'];
        $stmt = $conn->prepare("SELECT Fkey,Fiv FROM A_User_Key  WHERE  UserID=? AND FID=?");
        $stmt->bind_param('ii', $UID, $FID);
        $stmt->execute();
        $stmt->bind_result($aFkey, $aFIV);
        $stmt->fetch(); 
        $Fkey = $aFkey;
        $FIV = $aFIV;

        echo json_encode(array('FID' => $FID, 'Fkey'=>bin2hex(base64_decode($Fkey)), 'FIV'=>bin2hex(base64_decode($FIV))));
    } else {
        echo 'error';
    }
}
?>
