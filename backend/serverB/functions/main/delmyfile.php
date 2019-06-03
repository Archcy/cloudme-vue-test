<?php
/**
 * Delete File
 * Need to change User space Usage And Remove Share File at First
 * 
 */

function pre_del($conn, $UID, $POST)
{
    $stmt2 = $conn->prepare("SELECT StorageUsed FROM B_User_Info  WHERE  UserID=?");
    $stmt2->bind_param('i', $UID);
    $stmt2->execute();
    $Usize=0;
    $stmt2->bind_result($Usize);
    $stmt2->fetch();
    return $Usize;
}

function pre_del2($conn, $UID,$FID,$Usize)
{
    $stmt3 = $conn->prepare("SELECT Fsize FROM B_File_Info  WHERE  UserID=? AND FID=?");
    $stmt3->bind_param('ii', $UID, $FID);
    $stmt3->execute();
    $Fsize=0;
    $stmt3->bind_result($Fsize);
    $stmt3->fetch();
    $Usize=$Usize-$Fsize;
    return $Usize;
}

function delmyfile($conn, $UID, $POST)
{
    if ($UID != 'error') {
        $Usize=pre_del($conn, $UID, $POST);
        $FID = $POST['FID'];
        $Usize2=pre_del2($conn, $UID,$FID,$Usize);
        $stmt = $conn->prepare("UPDATE B_User_Info set StorageUsed=? WHERE  UserID=?");
        $stmt->bind_param('ii',$Usize2, $UID);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM B_File_Index  WHERE  UserID=? AND FID=?");
        $stmt->bind_param('ii', $UID, $FID);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM B_File_Info  WHERE  UserID=? AND FID=?");
        $stmt->bind_param('ii', $UID, $FID);
        $stmt->execute();
        $fullPath = UPLOAD_DIR . $UID . '/'  .  $POST['FID'] . '.enc';
        unlink($fullPath);
        echo 1;
    } else {
        echo 'error';
    }
}

?>