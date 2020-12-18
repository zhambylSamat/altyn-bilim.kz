<?php
    include_once("../connection.php");
    try {
        $stmt = $conn->prepare("SELECT id, name FROM entrance_examination_pocket ORDER BY name");
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error ".$e->getMessage()." !!!";
    }
?>
<form id='new-student-ee'>
    <div class='form-group'>
        <label for='surname'>Тегі</label>
        <input type="text" name="surname" id='surname' class='form-control' placeholder="Тегі" required="">
    </div>
	<div class='form-group'>
		<label for='name'>Аты</label>
		<input type="text" name="name" id='name' class='form-control' placeholder="Аты" required="">
	</div>
	<div class='form-group'>
        <label for='entrance-examination-pocket'></label>
        <select id='entrance-examination-pocket' name='test_pocket' class='form-control' required="">
            <option value=''>Тесттер жинағы...</option>
            <?php foreach ($result as $value) { ?>
            <option value='<?php echo $value['id']; ?>'><?php echo $value['name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <input type="submit" class='btn btn-sm btn-success' value='Сақтау'>
</form>