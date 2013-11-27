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
	$('.ques_preview_t').live('click', function() {
		var id = $(this).attr('md_id');
		
		// 生成DIV
		if ($('.page_content').find('.ques_preview_' + id).length == 0) {
			var div = $('<div></div>');
			div.addClass('ques_preview');
			div.addClass('corner5');
			div.addClass('ques_preview_' + id);
			div.html('<div class="info"></div><div class="content"></div><div class="clear"></div>');
			div.appendTo('.page_content');
		}	
		
		// 弹出框
		var ftvPopover = FTV_Popover('.ques_preview_' + id);
		
		// 获取题目
		$.get('/question/getDetail', {id:id}, function(res) {
			// 基本信息
			var s = '';
			var info = div.find('.info');
			s += '<div class="tt">题目类型</div>';
			s += '<div class="ct">' + res.type + '</div>';
			s += '<div class="tt">题目分值</div>';
			s += '<div class="ct">' + res.score + '</div>';
			info.html(s);
			// 非程序题
			if (res.type_id != 4) {
				div.find('.content').html(res.detail.desc);
			} else {
				div.find('.content').html(res.detail.desc);
			}
		});
	});
});