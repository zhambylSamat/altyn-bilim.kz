<script src="../js/jquery.js"></script>
<script type="text/javascript">
	var arr = ['11','13','11','13','11','13'];
	var new_arr = [];
	console.log(arr);
	for(var i = 0; i < arr.length; i++){
		if($.inArray(arr[i], new_arr) === -1){
			new_arr.push(arr[i]);
		}
	}
</script>