<!DOCTYPE html>
<html>
<head>
	<?php include_once('../meta.php');?>
	<title></title>
	<?php include_once('style.php');?>
	<link rel="stylesheet" href="datepicker/css/datepicker.css">
	<!-- <link rel="stylesheet/less" type='text/css' href="datepicker/less/datepicker.less"> -->
</head>
<body>

<!-- <input type="date" data-provide="datepicker" name=""> -->
<!-- <input data-provide="datepicker"> -->
<!-- <div class="input-group date" data-provide="datepicker">
    <input type="text" class="form-control datepicker">
    <div class="input-group-addon">
        <span class="glyphicon glyphicon-th"></span>
    </div>
</div> -->
<!-- <div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <input type='text' class="form-control" id='datetimepicker4' />
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker4').datetimepicker();
            });
        </script>
    </div>
</div> -->
<form>
<input type="text" class="form-control" placeholder="Please select date..." required="" id="dp1">
</form>
<?php include_once('js.php');?>
<script src="datepicker/js/bootstrap-datepicker.js"></script>
<script>
	$(function(){
		$('#dp1').datepicker({
			format: 'mm-dd-yyyy'
		});
	});
	// $(document).off('.datepicker.data-api');
    // $(function(){           
        // if (!Modernizr.inputtypes.date) {
            // $('input[type=date]').datepicker({
            //       dateFormat : 'yy-mm-dd'
            //     }
            //  );
            
//             $('.datepicker').datepicker({
//     format: 'mm/dd/yyyy',
//     startDate: '-3d'
// });
//         // }
//     });
</script>
</body>
</html>