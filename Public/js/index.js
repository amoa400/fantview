$().ready(function() {

	// ÓÊÏä
	$('.reg button').click(function() {
		var id = $(this).parent().parent().parent().parent().attr('md_id');
		var val = $('.reg[md_id="' + id + '"] input').val();
		location.href = "/index/register/email/" + $.base64.btoa(val);
	});
	$('.reg input').keydown(function(e) {
		if (e.keyCode == 13) {
			var id = $(this).parent().parent().parent().attr('md_id');
			$('.reg[md_id="' + id + '"] button').click();
		}
	});
	
	
	
});