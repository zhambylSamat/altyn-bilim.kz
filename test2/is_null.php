<?php
		// $ftp_server = "altyn-bilim.kz";
		// $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		// $login = ftp_login($ftp_conn, "altynbil", "grKNm4353l") or die("Could not connect to $ftp_server login or password error");;

		$file = "v/v1.mp4";

		// // try to delete file
		// if (ftp_delete($ftp_conn, $file))
		//   {
		//   echo "$file deleted";
		//   }
		// else
		//   {
		//   echo "Could not delete $file";
		//   }

		// // close connection
		// ftp_close($ftp_conn);


if (!unlink($file))
  {
  echo ("Error deleting $file");
  }
else
  {
  echo ("Deleted $file");
  }

// echo delete($file);
?>