<?php
/**
 * del file key for  file
 * return null
 */

function delfilekey($conn, $UID, $POST)
{
    if ($UID != 'error') {
        $FID = $POST['FID'];
        $stmt = $conn->prepare("DELETE FROM A_User_Key  WHERE  UserID=? AND FID=?");
        $stmt->bind_param('ii', $UID, $FID);
        $stmt->execute();
        echo 1;
    } else {
        echo 'error';
    }
}
