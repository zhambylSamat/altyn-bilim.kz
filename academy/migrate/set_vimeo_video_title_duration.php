<?php
	include_once('../common/connection.php');

	try {

		$stmt = $connect->prepare("SELECT * FROM tutorial_video");
		$stmt->execute();
		$result = $stmt->fetchAll();




		if (isset($_POST['save'])) {
			$stmt = $connect->prepare("UPDATE tutorial_video SET title = ?, duration = ? WHERE id = ?");
			$ids = $_POST['id'];
			$titles = $_POST['title'];
			$durations = $_POST['duration'];
			for ($i = 0; $i < count($ids); $i++) {
				$stmt->execute(array($titles[$i], $durations[$i], $ids[$i]));
			}
		}

	} catch (Exception $e) {
		throw $e;
	}

echo "<form method='post'>";
	foreach ($result as $value) {
		echo "<div>";
		echo "<input type='text' name='id[]' value='".$value['id']."'>";
		echo "<input type='text' name='link[]' value='".$value['link']."'>";
		echo "<input type='text' name='title[]' value=''>";
		echo "<input type='text' name='duration[]' value=''>";
		echo "</div>";
		echo "<br><br>";
	}
	echo "<input type='submit' name='save' value='okey'>";
echo "</form>";
echo "<button id='find'>find title and duration</button>"
?>

<script type="text/javascript" src='../../js/jquery.js'></script>
<script type="text/javascript">

	$(document).on('click', '#find', function()  {
		$('form').find('div').each(function() {
			$id = $(this).find('input[name="id[]"]').val();
			$link = $(this).find('input[name="link[]"]').val();

			$json = find($link, $(this));
		});
	});
	
	function find($link, $elem) {
		$res = [];
		$.ajax({
		    	url: 'https://vimeo.com/api/oembed.json?url=' + $link,
				type: "GET",
				beforeSend:function(){
					$('#lll').css('display','block');
				},
				success: function($data){
					$res = $data;
					$elem.find('input[name="title[]"]').val($res.title);
					$elem.find('input[name="duration[]"]').val($res.duration);
					console.log($res);
			    },
			  	error: function($dataS) 
		    	{
		    		console.log($dataS);
		    	} 	     
		   	});
	}
</script>