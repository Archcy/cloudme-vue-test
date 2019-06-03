<?php
/**
 * Fav and unFav File
 */
function Fav($conn,$UID,$POST)
{
    if($UID!='error')
    {
        $stmt = $conn->prepare("UPDATE B_File_Info set Fav=? WHERE  UserID=? AND FID=? ");
        $Fav=1;
        $FID = $POST['FID'];
        $stmt->bind_param('iii',$Fav, $UID,$FID) ;
        $stmt->execute();
    }
} 
function unFav($conn,$UID,$POST)
{
    if($UID!='error')
    {
        $stmt = $conn->prepare("UPDATE B_File_Info set Fav=? WHERE  UserID=? AND FID=?  ");
        $Fav=0;
        $FID = $POST['FID'];
        $stmt->bind_param('iii',$Fav, $UID,$FID) ;
        $stmt->execute();
    }
} 
?>