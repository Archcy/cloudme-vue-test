<?php
/**
 * Add Folder
 */
function insertSQL($conn, $UID, $FID, $FName, $Fsize, $FPID, $FShare, $Ftype, $Fav)
{
    $stmt = $conn->prepare("INSERT into B_File_Info(UserID,FID,FName,Fsize,FPID,FShare,Ftype,Fav) values (?,?,?,?,?,?,?,?)");
    $stmt->bind_param('iisiiiii', $UID, $FID, $FName, $Fsize, $FPID, $FShare, $Ftype, $Fav);
    $stmt->execute();
}
function addFolder($conn,$UID,$POST)
{
    if($UID!='error')
    {
        $stmt = $conn->prepare("INSERT into B_File_Index(UserID) values (?)");
        $stmt->bind_param('i', $UID) ;
        $stmt->execute();
        $FID = mysqli_insert_id($conn);
        $FPID = $POST['FPID'];
        $Fshare = 0;
        $Ftype = 1;
        $Fav = 0;
        insertSQL($conn, $UID, $FID, $POST['NFname'], 0, $FPID, $Fshare, $Ftype, $Fav);
    }
} 
?>