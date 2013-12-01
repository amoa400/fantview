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
});

// 题目预览类
function QuesPreview() {
	var _this = this;
	var id;
	var rank;
	var popover;
	var container;
	var ques;
	
	var init = function() {
		$('.ques_preview_t').live('click', preview);
		//$('.ques_preview').live('click', previewClick);
	}
	
	// 点击预览题目
	var preview = function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
		
		id = $(this).attr('md_id');
		rank = parseInt($(this).attr('md_rank'));
		
		generateContainer();
		popover = new FTV_Popover('.ques_preview_container', {mask:false});
		generateQues();
		ques.show();
		ques.attr('md_show', 1);
	}
	
	// 生成容器
	var generateContainer = function() {
		container = $('.page_content').find('.ques_preview_container');
		if (container.length != 0) return;

		container = $('<div></div>');
		container.addClass('ques_preview_container');
		container.css('width', $('body').css('width'));
		container.css('height', $('body').css('height'));
		container.css('opacity', 0);
		container.click(closeContainer);
		container.appendTo('.page_content');

		// 生成等待图
		//container.append('<div class="load"><img src="/image/loading3.gif"></div>');
		//container.find('.load').css('top', parseInt((parseInt(container.css('height')) - parseInt(container.find('.load').css('height'))) * 2 / 5));
		//container.find('.load').css('left', parseInt((parseInt(container.css('width')) - parseInt(container.find('.load').css('width'))) / 2));
	}
	
	// 生成题目预览
	var generateQues = function(callback) {
		ques = container.find('.ques_preview_' + id);
		if (ques.length != 0) {
			ques.find('.info .title span').html('(' + rank + '/' + $('.question_count').html() + ')');
			// 回调函数
			if(typeof callback == 'function')
				callback();
			return;
		}
		fetchQues(callback);
	}
	
	// 获取题目
	var fetchQues = function(callback) {
		$.get('/question/getDetail', {id:id}, function(res) {
			res = eval('(' + res + ')');
			// 生成DIV
			ques = $('<div></div>');
			ques.addClass('ques_preview');
			ques.addClass('corner2');
			ques.addClass('shadow1w');
			ques.addClass('ques_preview_' + id);
			ques.attr('md_show', 1);
			ques.html('<div class="close"><i class="fa fa-times"></i></div><div class="info"></div><div class="content"></div>');
			ques.click(previewClick);
			ques.appendTo(container);
			
			// 基本信息
			var s = '';
			var info = ques.find('.info');
			s += '<div class="title">题目预览';
			if ($('.question_count').length != 0)
				s += ' <span>(' + rank + '/' + $('.question_count').html() + ')</span>';
			s += '</div>';
			s += '<div class="item">题目类型：' + res.type + '</div>';
			s += '<div class="item" style="margin-left:25px;">题目分值：' + res.score + '</div>';
			s += '<div class="item right next" onselectstart="return false">下一题&nbsp;<i class="fa fa-chevron-right"></i></div>';
			s += '<div class="item right prev" onselectstart="return false" style="margin-right:20px;"><i class="fa fa-chevron-left"></i>&nbsp;上一题</div>';
			s += '<div class="clear"></div>';
			info.html(s);
			info.find('.right').click(changeQues);
				
			// 详细信息
			var content = ques.find('.content');
			content.css('height', parseInt(parseInt(ques.css('height')) - parseInt(info.css('height'))));
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
				if (res.detail.input != null && res.detail.input != '') {
					s += '<div class="tt">输入数据</div>';
					s += '<div class="ct">' + res.detail.input + '</div>';
				}
				if (res.detail.output != null && res.detail.output != '') {
					s += '<div class="tt">输出数据</div>';
					s += '<div class="ct">' + res.detail.output + '</div>';
				}
				if (res.detail.s_input != null && res.detail.s_input != '') {
					s += '<div class="tt">样例输入</div>';
					s += '<div class="ct">' + res.detail.s_input + '</div>';
				}
				if (res.detail.s_output != null && res.detail.s_output != '') {
					s += '<div class="tt">样例输出</div>';
					s += '<div class="ct">' + res.detail.s_output + '</div>';
				}
				if (res.detail.hint != null && res.detail.hint != '') {
					s += '<div class="tt">提示</div>';
					s += '<div class="ct">' + res.detail.hint + '</div>';
				}
				s += '<div class="tt">时间限制</div>';
				s += '<div class="ct">' + res.detail.time_limit + ' MS</div>';
				s += '<div class="tt">空间限制</div>';
				s += '<div class="ct">' + res.detail.memory_limit + ' KB</div>';
			}
			content.html(s);
			
			// 高度大小
			ques.css('top', parseInt((parseInt(container.css('height')) - parseInt(ques.css('height'))) * 2 / 5));
			ques.css('left', parseInt((parseInt(container.css('width')) - parseInt(ques.css('width'))) / 2));
			
			// 事件
			ques.find('.close').click(closeContainer);
			
			// 回调函数
			if(typeof callback == 'function')
				callback();
		});
	}

	// 关闭容器
	var closeContainer = function() {
		popover.close(function() {
			ques.hide();
			ques.attr('md_show', 0);
		});
	}
	
	// 点击题目界面
	var previewClick = function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
	}
	
	// 切换题目
	var changeQues = function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
			
		var cntRank;
		if ($(this).attr('class').indexOf('next') >= 0)
			cntRank = rank + 1;
		else
			cntRank = rank - 1;
		if ($('.ques_preview_t[md_rank="' + cntRank + '"]').length == 0) return;
		
		// 进度条
		container.append('<div class="load corner5"><img src="/image/loading3.gif"></div>');
		var load = container.find('.load');
		load.css('top', parseInt((parseInt(ques.css('top')) + parseInt(ques.css('height'))) / 2));
		load.css('left', parseInt((parseInt(container.css('width')) - parseInt(load.css('width'))) / 2));
		
		var oldQues = ques;
		id = $('.ques_preview_t[md_rank="' + cntRank + '"]').attr('md_id');
		rank = cntRank;
		generateQues(function() {
			load.remove();
			ques.show();
			ques.attr('md_show', 1);
			oldQues.hide();
			oldQues.attr('md_show', 0);
		});
		
		//$('.ques_preview_t[md_rank="' + cntRank + '"]').click();
	}
	
	init();
}

/* 触发所有类 */
$().ready(function() {
	QuesPreview();
});
