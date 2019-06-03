<?php
/**
 * Dash
 * PHP Version 7
 * Get User Info
 */
function userinfo($conn,$UID){
    if ($UID!='error') {
        $stmt = $conn->prepare("SELECT Sex,Regdate,VIPend,mail,Phone From A_User_Info WHERE UserID = ?");
        $stmt->bind_param('i', $UID);
        $stmt->execute();
        $Sex=null;
        $Regdate=null;
        $VIPend=null;
        $mail=null;
        $Phone=null;
        $stmt->bind_result($Sex, $Regdate, $VIPend, $mail, $Phone);
        $stmt->fetch();
        echo json_encode(array('Sex'=>$Sex, 'Regdate'=>$Regdate, 'VIPend'=>$VIPend, 'mail'=>$mail, 'Phone'=>$Phone));
    } else {
        echo 'error';
    }
}
?>