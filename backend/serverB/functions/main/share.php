<?php
/**
 * GetShare and unshare File
 */
function getshare($conn,$UID, $POST)
{
   $FID = $POST['FID'];
   $stmt3 = $conn->prepare("SELECT FDIR FROM B_File_Share  WHERE UserID=? AND FID=?");
   $stmt3->bind_param('ii', $UID, $FID);
   $stmt3->execute();
   $FDIR="";
   $stmt3->bind_result($FDIR);
   $stmt3->fetch();
   return $FDIR;
}

function deldir($path){
   if(is_dir($path)){
      $p = scandir($path);
      foreach($p as $val){
          if($val !="." && $val !=".."){
            unlink($path.'/'.$val);
          }
      }
  }
}

function unshare($conn,$UID, $POST)
{
   $FID = $POST['FID'];
   $path=getshare($conn,$UID, $POST);
   if($path)
   {
      $stmt3 = $conn->prepare("DELETE FROM B_File_Share  WHERE UserID=? AND FID=?");
      $stmt3->bind_param('ii', $UID, $FID);
      $stmt3->execute();
      $stmt = $conn->prepare("UPDATE B_File_Info set FShare=? WHERE  UserID=? AND FID=?  ");
      $Fshare=0;
      $stmt->bind_param('iii',$Fshare, $UID,$FID) ;
      $stmt->execute();
      deldir($path);
      rmdir ($path);
   }
}
?>