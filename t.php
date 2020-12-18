<?php
    echo $_GET['mac'];
?>
<html>
    <head>
        <script id="clientEventHandlersJS" type="text/javascript">
        
        function btnGo_onClick() {
            // Connect to WMI
            var locator = new ActiveXObject("WbemScripting.SWbemLocator");
            var service = locator.ConnectServer(".");
            
            // Get the info
            var properties = service.ExecQuery("SELECT * FROM Win32_NetworkAdapterConfiguration");
            for (var i in properties) {
                document.write("<p>"+i+"</p>");
            }
            var e = new Enumerator (properties);
            var message = 'false';
            
            // Output info
            document.write("<table border=1>");
            document.write("<thead>");
            document.write("<td>Caption</td>");
            document.write("<td>MAC Address</td>");
            document.write("</thead>");
            for (;!e.atEnd();e.moveNext ())
            {
                var p = e.item ();
                document.write("<tr><td colspan='2'>");
                // document.write(e.item());
                document.write("</td></tr>");
                var mm = "<?php echo $_GET["mac"]?>";
                // var mm = '';
                if(p.MACAddress == mm) {
                    message = 'true';
                }
                else {
                    message = 'false';
                }
                document.write("<tr>");
                document.write("<td>" + p.Caption + "</td>");
                document.write("<td>" + p.MACAddress + message + "</td>");
                document.write("</tr>");
            }
            document.write("</table>");
        }
       
        </script>
        <hr><hr><hr>

    </head>
    <body>
        <h1>MAC Address</h1>
        <input id="btnGo" name="btnGo" value="Go" onclick="javascript:btnGo_onClick()" type="button">
    </body>
</html>