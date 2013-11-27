$().ready(function() {
	$('.header .user').click(function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
		if ($('.user_menu').css('display') == 'none')
			$('.user_menu').show();
		else
			$('.user_menu').hide();
	});
	
	$('.user_menu').click(function() {
		$('.user_menu').hide();
	});
	
	// 题目预览
	// 题目显示
	$('.ques_preview').live('click', function() {
		//alert($(this).attr('md_id'));
		var id = $(this).attr('md_id');
		if ($('.page_content').find('.ques_preview_' + id).length == 0) {
			var div = $('<div></div>');
			div.addClass('ques_preview_' + id);
			div.appendTo('.page_content');
		}
	});
});