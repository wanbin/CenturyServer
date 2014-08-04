<?php
 
	 function dataConnect()
		{
			$con = mysql_connect(DB_HOST, DB_USER, DB_PWD);
			if (!$con){die('Could not connect: ' . mysql_error());}
			mysql_select_db(DB_NAME, $con);
			mysql_query("SET NAMES 'utf8'");
		}

 
?>