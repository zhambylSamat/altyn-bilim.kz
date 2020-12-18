<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/root_url.php');

	$bootstrap_path = $ab_root.'/bootstrap/js';

	$js_path = $ab_root.'/js';
?>
<script src="<?php echo $js_path;?>/jquery.js"></script>
<script src="https://player.vimeo.com/api/player.js"></script>
<script src="<?php echo $js_path;?>/jquery.color.js"></script>
<script src="<?php echo $bootstrap_path;?>/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $bootstrap_path;?>/less.min.js"></script>
<script src="<?php echo $bootstrap_path;?>/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo $bootstrap_path;?>/bootstrap-datepicker.ru.min.js"></script>
<script type="text/javascript" src="<?php echo $bootstrap_path;?>/chart.js"></script>
<script type="text/javascript" src='<?php echo $ab_root.'/academy/common/assets'; ?>/js/actions.js?v=0.1.2'></script>