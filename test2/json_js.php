<!DOCTYPE html>
<html>
<head>
	<title>JSON js</title>
</head>
<body>

<script type="text/javascript">
	var json = '{ "arr1" : ["val1", "val2", "val3", "val4"], "arr2" : ["val5", "val6", "val7", "val8"] }';
	var arr = {};
	arr = {"arr1" : [[1,2,3],[4,5,6]], "arr2" : ["val5", "val6", "val7", "val8"]};
	console.log(arr);
	var obj = JSON.parse(json);
	console.log(obj.arr1);
	var index = (obj.arr1).indexOf("val3");
	(obj.arr1).splice(index,1);
	console.log(obj.arr1);
	console.log(obj.arr2);
</script>
</body>
</html>