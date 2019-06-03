<?php
require './public/index.php';
header("Content-Type: application/json; charset=UTF-8");
$POSTDATA = file_get_contents('php://input');
$POST = !empty($POSTDATA) ? json_decode($POSTDATA, true) : array();
$conn = sql_connect();

if ($POST['username'] != '' && $POST['passwd'] != '') {
    if ($conn != 0) {
        $stmt = $conn->prepare("SELECT Pass FROM B_Admin WHERE User=?");
        $stmt->bind_param('s', $POST[username]);
        $stmt->execute();
        $stmt->bind_result($Tpass);
        $stmt->fetch();
        $upass.=$Tpass;
        $rus1=[];
        $rus2=[];
        if($Tpass==$POST['passwd'])
        {
            exec("df",$rus1);
            exec("cat /var/log/nginx/error.log",$rus2);
            echo(array("df"=>$rus1,"cat"=>$rus2));
        }
        $conn->close();
        return null;
    }
} else { 
    echo json_encode(array('status'=>'failed','token'=>null));
    return null;
}

?>