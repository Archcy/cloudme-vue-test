<?php

/**
 * Check file is Encrypted or not
 * 
 * IF not Encrypted,Download directly
 * Decrypt and Share File 
 */

function randomkeys($length)
{
   $pattern = '1234567890abcdefghijklmnopqrstuvwxyz   
   ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
   $key='';
   for ($i = 0; $i < $length; $i++) {
      $key .= $pattern{
      mt_rand(0, 35)};    //get rand num  
   }
   return $key;
}
function ShareDownload($conn,$UID, $POST)
{
   $FID = $POST['FID'];
   $IN = UPLOAD_DIR . $UID . '/'  . $FID . '.unenc';
   $dir='./DNC/'.randomkeys(200);
   while (file_exists($dir)){
      $dir='./DNC/'.randomkeys(200);
   }
   $stmt3 = $conn->prepare("INSERT INTO B_File_Share (UserID,FID,FDIR) VALUES (?,?,?)");
   $stmt3->bind_param('iis', $UID, $FID, $dir);
   $stmt3->execute();
   $stmt = $conn->prepare("UPDATE B_File_Info set FShare=? WHERE  UserID=? AND FID=?  ");
   $Fshare=1;
   $stmt->bind_param('iii',$Fshare, $UID,$FID) ;
   $stmt->execute();
   mkdir ($dir,0777,true);
   $OUT= $dir.'/'.$POST['FName'];
   rename($IN,$OUT);
   echo $OUT;
  
}
/*function Download($UID, $POST)
{
   $fullPath = UPLOAD_DIR . $UID . '/'  .  $POST['FID'] . '.unenc';
   if ($fd = fopen($fullPath, "rb")) {
      $fsize = filesize($fullPath);
      header("Content-type: application/octet-stream");
      header("Content-Disposition:");
      header("Content-length: $fsize");
      //header("Cache-control: private"); //use this to open files directly
      ob_clean();
      flush();
      while (!feof($fd)) {
         $buffer = fread($fd, 2048);
         echo $buffer;
      }
      //readfile($fullPath);
      fclose($fd);
      ignore_user_abort(true);
      if (connection_aborted()) {
         unlink($fullPath);
      }

   }
   //unlink($fullPath);
}*/
