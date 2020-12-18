<?php
include_once('../connection.php');
try {
	$stmt = $conn->prepare("SELECT * FROM device_mac_address");
	     
    $stmt->execute();
    $result_mac = $stmt->fetchAll(); 
    foreach($result_mac as $mac){
    	echo $mac['mac_address'];
    }
} catch (PDOException $e) {
	echo "Error : ".$e->getMessage()." !!!";
}
$count = 0;
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript">
	(function(){
		var mac = 'B0:C0:90:A9:16:91';
		<?php foreach($result_mac as $mac){?>
			if(var mac == <?php echo $mac['mac_address'];?>){
				<?php $count++; header('location:finish.php');?>
				break;
			}
		<?php }?>
		<?php if($count==0) header('location:index.php');?>
	})();
		// (function() {
  //           // Connect to WMI
  //           var locator = new ActiveXObject("WbemScripting.SWbemLocator");
  //           var service = locator.ConnectServer(".");
            
  //           // Get the info
  //           var properties = service.ExecQuery("SELECT * FROM Win32_NetworkAdapterConfiguration");
  //           // for (var i in properties) {
  //           //     document.write("<p>"+i+"</p>");
  //           // }
  //           var e = new Enumerator (properties);
  //           <?php $count = 0;?>
  //           for (;!e.atEnd();e.moveNext ())
  //           {
  //               var p = e.item ();
                
  //               <?php $count = 0; foreach($result_mac as $mac){?>
  //                if(p.MACAddress == <?php echo $mac['mac_address'];?>){
  //                  <?php $count++; header('location:finish.php');?>
  //                  break;
  //                }
  //              <?php }?>
               
  //           }
  //           <?php if($count==0) header('location:index.php?asdf');?>
  //       })();
	</script>
</head>
<body>
<!-- <button onclick='abc()'>asdf</button> -->
</body>
</html>