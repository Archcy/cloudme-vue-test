<?php
/**
 * Generate File ID. This will be removed in future
 */
function GenFID($conn,$UID)
{
    if($UID!='error')
    {
        $stmt = $conn->prepare("INSERT into B_File_Index(UserID) values (?)");
        $stmt->bind_param('i', $UID) ;
        $stmt->execute();
        $FID = mysqli_insert_id($conn);
        echo  json_encode(array("FID"=>$FID));
    }
} 
?>