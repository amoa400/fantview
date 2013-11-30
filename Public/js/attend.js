function sleep(n) {
	var start=new Date().getTime();
	while(true) if(new Date().getTime()-start>n) break;
}
function FTV_Question() {
	var _this = this;
	var testId;
	var hasLoad;
	var editor;
	var isComRun;
	var answerArr;
	var time;
	var quesCount;
	var curRank;
	
	// 初始化
	var init = function() {
		testId = $('.g_test_id').html();
		hasLoad = new Array();
		editor = new Array();
		answerArr = new Array();
		time = $('.time').html();
		_this.updateTime();
		isComRun = false;
		quesCount = $('.g_quesCount').html();
		$('.ques_load').click(_this.save);
		$('.ques_load').click(_this.load);
		$('.question .lang').live('change', _this.changeLang);
		$('.question .com_run').live('click', _this.comRun);
		$('.question .view_res').live('click', _this.viewRes);
		$('.submit_btn').live('click', _this.submit);
		$('.save').live('click', _this.save);
		$('.next').live('click', _this.next);
		$('.prev').live('click', _this.prev);
		setInterval(_this.save, 5000);
		setInterval(_this.updateTime, 1000);
	}
	
	// 加载题目
	_this.load = function(r) {
		var rank = $(this).attr('md_rank');
		if (rank == null) rank = r;
		curRank = rank;
		var thisObj = $('.ques_load[md_rank="' + rank + '"]');
		var id = thisObj.attr('md_id');		
		$('.navi .item').removeClass('active');
		thisObj.addClass('active');
		$('.question .ques_item').hide();
		$('.question .q' + id).show();
		
		// 加载题目
		if (hasLoad[id] == 1) {
			answerArr[id]['start_time'] = (new Date()).valueOf();
			answerArr[id]['last_tot_time'] = answerArr[id]['tot_time'];
			answerArr[id]['tot_time'] = 0;
			return;
		}		
		$('.question .load').show();
		
		$.post('/attend/getQues/', {test_id : testId, question_id : id},
			function(res) {
				res = eval('('+res+')');
				if (res.jumpUrl != null) location.href = res.jumpUrl;
				hasLoad[res.base.id] = 1;
				$('.question .load').hide();
				answerArr[res.base.id] = new Array();
				answerArr[res.base.id]['type'] = res.base.type_id;
				answerArr[res.base.id]['content'] = "";
				answerArr[res.base.id]['lang'] = "";
				answerArr[res.base.id]['start_time'] = (new Date()).valueOf();
				answerArr[res.base.id]['last_tot_time'] = res.tot_time*1000;
				answerArr[res.base.id]['tot_time'] = 0;
				var div = $('<div></div>');
				div.addClass('ques_item');
				div.addClass('q' + res.base.id);
				div.appendTo('.question');
				
				// 题目
				if (res.base.type_id == 4) {
					div.append('<div class="name">' + res.detail.name + '</div>');
					div.append('<div class="desc item"><div class="title">Description</div>' + res.detail.desc + '</div>');
					if (res.detail.input)
						div.append('<div class="input item"><div class="title">Input Format</div>' + res.detail.input + '</div>');
					if (res.detail.output)
						div.append('<div class="output item"><div class="title">Output Format</div>' + res.detail.output + '</div>');
					if (res.detail.s_input)
						div.append('<div class="s_input item"><div class="title">Sample Input</div><pre>' + res.detail.s_input + '</pre></div>');
					if (res.detail.s_output)
						div.append('<div class="s_output item"><div class="title">Sample Output</div><pre>' + res.detail.s_output + '</pre></div>');
					if (res.detail.hint)
						div.append('<div class="hint item"><div class="title">Hint</div>' + res.detail.hint + '</div>');
				}
				else if (res.base.type_id == 1 || res.base.type_id == 2) {
					var t;
					var tit;
					if (res.base.type_id == 1) {
						t = 'radio';
						tit = '单选题';
					}else {
						t = 'checkbox';
						tit = '多选题';
					}
					div.append('<div class="name">' + tit + '</div>');
					div.append('<div class="desc item">' + res.detail.desc + '</div>');
					var answer = $('<div></div>')
					answer.addClass('answer');
					var check;
					for (var i = 0; i < res.detail.option.length; i++) {
						if (res.answer.indexOf(res.detail.option[i][0]) != -1) check = 'checked';
						else check = '';
						var s = '<div class="radio"><label><input name="answer_'+res.base.id+'" type="'+t+'" value="'+res.detail.option[i][0]+'" '+check+'><div class="icon"></div><div class="choice_content">'+res.detail.option[i][1]+'</div></label></div>';
						answer.append(s);
					}
					answer.append('<div class="button"><span class="it btn btn-primary prev" md_id="' + res.base.id + '">上一题</span><span class="it btn btn-primary next" md_id="' + res.base.id + '">下一题</span></div>');
					answer.appendTo(div);
					if (curRank == 1) div.find('.prev').addClass('disabled');
					if (curRank == quesCount) div.find('.next').addClass('disabled');
				}
				else if (res.base.type_id == 3) {
					div.append('<div class="name">简答题</div>');
					div.append('<div class="desc item">' + res.detail.desc + '</div>');
					var answer = $('<div></div>')
					answer.addClass('answer');
					var s = '<div class="answer_area"><textarea class="form-control" rows="9">'+res.answer+'</textarea></div>';
					answer.append(s);
					answer.append('<div class="button"><span class="it btn btn-primary prev" md_id="' + res.base.id + '">上一题</span><span class="it btn btn-primary next" md_id="' + res.base.id + '">下一题</span></div>');
					answer.appendTo(div);
					if (curRank == 1) div.find('.prev').addClass('disabled');
					if (curRank == quesCount) div.find('.next').addClass('disabled');
				}
				
				// 编程作答
				if (res.base.type_id == 4) {
					var answer = $('<div></div>');
					answer.addClass('answer');
					// 语言
					var s = '<div class="lang" md_id="' + res.base.id + '"><select class="form-control">';
					var lang = $('.g_lang').html().split('|');
					for (var i = 0; i < lang.length; i++)
					if (lang[i] != '')
						if (lang[i] == res.lang)
							s += '<option value="' + lang[i] + '" selected>' + lang[i] + '</option>';
						else s += '<option value="' + lang[i] + '">' + lang[i] + '</option>';
					s += '<select></div>';
					answer.append(s);
					// 编辑器
					answer.append('<div id="editor_' + res.base.id + '" class="editor">//请在此写入您的代码</div>');
					// 运行结果
					answer.append('<div class="result" md_id="' + res.base.id + '"><textarea></textarea></div>');
					// 语言
					answer.append('<div class="button"><span class="it btn btn-success com_run" md_id="' + res.base.id + '">编译运行</span><span class="it btn btn-primary view_res" md_id="' + res.base.id + '">查看结果</span><div class="tool"><span class="it btn btn-primary prev" md_id="' + res.base.id + '">上一题</span><span class="it btn btn-primary next" md_id="' + res.base.id + '">下一题</span></div></div>');
					
					answer.appendTo(div);
					editor[res.base.id] = ace.edit('editor_' + res.base.id);
					editor[res.base.id].setTheme('ace/theme/textmate');
					editor[res.base.id].getSession().setMode('ace/mode/c_cpp');
					editor[res.base.id].setFontSize(14);
					if (res.answer) editor[res.base.id].setValue(res.answer);
					answerArr[res.base.id]['content'] = editor[res.base.id].getValue();

					if (curRank == 1) div.find('.prev').addClass('disabled');
					if (curRank == quesCount) div.find('.next').addClass('disabled');
				}
			}
		);
	}

	// 保存答案
	_this.save = function() {
		var id = $(".ques_load.active").attr('md_id');
		if (hasLoad[id] == null) return;
		var nowTime = (new Date()).valueOf();
		var que_type = "";
		var codeLang = "";
		answerArr[id]['tot_time']  = Number(answerArr[id]['last_tot_time']) + Number(nowTime-answerArr[id]['start_time']);
		if (answerArr[id]['type'] == 4) {
			var curAns = editor[id].getValue();
			codeLang = $(".lang[md_id='"+id+"'] select").find('option:selected').text();
			answerArr[id]['content'] = curAns;
			answerArr[id]['lang'] = codeLang;
			que_type = 4;
		}
		else if (answerArr[id]['type'] == 1 || answerArr[id]['type'] == 2) {
			var curAns = "";
			$('.q'+id+' :checked').each(function(){
				curAns += $(this).attr('value');
			});
			answerArr[id]['content'] = curAns;
			que_type = 1;
		}
		else if (answerArr[id]['type'] == 3) {
			var curAns = $('.q'+id+' textarea').val();
			answerArr[id]['content'] = curAns;
			que_type = 3;
		}
		var force;
		if ($(this).hasClass('save')) force = 1;
		else force = 0;
		if (force) {
			$(this).html('正在保存');
			$(this).addClass('disabled');
			$(this).addClass('btn-success');
		}
		var thisObj = $(this);
		$.post('/attend/saveAns/', {test_id: testId, tot_time : parseInt(answerArr[id]['tot_time']/1000), question_id : id, type : que_type, answer : curAns, lang : codeLang}, 
			function(res) {
				res = eval('('+res+')');
				if (res.jumpUrl != null) location.href = res.jumpUrl;
				time = res;
				if (force) {
					thisObj.html('保存成功');
					thisObj.removeClass('disabled');
					setTimeout(
						function(){
							thisObj.html('保存答案');
							thisObj.removeClass('btn-success');
						}, 2000
					);
					//var nextRank = Number($('.ques_load.active').attr('md_rank'))+1;
					//_this.load(nextRank);
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
		if ($('.question .view_res[md_id="' + id + '"]').html() == '查看结果') {
			$('.question .view_res[md_id="' + id + '"]').click();
		}
	
		var lang = $('.question .lang[md_id="' + id + '"]').find('option:selected').text();
		var code = editor[id].getValue();
		$.post('/attend/comRun', {test_id : testId, question_id : id, lang : lang, code : code},
			function(res) {
				res = eval('('+res+')');
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

	// 更新剩余时间
	_this.updateTime = function() {
		var t = time;
		var h = parseInt(t/3600);
		if (h < 10) h = '0'+h;
		t = t-3600*h;
		var m = parseInt(t/60);
		if (m < 10) m = '0'+m;
		t = t-60*m;
		var s = t;
		if (s < 10) s = '0'+s;
		$('.header .right').html('剩余时间：'+h+'小时'+m+'分'+s+'秒');
		time = time-1;
		if (time <= 0) _this.submit();
	}
	
	// 上一题
	_this.prev = function() {
		if (curRank == 1) return;
		$('.ques_load[md_rank="'+(curRank-1)+'"]').click();
	}

	// 下一题
	_this.next = function() {
		if (curRank == quesCount) return;
		$('.ques_load[md_rank="'+(Number(curRank)+1)+'"]').click();
	}
	
	// 提交
	_this.submit = function() {
		_this.save();
		$('form').submit();
	}
	init();
}

$(document).ready(function() {
	window.ftvQuestion = new FTV_Question();
	ftvQuestion.load(1);
	$('.item_list').height($(window).height()-120);
});