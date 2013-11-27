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
		var exist = true;
		
		// 生成DIV
		if ($('.page_content').find('.ques_preview_' + id).length == 0) {
			exist = false;
			var div = $('<div></div>');
			div.addClass('ques_preview');
			div.addClass('ques_preview_' + id);
			div.css('opacity', 0);
			div.css('width', $('body').css('width'));
			div.css('height', $('body').css('height'));
			div.html('<div class="contain corner5 shadow3w"><div class="close"><i class="fa fa-times"></i></div><div class="info"></div><div class="content"></div><div class="clear"></div></div>');
			div.appendTo('.page_content');
			div.find('.contain').css('margin-top', (parseInt($('body').css('height')) - parseInt(div.find('.contain').css('height'))) * 2 / 5);
		}	
		
		// 弹出框
		var ftvPopover = new FTV_Popover('.ques_preview_' + id, {mask:false});
		div.click(function(e) {
			var minx = div.find('.contain').offset().left;
			var miny = div.find('.contain').offset().top;
			var maxx = minx + parseInt(div.find('.contain').css('width'));
			var maxy = miny + parseInt(div.find('.contain').css('height'));
			
			if (e.clientX < minx || e.clientX > maxx || e.clientY < miny || e.clientY > maxy) {
				ftvPopover.close();
			}
		});
		div.find('.close').click(function() {
			ftvPopover.close();
		});
		
		// 获取题目
		if (!exist) {
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
				var content = div.find('.content');
				var contentWidth = parseInt(parseInt(div.find('.contain').css('width')) - parseInt(info.css('width')) );
				content.css('width', contentWidth);
				
				// 详细信息
				s = '';
				if (res.type_id != 4) {
					s += '<div class="tt">题目描述</div>';
					s += '<div class="ct">' + res.detail.desc + '</div>';
					if (res.type_id == 1 || res.type_id == 2) {
						s += '<div class="tt">选项内容</div>';
						for(var i = 0; i < res.detail.options.length; i++) {
							s += '<div class="ct"><span class="correct';
							if (res.type_id == 1 && res.detail.answer == String.fromCharCode(65 + i))
								 s += ' correct_bg';
							if (res.type_id == 2 && res.detail.answer.indexOf(String.fromCharCode(65 + i)) >= 0)
								 s += ' correct_bg';
							s += '"></span>' + String.fromCharCode(65 + i) + '、' + res.detail.options[i] + '</div>';
						}
					}
				} else {
					s += '<div class="tt">题目名称</div>';
					s += '<div class="ct">' + res.detail.name + '</div>';
					s += '<div class="tt">题目描述</div>';
					s += '<div class="ct">' + res.detail.desc + '</div>';
					if (res.detail.input != null) {
						s += '<div class="tt">输入数据</div>';
						s += '<div class="ct">' + res.detail.input + '</div>';
					}
					if (res.detail.output != null) {
						s += '<div class="tt">输出数据</div>';
						s += '<div class="ct">' + res.detail.output + '</div>';
					}
					if (res.detail.s_input != null) {
						s += '<div class="tt">样例输入</div>';
						s += '<div class="ct">' + res.detail.s_input + '</div>';
					}
					if (res.detail.s_output != null) {
						s += '<div class="tt">样例输出</div>';
						s += '<div class="ct">' + res.detail.s_output + '</div>';
					}
					if (res.detail.hint != null) {
						s += '<div class="tt">提示</div>';
						s += '<div class="ct">' + res.detail.hint + '</div>';
					}
					s += '<div class="tt">时间限制</div>';
					s += '<div class="ct">' + res.detail.time_limit + ' KB</div>';
					s += '<div class="tt">空间限制</div>';
					s += '<div class="ct">' + res.detail.memory_limit + ' MS</div>';
				}
				content.html(s);
			});
		}
	});
});