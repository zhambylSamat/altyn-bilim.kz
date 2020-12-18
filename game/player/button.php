<?php
	if (!isset($_GET['id']) || !isset($_GET['img']) || !isset($_GET['title']) || !isset($_GET['color'])) {
		header("Location:index.php");
	}

	$id = $_GET['id'];
	$title = $_GET['title'];
	$img = $_GET['img'];
	$color = $_GET['color'];

	include_once('../connection.php');
	$link = $_SESSION['link'];

	$stmt = $conn->prepare("SELECT * FROM answer_button_access WHERE team_id = :team_id");
	$stmt->bindParam(':team_id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$access_count = $stmt->rowCount();
	$sql_access = $stmt->fetch(PDO::FETCH_ASSOC)['is_access'];
	
	if ($sql_access == 0) {
		$has_access = false;
	} else if ($sql_access == 1) {
		$has_access = true;
	}

	$result_row_count = 0;
	if ($has_access && isset($_GET['send'])) {
		$stmt = $conn->prepare("SELECT * FROM results WHERE team_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$result_row_count = $stmt->rowCount();

		if ($result_row_count == 0) {
			$stmt = $conn->prepare("INSERT INTO results (team_id) VALUES(:team_id)");
			$stmt->bindParam(":team_id", $id, PDO::PARAM_INT);
			$stmt->execute();

			$stmt = $conn->prepare("UPDATE answer_button_access SET is_access = 0 WHERE team_id = :team_id");
			$stmt->bindParam(":team_id", $id, PDO::PARAM_INT);
			$stmt->execute();
			$result_row_count = 1;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

	<style type="text/css">
		.full_height {
			width: 100%;
			height: 100vh;
			padding: 0;
			margin: 0;
		}
		.big-btn {
			border: none;
			border-radius: none;
			width: 100%;
			height: 100vh;
			padding: 0;
			margin: 0;
		}
		.img-btn {
			height: 100vh;
			padding: 10px 0px;
		}
		.access-denied {
			padding: 2% 2%;
			border-radius: 10px;
			box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
			background-color: rgba(255, 255, 255, 0.5)
		}
	</style>
</head>
<body>

	<div class='container-fluid'>
		<div class='row'>
			<?php
				$html = "";
				if ($result_row_count == 1) {
					$html .= "<div id='alert' style='position: absolute; left: 10%; width: 70%; z-index: 10000; margin: 5%' class='alert alert-success' role='alert'>
  								<b>Кнопка басылды</b>
							</div>";
				} else if (!$has_access) {
					$html .= "<div id='alert' style='position: absolute; left: 10%; width: 70%; z-index: 10000; margin: 5%' class='alert alert-danger' role='alert'>
  								<b>Доступ жоқ</b>
							</div>";
				}
				echo $html;
			?>
			<?php
				$html = "";
				$html .= "<div class='col-12 col-sm-12 col-md-12 full_height'>";
				$html .= 	"<form action='button.php' method='get'>";
				$html .= 		"<input type='hidden' name='id' value='".$id."' >";
				$html .= 		"<input type='hidden' name='title' value='".$title."' >";
				$html .= 		"<input type='hidden' name='img' value='".$img."' >";
				$html .= 		"<input type='hidden' name='color' value='".$color."' >";
				$html .= 		"<button type='submit' name='send' class='big-btn' style='background-color: ".$color.";'>";
				$html .= 			"<img class='img-btn' src='".$link.$img."'/>";
				$html .= 		"</button>";
				$html .= 	"</form>";
				$html .= "</div>";
				echo $html;
			?>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$length = $('#alert').length;
			if ($length == 1) {
				setTimeout(function(){
				  $('#alert').remove();
				}, 1000);
			}
		});
	</script>
</body>
</html>