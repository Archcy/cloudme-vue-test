<?php
/**
 * Login
 * PHP Version 7
 * 
 */

function login($conn, $POST)
{
    if ($POST['username'] != '' && $POST['passwd'] != '') {
        if ($conn) {
            $stmt = $conn->prepare("SELECT Pass,UserID FROM A_User_Control WHERE User=?");
            $stmt->bind_param('s', $POST['username']);
            $stmt->execute();
            $upass=null;
            $UserID=null;
            $stmt->bind_result($upass, $UserID);
            $stmt->fetch();
            if (password_verify($POST['passwd'], $upass) ) {
                echo json_encode(array('status'=>'success','token'=>gtoken('0', $UserID)));
            }else {
                echo json_encode(array('status'=>'failed','token'=>null));
            }
            return null;
        }
    } else { 
        echo json_encode(array('status'=>'error','token'=>null));
        return null;
    }
}
?>