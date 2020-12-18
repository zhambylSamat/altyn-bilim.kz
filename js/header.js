$(document).on('click','.sign-in',function(){
	$('#float-login').slideToggle();
});
$(document).on("click",'.cover, .close',function(){
	$('#float-login').slideUp();
});