<!DOCTYPE html>
<html>
<head>
	<title>form test</title>
</head>
<body>
	<form action='controller.php' method='post'>
		<input type="text" name="first_name" placeholder="first_name" required>
		<input type="text" name="last_name" required>
		<input type="text" name="email" required>
		<select name='sel' required>
			<option value=''>Choose one</option>	
			<option value='1'> bir </option>
			<option value='2'> eki </option>
			<option value='3'> uw </option>
		</select>
		<input type="submit" style='display: none;' name="sbm1" value='save'>
	</form>
	<form action='controller.php' method='post'>
		<input type="text" name="first_name" placeholder="first_name" required>
		<input type="text" name="last_name" required>
		<input type="text" name="email" required>
		<input type="submit" name="sbm2" value='save'>
	</form>
</body>
</html>