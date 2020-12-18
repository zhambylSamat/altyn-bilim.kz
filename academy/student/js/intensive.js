$(document).on('click', '.set-intensive-course', function() {
	$group_student_id = $(this).data('group-student-id');
	if (confirm("Are you shure to set selected group to intensive?")) {
		$.ajax({
			url: "intensive/controller.php?set_group_to_intensive&group_student_id="+$group_student_id,
			type: "GET",
			beforeSend: function() {
				set_load('body');
			},
			success: function($data) {
				remove_load();
				console.log($data);
				$json = $.parseJSON($data);
				$('#intensive-group-list').load('intensive/components/student_group_list.php');
			}
		});
	}
});

$(document).on('click', '.unset-intensive-course', function() {
	$group_student_id = $(this).data('group-student-id');
	if (confirm("Are you shure to unset selected group to intensive?")) {
		$.ajax({
			url: "intensive/controller.php?unset_group_to_intensive&group_student_id="+$group_student_id,
			type: "GET",
			beforeSend: function() {
				set_load('body');
			},
			success: function($data) {
				remove_load();
				console.log($data);
				$json = $.parseJSON($data);
				$('#intensive-group-list').load('intensive/components/student_group_list.php');
			}
		});
	}
});

function render_intensive_course_video_instruction() {
	$link = 'https://vimeo.com/489744429';
	// $link = 'https://vimeo.com/481386833';
	$width = $('#intensive-course-instruction-video').width();
	if ($('body').width() > 768) {
		$width = $width*0.7;
	}
	$.ajax({
    	url: "https://vimeo.com/api/oembed.json?url="+$link+'&width='+$width,
		type: "GET",
		beforeSend:function(){
			$('#vimeo-content').html("<center>Загрузка...</center>");
		},
		success: function(data){
			$('#lll').css('display','none');
			$('#intensive-course-instruction-video').html('<center>'+data.html+'</center>');
			
			if ($('body').width() > 768) {
				// $('.payment-instruction-vimeo-video').find('iframe').attr('height', '400');
				$('#intensive-course-instruction-video').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 4%;');
			} else {
				$('#intensive-course-instruction-video').find('iframe').attr('style', 'border: 1px solid lightgray; border-radius: 5px; margin-top: 10%;');
			}
	    },
	  	error: function(dataS) 
    	{
    		console.log(dataS);
    	} 	     
   	});
}