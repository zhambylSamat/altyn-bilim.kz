$materials_for_link = {};

$(document).on('click', '#open-create-link-form', function() {
	$(this).addClass('hide');
	$('#create-link-content').removeClass('hide');
});

$(document).on('click', '#close-create-link-form', function() {
	$("#open-create-link-form").removeClass('hide');
	$('#create-link-content').addClass('hide');
});

$(document).on('click', '#materials-for-link-btn', function() {
	$('#materials-for-link .modal-body').html('<center><h2>Загрузка...</h2></center>');
	$('#materials-for-link .modal-body').load('generate_link/components/choose_material.php', function() {
		fill_materials_gap();
	});
});

$(document).on('click', '.subject-title', function() {
	$(this).parents('table').find('.topics').fadeToggle('fast');
	$(this).parents('table').find('.subtopics').hide();
});

$(document).on('click', '.topic-title', function() {
	$topic_id = $(this).data('id');
	$(this).parents('table').find('.subtopic-'+$topic_id).fadeIn('fast');
	$(this).hide();
});

$(document).on('click', '.subtopic-title', function() {
	$topic_id = $(this).data('id');
	$current_elem = $(this);
	$(this).parents('table').find('.subtopic-'+$topic_id).fadeOut('fast', function() {
		$current_elem.parents('table').find('.topic-title[data-id='+$topic_id+']').show();
	});
});

$(document).on('change', '.material-elem', function() {
	$material_type = $(this).val();
	$material_info = $(this).parents('tr');
	$subject_id = $material_info.find("input[name=subject-id]").val();
	$subject_title = $material_info.find("input[name=subject-title]").val();
	$topic_id = $material_info.find("input[name=topic-id]").val();
	$topic_title = $material_info.find("input[name=topic-title]").val();
	$subtopic_id = $material_info.find("input[name=subtopic-id]").val();
	$subtopic_title = $material_info.find("input[name=subtopic-title]").val();
	if ($(this).prop('checked')) {
		$(this).parents('td').addClass('success');
		set_materials_for_link($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title, $material_type);
	} else {
		$(this).parents('td').removeClass('success');
		remove_materials_for_link($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title, $material_type)
	}
});


function fill_materials_gap() {
	$('.material-info').each(function() {
		$subject_id = $(this).find("input[name=subject-id]").val();
		$subject_title = $(this).find("input[name=subject-title]").val();
		$topic_id = $(this).find("input[name=topic-id]").val();
		$topic_title = $(this).find("input[name=topic-title]").val();
		$subtopic_id = $(this).find("input[name=subtopic-id]").val();
		$subtopic_title = $(this).find("input[name=subtopic-title]").val();
		$which_selected = check_for_material_selection($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title);

		$tr = $(this).parents('tr');
		if ($which_selected['tv']) {
			$tr.find('.material-elem[value=tutorial_video]').prop('checked', true).change();
		}
		if ($which_selected['td']) {
			$tr.find('.material-elem[value=tutorial_document]').prop('checked', true).change();
		}
		if ($which_selected['ev']) {
			$tr.find('.material-elem[value=end_video]').prop('checked', true).change();
		}
		if ($which_selected['mt']) {
			$tr.find('.material-elem[value=material_test]').prop('checked', true).change();
		}
	});
}

function check_for_material_selection($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title) {
	$result = {'tv': false, 'td': false, 'ev': false, 'mt': false};
	if ($materials_for_link[$subject_id] !== undefined) {
		if ($materials_for_link[$subject_id]['topics'][$topic_id] !== undefined) {
			if ($materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id] !== undefined) {
				$result = $materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id];
			}
		}
	}
	return $result;
}


function set_materials_for_link($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title, $material_type) {
	if ($materials_for_link[$subject_id] === undefined) {
		$materials_for_link[$subject_id] = {'title': $subject_title, 'topics': {}};
	}
	if ($materials_for_link[$subject_id]['topics'][$topic_id] === undefined) {
		$materials_for_link[$subject_id]['topics'][$topic_id] = {'title': $topic_title,
																'subtopics': {}};
	}
	if ($materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id] === undefined) {
		$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id] = {'title': $subtopic_title,
																							'tv': false,
																							'td': false,
																							'ev': false,
																							'mt': false};
	}
	switch ($material_type) {
		case 'tutorial_video':
			$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['tv'] = true;
			break;
		case 'tutorial_document':
			$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['td'] = true;
			break;
		case 'end_video':
			$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['ev'] = true;
			break;
		case 'material_test':
			$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['mt'] = true;
			break;
	}
	set_selected_materials_table();
}

