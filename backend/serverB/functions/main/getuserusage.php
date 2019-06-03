<?php
/**
 * Get user space Usage Info
 */
function getusage($conn,$UID)
{
    if ($UID != 'error') {
        $stmt = $conn->prepare("SELECT StorageTotal,StorageUsed From B_User_Info WHERE UserID = ?");
        $stmt->bind_param('i', $UID);
        $stmt->execute();
        $StorageTotal=0;
        $StorageUsed=0;
        $stmt->bind_result($StorageTotal, $StorageUsed);
        $stmt->fetch();
        $out = array('StorageTotal'=>$StorageTotal, 'StorageUsed'=>$StorageUsed);
        echo json_encode($out);
    }else {
        echo 'error';
    }
}
?>