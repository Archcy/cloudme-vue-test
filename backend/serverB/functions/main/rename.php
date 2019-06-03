<?php
/**
 * Rename, will be added in future
 */
function myfilerename($conn,$UID, $POST)
{
    $stmt = $conn->prepare("UPDATE B_File_Info set FName=? WHERE  UserID=? AND FID=?  ");
    $FID=$POST["FID"];
    $FName=$POST["FName"];
    $stmt->bind_param('sii',$FName, $UID,$FID);
    $stmt->execute();
}
?>