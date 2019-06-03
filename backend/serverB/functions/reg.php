<?php
function reg($conn, $POST)
{
    //need to Auth ServerA In Future
    if ($conn != 0) {
        $CID=$POST['UID'];
        $stmt1 = $conn->prepare("INSERT INTO B_User_Control (UserID,User,Pass) VALUES (?,?,?)");
        $stmt1->bind_param('iss', $CID, $POST['username'], password_hash($POST['passwd'], PASSWORD_BCRYPT));
        $stmt1->execute();
        $stmt3 = $conn->prepare("INSERT INTO B_User_Info (UserID,mail,Phone) VALUES (?,?,?)");
        $stmt3->bind_param('iss', $CID, $POST['mail'], $POST['phone']);
        $stmt3->execute();
        if (!file_exists(UPLOAD_DIR . $CID)) {
            mkdir(UPLOAD_DIR . $CID, 0777, true);
            mkdir(UPLOAD_DIR . $CID . '/.tmp', 0777, true);
        }
        echo 1;
    }
}
