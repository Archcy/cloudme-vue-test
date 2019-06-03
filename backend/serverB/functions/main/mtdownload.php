<?php
/**
 * Multi Part Download
 */
function MTDownload($UID, $POST)
{
    $multipartChunkSize=2*1024*1024;
    $fullPath = UPLOAD_DIR . $UID . '/'  .  $POST['FID'] . '.enc';
    if ($fd = fopen($fullPath, "rb")) {
       ob_clean();
       fseek($fd, $multipartChunkSize*$POST['PID']);
       flush();
       PDownload($fd);
       fclose($fd);
    }
}

function PDownload($fd)
{
    $multipartChunkSize=2*1024*1024;
    if (!feof($fd)) {
        $buffer = fread($fd, $multipartChunkSize);
        echo $buffer;
     }
}
?>