function remove_materials_for_link($subject_id, $subject_title, $topic_id, $topic_title, $subtopic_id, $subtopic_title, $material_type) {
	if ($materials_for_link[$subject_id] !== undefined) {
		if ($materials_for_link[$subject_id]['topics'][$topic_id] !== undefined) {
			if ($materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id] !== undefined) {
				switch ($material_type) {
					case 'tutorial_video':
						$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['tv'] = false;
						break;
					case 'tutorial_document':
						$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['td'] = false;
						break;
					case 'end_video':
						$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['ev'] = false;
						break;
					case 'material_test':
						$materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['mt'] = false;
						break;
				}
				if ($materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['tv'] == false
					&& $materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['td'] == false
					&& $materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['ev'] == false
					&& $materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id]['mt'] == false) {

					delete $materials_for_link[$subject_id]['topics'][$topic_id]['subtopics'][$subtopic_id];

					if (Object.keys($materials_for_link[$subject_id].topics[$topic_id].subtopics).length == 0) {
						delete $materials_for_link[$subject_id]['topics'][$topic_id];
					}

					if (Object.keys($materials_for_link[$subject_id].topics).length == 0) {
						delete $materials_for_link[$subject_id];
					}
				}
			}
		}
	}
	set_selected_materials_table();
};


function set_selected_materials_table() {
	$html = '';
	$.each($materials_for_link, function($subject_id, $subject) {
		$html += "<table class='table table-bordered'>";
		$html += "<tr class='info'><td colspan='5'><center><b>"+$subject.title+"</b></center></td></tr>";
		$.each($subject.topics, function($topic_id, $topic) {
			$html += "<tr>";
			$html += "<td rowspan='"+(Object.keys($topic.subtopics).length)+"'>"+$topic.title;
			$count = 0;
			$.each($topic.subtopics, function($subtopic_id, $subtopic) {
				if ($count > 0) {
					$html += "<tr>";
				}
				$html += "<td>";
					$html += $subtopic.title;
					if ($subtopic.tv == true) {
						$html += "<br><b>Тақырыптық видео</b>";
					}
					if ($subtopic.td == true) {
						$html += "<br><b>Тапсырмалар</b>";	
					}
					if ($subtopic.ev == true) {
						$html += "<br><b>Шығару жолы</b>";	
					}
					if ($subtopic.mt == true) {
						$html += "<br><b>Тест</b>";
					}
				$html += "</td>";
				$html += "</tr>";
				$count++;	
			});
		});
		$html += "</table>";
	});

	$('#material-link-content').html($html);
}

$(document).on('submit', '#create-link-form', function($e) {
	$e.preventDefault();

	if (Object.keys($materials_for_link).length == 0) {
		$('#material-link-content-message').text('Кем дегенде бір видео немесе материал таңдалуы керек!');
	} else {
		$this = $(this);
		$access_hours = $(this).find('input[name=access-hours]').val();
		$comment = $(this).find('textarea[name=comment]').val();
		$subtopics_and_materials_type = [];
		$.each($materials_for_link, function($subject_id, $subject) {
			$.each($subject.topics, function($topic_id, $topic) {
				$.each($topic.subtopics, function($subtopic_id, $subtopic) {
					$tmp = {'id': $subtopic_id,
							'tv': $subtopic.tv,
							'td': $subtopic.td,
							'ev': $subtopic.ev,
							'mt': $subtopic.mt};
					$subtopics_and_materials_type.push($tmp);
				});
			});
		});
		$subtopics_and_materials_type = JSON.stringify($subtopics_and_materials_type);		$form_data = new FormData();
		$form_data.append("comment", $comment);
		$form_data.append("access_hours", $access_hours);
		$form_data.append('datas', $subtopics_and_materials_type);
		$.ajax({
			url: 'generate_link/controller.php?create-material-link',
			type: 'POST',
			data: $form_data,
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function() {
				set_load('body');
			},
			success: function($data) {
				remove_load();
				$materials_for_link = {};
				$json = $.parseJSON($data);
				if ($json.success) {
					$.ajax({
						url: 'generate_link/components/link_list.php',
						type: 'GET',
						async: false,
						success: function($elem) {
							$('.material-links').load('generate_link/components/create_link.php', function() {
								$('.material-links').append($elem);
							});
						}
					});
				}
			}
		});
	}
});


$(document).on('click', '.copy-material-link-btn', function() {
	$elem = $(this).parents('tr').find('.material-link');
	$text_area = document.createElement('textarea');
	$text_area.value = $elem.text();
	document.body.appendChild($text_area);
	$text_area.select();
	document.execCommand('Copy');
	$text_area.remove();
	alert('Ссылка скопировано!');
});