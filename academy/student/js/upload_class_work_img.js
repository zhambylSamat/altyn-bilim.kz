$(document).on('click', '.submit-class-work-btn', function() {
	$lp_id = $(this).data('lp-id');
	$gscwsf_infos = $(this).parents('.material-box').find('.gscwsf-infos');
	$html = "";
	$.each($gscwsf_infos, function() {
		$gscwsf_id = $(this).data('gscwsf-id');
		$file_link = $(this).data('gscwsf-file-link');
		$html += "<div class='class-work-solve-box'>";
		// class-work-img-modal
			$html += "<center><div class='class-work-solve-img' data-toggle='modal' data-target='#class-work-img-modal' data-file-link='"+$file_link+"'>";
				$html += "<img src='"+$file_link+"'>";
			$html += "</div></center>";
			$html += "<center><i class='uploaded-text'>Сурет жүктелді</i></center>";
			$html += "<span class='class-work-solve-img-controls'>";
				$html += "<i class='remove fas fa-trash-alt' data-gscwsf-id='"+$gscwsf_id+"' data-lp-id='"+$lp_id+"'></i>";
				$html += "<i class='expand fas fa-expand' data-toggle='modal' data-target='#class-work-img-modal' data-file-link='"+$file_link+"'></i>";
			$html += "</span>";
		$html += "</div>";
	});

	$('#modal-lp-id').val($lp_id);
	$('#class-work-submit-form').find('.uploaded-imgs').html($html);
});

$(document).on('click', '.class-work-solve-img, .class-work-solve-img-controls .expand', function() {
	$file_link = $(this).data('file-link');

	$('#class-work-img-modal .modal-body').html("<img src='"+$file_link+"' style='width: 100%;'></img>");
});

$(document).on('click', '.class-work-solve-img-controls .remove', function() {
	$gscwsf_id = $(this).data('gscwsf-id');
	$lp_id = $(this).data('lp-id');
	$selected_element = $(this);
	if (confirm('Таңдаған үй жұмысының шығару жолын өшіруге келісесіңба?')) {
		$.ajax({
			url: 'lesson/controller.php?remove-submitted-class-work&gscwsf_id='+$gscwsf_id+'&lp_id='+$lp_id,
			type: 'GET',
			cache: false,
			beforeSend: function() {
				set_load($selected_element.parents('.class-work-solve-box'));
			},
			success: function($data) {
				console.log($data);
				$json = $.parseJSON($data);
				remove_load();
				if ($json.success) {
					$selected_element.parents('.class-work-solve-box').remove();
					$elem = $('.gscwsf-infos');
					$.each($elem, function() {
						if ($(this).data('gscwsf-id') == $gscwsf_id) {
							$(this).remove();
						}
					});
				}
			}
		});
	}
});

$(document).on('change', '.upload-img-input', function(){
	$img_fake_path = $(this).val();
	$file_extentions = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];
	$progress = $(this).parents('.upload-img-box').find('.percent');
	$lesson_progress_id = $('#modal-lp-id').val();
	$current_element = $(this);
	if ($img_fake_path != '') {
		$($(this).prop('files')).each(function($i, $v) {
			if ($v.size < 524288000) {
				if ($file_extentions.includes($v.name.split('.').pop()) == 1) {
					$form_data = new FormData();
					$form_data.append('class_work_img', $v);
					$form_data.append('lesson_progress_id', $lesson_progress_id);

					$.ajax({
						url: 'lesson/controller.php?upload_class_work_img',
						type: 'POST',
						data: $form_data,
						processData: false,
						contentType: false,
						cache: false,
						xhr: function() {
			                var xhr = new window.XMLHttpRequest();
			                xhr.upload.addEventListener("progress", function(evt) {
			                    if (evt.lengthComputable) {
			                        var percentComplete = parseInt((evt.loaded / evt.total) * 100)+'%';
			                        $progress.html(percentComplete);
			                    }
			                }, false);
			                return xhr;
			            },
						beforeSend: function() {
							$percentVal = '0%';
							$progress.html($percentVal);
						},
						// uploadProgress: function(event, position, total, percentComplete) {
						// 	$percentVal = percentComplete+'%';
						// 	$progress.html($percentVal);
						// },
						complete: function(xhr) {
							$progress.html('');
						},
						success: function($data) {
							console.log($data);
							$current_element.val("");
							$json = $.parseJSON($data);
							if ($json.success) {
								$lp_id = $json.lesson_progress_id;
								$gscwsf_id = $json.group_student_class_work_submit_file_id;
								$file_link = $json.file_link;
								$html = "";
								$html += "<div class='class-work-solve-box'>";
								// class-work-img-modal
									$html += "<center><div class='class-work-solve-img' data-toggle='modal' data-target='#class-work-img-modal' data-file-link='"+$file_link+"'>";
										$html += "<img src='"+$file_link+"'>";
									$html += "</div></center>";
									$html += "<center><i class='uploaded-text'>Сурет жүктелді</i></center>";
									$html += "<span class='class-work-solve-img-controls'>";
										$html += "<i class='remove fas fa-trash-alt' data-gscwsf-id='"+$gscwsf_id+"' data-lp-id='"+$lp_id+"'></i>";
										$html += "<i class='expand fas fa-expand' data-toggle='modal' data-target='#class-work-img-modal' data-file-link='"+$file_link+"'></i>";
									$html += "</span>";
								$html += "</div>";

								$('#class-work-submit-form').find('.uploaded-imgs').append($html);

								$.each($('.submit-class-work-btn'), function() {
									if ($(this).data('lp-id') == $lp_id) {
										$html = "<input type='hidden' class='gscwsf-infos' data-gscwsf-id='"+$gscwsf_id+"' data-gscwsf-file-link='"+$file_link+"'>";
									}
									$(this).parents('.material-box').append($html);
								});
							}
						}
					});
				} else {
					error_alert_message('Жүктелген суреттің типі "jpeg", "jpg", "png" болу керек.');
				}
			} else {
				error_alert_message('Жүктелген суреттің салмағы 5 мб (мега бит) тан көп болмауы керек');
			}
		});
	}
});

function error_alert_message($message) {
	Swal.fire({
		width: '50em',
		title: $message,
		icon: 'error'
	});
}
