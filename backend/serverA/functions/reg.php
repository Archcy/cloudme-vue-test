<?php
function reg($conn, $POST)
{
    $username = $POST['username'];
    if ($username != '' && $POST['passwd'] != '') {
        if ($conn) {
            $stmt = $conn->prepare("SELECT User FROM A_User_Control WHERE User=?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($col);
            $result = $stmt->fetch();
            if (!$result) {
                $stmt1 = $conn->prepare("INSERT INTO A_User_Control (User,Pass) VALUES (?,?)");
                $stmt1->bind_param('ss', $username, password_hash($POST['passwd'], PASSWORD_BCRYPT));
                $stmt1->execute();
                $CID = mysqli_insert_id($conn);
                $stmt3 = $conn->prepare("INSERT INTO A_User_Info (UserID,Sex,mail,Phone) VALUES (?,?,?,?)");
                $stmt3->bind_param('isss', $CID, $POST['sex'], $POST['mail'], $POST['phone']);
                $stmt3->execute();
                echo json_encode(array('status'=>'1','UID'=>$CID));
            }
            if ($result) {
                echo json_encode(array('status'=>'0'));
            }
        }
    } else {
        echo json_encode(array('status'=>'3'));
    }
}
