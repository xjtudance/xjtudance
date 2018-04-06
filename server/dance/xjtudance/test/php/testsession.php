<?php  

if(isset($PHPSESSID)) {
	session_id($PHPSESSID);

}

session_start();
	echo session_id();

if(isset($_SESSION['views']))
  $_SESSION['views'] = $_SESSION['views'] + 1;

else
  $_SESSION['views'] = 1;
echo "Views = ". $_SESSION['views'];


?> 