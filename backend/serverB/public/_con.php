<?php
/**
 * Connect mySQL server
 * PHP Version 7
 * 
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */


/** 
 * Connect 
 * db use mysqi
 * create by Archcy
 * 
 * @author wang
 * @return int|mysqli|bool in connect
 */
function Sql_connect()
{
    
    $servername = "127.0.0.1";
    $username = "localsite";
    $password = "pass110me123";

    $conn = mysqli_connect($servername, $username, $password, "localsite");
    // Check connection
    if (mysqli_connect_errno()) {
        return 0;
    }
    return $conn;
}
?>