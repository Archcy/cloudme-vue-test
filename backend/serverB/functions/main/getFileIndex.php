<?php
/**
 * Send File Index Back
 */

function getFileIndex($conn,$UID)
{
    if ($UID != 'error') {
        $stmt = $conn->prepare("SELECT FID,FName,Fsize,FPID,FShare,Ftype,Fav,Ftime From B_File_Info WHERE UserID = ?");
        $stmt->bind_param('i', $UID);
        $stmt->execute();
        $stmt->bind_result($FID, $FName ,$Fsize, $FPID, $FShare, $Ftype, $Fav, $Ftime);
        while ($stmt->fetch()) {
            $out[] = array('FID'=>$FID, 'FName'=>$FName, 'Fsize'=>$Fsize, 'FPID'=>$FPID ,'FShare'=>$FShare, 'Ftype'=>$Ftype, 'Fav'=>$Fav, 'Ftime'=>$Ftime , 'FC'=>0);
        }
        echo json_encode($out);
    } else {
        echo 'error';
    }
}
?>