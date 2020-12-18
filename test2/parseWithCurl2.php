<?php
echo '11111111<br>';

// $data = array("idtc" => "834003094",
//             "idTestType" => "25",
//             "iin"=> "000430500050",
//             "langId" => "1",
//         	"iktent" => "834003094");
// $data = array("go" => "Узнать+результат", "iktent" => "834003094");
$data_string = json_encode($data);

// set url
// curl_setopt($ch, CURLOPT_URL, "https://res.testcenter.kz/test-result/api/userdata/");
$ch = curl_init("https://edu.mail.kz/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "iktent=834003094&go=Узнать+результат");

// //return the transfer as a string
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                      
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                 
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                    
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                      
    // 'Content-Type: application/json',                                             
    // 'Content-Length: ' . strlen($data_string))                                           
// );      

// $output contains the output string
$output = curl_exec($ch);

// close curl resource to free up system resources
curl_close($ch);      

print_r($output);

echo '<br>2222';
?>

