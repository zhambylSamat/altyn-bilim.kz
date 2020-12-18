<?php

	$config_list = array("config-quiz"=>"Аралық бақылау");

	try {
		
	} catch (PDOException $e) {
		throw $e;
	}
?>
<table class='table table-striped'>
	<tr>
		<th>
			<a role='button' class='config pull-left' data-type='start'>Бастапқы бет</a>
			<br>
			<center>Конфигурацияның түрін таңдаңыз</center>
		</th>
	</tr>
	<?php
		foreach ($config_list as $key => $value) {
			echo "<tr><td><a role='button' class='config' data-type='config' data-val='".$key."'>".$value."</a></td></tr>";
		}
	?>
</table>