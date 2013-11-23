function FTV_Question() {
	var _this = this;
	var testId;
	var hasLoad;
	var editor;
	var isComRun;
	
	// 初始化
	var init = function() {
		testId = $('.g_test_id').html();
		hasLoad = new Array();
		editor = new Array();
		isComRun = false;
		$('.ques_load').click(_this.load);
		$('.question .lang').live('change', _this.changeLang);
		$('.question .com_run').live('click', _this.comRun);
		$('.question .view_res').live('click', _this.viewRes);
	}
	
	// 加载题目
	_this.load = function(r) {
		var rank = $(this).attr('md_rank');
		if (rank == null) rank = r;
		var thisObj = $('.ques_load[md_rank="' + rank + '"]');
		var id = thisObj.attr('md_id');		
		$('.navi .item').removeClass('active');
		thisObj.addClass('active');
		$('.question .item').hide();
		$('.question .q' + id).show();
		
		// 加载题目
		if (hasLoad[id] == null)
			hasLoad[id] = 1;
		else
			return;
		$('.question .load').show();
		
		$.post('/attend/getQues/' + id, {test_id : testId, question_id : id},
			function(res) {
				$('.question .load').hide();
				var div = $('<div></div>');
				div.addClass('item');
				div.addClass('q' + res.base.id);
				div.appendTo('.question');
				
				// 名称
				if (res.base.type_id == 4)
					div.append('<div class="name">' + res.detail.name + '</div>');

				// 描述
				div.append('<div class="desc">' + res.detail.desc + '</div>');
				
				// 作答
				if (res.base.type_id == 4) {
					var answer = $('<div></div>');
					answer.addClass('answer');
					// 语言
					var s = '<div class="lang" md_id="' + res.base.id + '"><select class="form-control">';
					var lang = $('.g_lang').html().split('|');
					for (var i = 0; i < lang.length; i++)
					if (lang[i] != '')
						s += '<option value="' + lang[i] + '">' + lang[i] + '</option>';
					s += '<select></div>';
					answer.append(s);
					// 编辑器
					answer.append('<div id="editor_' + res.base.id + '" class="editor">//请在此写入您的代码</div>');
					// 运行结果
					answer.append('<div class="result" md_id="' + res.base.id + '"><textarea></textarea></div>');
					// 语言
					answer.append('<div class="button"><span class="it btn btn-success com_run" md_id="' + res.base.id + '">编译运行</span><span class="it btn btn-primary view_res" md_id="' + res.base.id + '">查看结果</span></div>');
					
					answer.appendTo(div);
					
					editor[res.base.id] = ace.edit('editor_' + res.base.id);
					editor[res.base.id].setTheme('ace/theme/textmate');
					editor[res.base.id].getSession().setMode('ace/mode/c_cpp');
					editor[res.base.id].setFontSize(14);
				}
			}
		);
	}
	
	// 更换语言
	_this.changeLang = function() {
		var lang = $(this).find('option:selected').text();
		var id = $(this).attr('md_id');
		var langStr = '';
		switch(lang) {
			case 'C':
			case 'C++':
				langStr = 'c_cpp';
				break;
			case 'C#':
				langStr = 'csharp';
				break;
				case 'Objective C':
				langStr = 'objectivec';
				break;
			default:
				langStr = lang.toLowerCase();
		}
		editor[id].getSession().setMode('ace/mode/' + langStr);
	}
	
	// 编译运行
	_this.comRun = function() {
		if (isComRun) return;
		else isComRun = true;
		
		var id = $(this).attr('md_id');
		$('.question .com_run').attr('disabled', 'disabled');
		$('.question .result[md_id="' + id + '"] textarea').val('编译运行中...');
		$('.question .view_res[md_id="' + id + '"]').click();
	
		var lang = $('.question .lang[md_id="' + id + '"]').find('option:selected').text();
		var code = editor[id].getValue();
		$.post('/attend/comRun', {test_id : testId, question_id : id, lang : lang, code : code},
			function(res) {
				var resStr = '';
				// 处理结果			
				if (res.charAt(0) == 'C' && res.charAt(1) == 'E') {
					resStr += '编译错误\r\n\r\n';
					for (var i = 3; i < res.length; i++)
						resStr += res.charAt(i);
				}
				else {
					resStr += '编译成功\r\n';
					var cases = res.split('\|');
					resStr += '共测试了' + (cases.length - 1) + '个测试数据（并非全部测试数据）\r\n\r\n';
					for (var i = 0; i < cases.length; i++) {
						if (cases[i] == '') continue;
						var info = cases[i].split(',');
						resStr += '运行结果：' + info[3] + '\r\n';
						resStr += '运行时间：' + info[1] + 'MS\r\n';
						resStr += '使用内存：' + info[2] + 'KB\r\n';
						resStr += '\r\n';
					}
				}
				$('.question .result[md_id="' + id + '"] textarea').val(resStr);
				$('.question .com_run').removeAttr('disabled');
				isComRun = false;
			}
		);
	}
	
	// 查看运行结果
	_this.viewRes = function() {
		var id = $(this).attr('md_id');
		
		if ($(this).html() == '查看结果') {
			$('#editor_' + id).hide();
			$('.question .result[md_id="' + id + '"]').show();
			$(this).html('编辑代码');
		} else {
			$('#editor_' + id).show();
			$('.question .result[md_id="' + id + '"]').hide();
			$(this).html('查看结果');
		}
	}
	
	init();
}

$().ready(function() {
	window.ftvQuestion = new FTV_Question();
	ftvQuestion.load(1);
});