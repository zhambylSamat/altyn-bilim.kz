$(document).on('click', '.set_breadcrumb', function() {
	$id = $(this).parent().data('id');
	$title = $(this).parent().data('title');
	$obj = $(this).parent().parent().data('obj');
	$next_obj = $(this).parent().parent().data('next-obj');
	set_breadcrumb($obj, $id, $title, 'append');
	set_content($datas, $next_obj);
});


$(document).on('click', '.materials #breadcrumb li', function() {
	$id = $(this).data('id');
	if ($id != '') {
		$title = $(this).data('title');
		$obj = $(this).data('obj');
		$datas = set_breadcrumb($obj, $id, $title, '');
		set_content($datas, $obj);
	}
});

function set_breadcrumb($obj, $id, $title, $action) {
	$datas = [];
	$breadcrumb_count = 0;
	$('.materials .breadcrumb li').each(function() {
		$breadcrumb_count++;
		$tmp_title = $(this).data('title');
		$tmp_id = $(this).data('id');
		$tmp_obj = $(this).data('obj');
		$tmp_next = $(this).data('next');
		$tmp_dict = {
			title: $tmp_title,
			id: $tmp_id,
			obj: $tmp_obj,
			next: $tmp_next
		}
		$datas.push($tmp_dict);
	});

	$next = "";
	$html = "";
	for ($i = 0; $i < $datas.length; $i++) {
		if ($datas[$i].obj === $obj || $datas[$i].obj == '') {
			$datas = $datas.slice(0, $i);
			break;
		}
		$html += "<li data-next='"+$datas[$i].next+"' data-obj='"+$datas[$i].obj+"' data-title='"+$datas[$i].title+"' data-id='"+$datas[$i].id+"'><a href='#'>"+$datas[$i].title+"</a></li>";
		$next = $datas[$i].next;
	}
	if ($action == 'append') {
		if ($breadcrumb_count == 0) {
			$tmp_dict = {
				title: 'Пәндер',
				id: $id,
				obj: $obj,
				next: $title
			};
			$datas.push($tmp_dict);
			$html += "<li data-next='"+$title+"' data-obj='"+$obj+"' data-title='"+$tmp_dict.title+"' data-id='"+$id+"'><a href='#'>"+$tmp_dict.title+"</a></li>";
			$html += "<li data-next='' data-obj='' data-title='"+$title+"' data-id=''><span style='color: #888;'>"+$title+"</span></li>";
		} else {
			$tmp_dict = {
				title: $next,
				id: $id,
				obj: $obj,
				next: $title
			};
			$datas.push($tmp_dict);
			$html += "<li data-next='"+$title+"' data-obj='"+$obj+"' data-title='"+$tmp_dict.title+"' data-id='"+$id+"'><a href='#'>"+$tmp_dict.title+"</a></li>";
			$html += "<li data-next='' data-obj='' data-title='"+$title+"' data-id=''><span style='color: #888;'>"+$title+"</span></li>";
		}
	} else if ($next != '') {
		$html += "<li data-next='' data-obj='' data-title='"+$next+"' data-id=''><span style='color: #888;'>"+$next+"</span></li>";
	}

	$('.materials #breadcrumb').html($html);
	return $datas;
}

function set_content($datas, $obj) {
	$url = "material/loads/" + $obj + ".php?obj="+$obj;
	for ($i = 0; $i < $datas.length; $i++) {
		$url += "&" + $datas[$i].obj + "_id=" + $datas[$i].id;
	}
	set_load('.materials #material-body');
	$('.materials #material-body').load($url);
}

$isDragging = false;
$(document).on('mousedown', '.materials .ui-sortable-handle', function() {
	$isDragging = false;
}).on('mousemove', '.materials .ui-sortable-handle', function() {
	$isDragging = true;
}).on('mouseup', '.materials .ui-sortable-handle', function() {
	if ($isDragging) {
		$isDragging = false;
		$(this).addClass('bg-warning');
		$('.materials .order-actions').removeClass('hide');
	}
});

$(document).on('click', '.change-order', function() {
	$action = $(this).data('action');
	if ($action == 'reset') {
		$('.materials .order-actions').addClass('hide');
		$obj_arr = [];
		$('.materials .sortable').find('li').each(function() {
			$index = $(this).data('order');
			$(this).removeClass("bg-warning");
			$obj_arr[$index] = $(this);
		});
		for ($i = 0; $i < $obj_arr.length; $i++) {
			$('.materials .sortable').append($obj_arr[$i]);
		}
	}
});

$(document).on('click', '.select-materials', function() {
	$dir = $(this).data('dir');
	$subtopic_id = $(this).parents('.material-btn-groups').data('subtopic-id');
	$(this).parents('.material-btn-groups').find('button').removeClass('active');
	$(this).addClass('active');

	$('#material-content').load('material/' + $dir);
});