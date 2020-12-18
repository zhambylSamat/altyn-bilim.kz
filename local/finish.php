<?php include_once('../connection.php');?>
<html>
    <head>
        <script id="clientEventHandlersJS" type="text/javascript">
        
        // function btnGo_onClick() {
        //     // Connect to WMI
        //     var locator = new ActiveXObject("WbemScripting.SWbemLocator");
        //     var service = locator.ConnectServer(".");
            
        //     // Get the info
        //     var properties = service.ExecQuery("SELECT * FROM Win32_NetworkAdapterConfiguration");
        //     for (var i in properties) {
        //         document.write("<p>"+i+"</p>");
        //     }
        //     var e = new Enumerator (properties);
        //     var message = 'false';
            
        //     // Output info
        //     document.write("<table border=1>");
        //     document.write("<thead>");
        //     document.write("<td>Caption</td>");
        //     document.write("<td>MAC Address</td>");
        //     document.write("</thead>");
        //     for (;!e.atEnd();e.moveNext ())
        //     {
        //         var p = e.item ();
        //         document.write("<tr><td colspan='2'>");
        //         // document.write(e.item());
        //         document.write("</td></tr>");
        //         var mm = " ";
        //         // var mm = '';
        //         if(p.MACAddress == mm) {
        //             message = 'true';
        //         }
        //         else {
        //             message = 'false';
        //         }
        //         document.write("<tr>");
        //         document.write("<td>" + p.Caption + "</td>");
        //         document.write("<td>" + p.MACAddress + message + "</td>");
        //         document.write("</tr>");
        //     }
        //     document.write("</table>");
        // }
        
        </script>
        <script type="text/javascript">
            function btnCh(){
            var mac = "B0-C0-90-A9-16-91";
        }
        </script>
    </head>
<script type="text/javascript">
function fnUnloadHandler() {
  xmlhttp=null; 
  if (window.XMLHttpRequest) 
     {// code for Firefox, Opera, IE7, etc. 
        xmlhttp=new XMLHttpRequest(); 
     } 
  else if (window.ActiveXObject) 
     {// code for IE6, IE5 
        xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
     } 

  if (xmlhttp!=null) 
     {  
        xmlhttp.open("GET","del_action.php",true); 
        xmlhttp.send(null); 

     } 
     else 
     { 
        alert("Your browser does not support XMLHTTP."); 
     } 

}
</script>
<body onbeforeunload="fnUnloadHandler()">
    <h1>Please do not reload and close browser/window/tab!</h1>
    <?php
        try {
            $stmt = $conn->prepare("SELECT count(*) FROM user_connection_tmp WHERE student_num = :student_num");
            $stmt->bindParam(':student_num', $_SESSION['student_num'], PDO::PARAM_STR);
            $stmt->execute();
            $result_ip = $stmt->fetchColumn(); 
            if($result_ip==0){
                header('location:index.php');
            } 
        } catch (PDOException $e) {
        echo "Error : ".$e->getMessage()." !!!";
        }
    ?>
    <!-- <button onclick='fnUnloadHandler()'>asdf</button>
        <h3><?php echo $_SESSION['ip_address'];?></h3>
        <h4><?php echo $_SESSION['student_num'];?></h4>
        <input id="btnGo" name="btnGo" value="Start" onclick="javascript:btnGo_onClick()" type="button">
        <input id="btnGo" name="btnGo" value="Start" onclick="javascript:abc()" type="button"> -->
    </body>
</html>