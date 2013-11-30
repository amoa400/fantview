/*
	基本类
*/
function FTV_Common() {
	// 变量
	var _this = this;
	
	// 初始化函数
	var init = function() {
		$('body').click(blurClick);
	}
	
	// 失焦点击
	var blurClick = function(e) {
		if ($('.ftv_popbox').length != 0) {
			var inBox = false;
			if (e.clientX > $('.ftv_popbox').offset().left && e.clientX < $('.ftv_popbox').offset().left + parseInt($('.ftv_popbox').css('width')) && e.clientY > $('.ftv_popbox').offset().top && e.clientY < $('.ftv_popbox').offset().top + parseInt($('.ftv_popbox').css('height')))
				inBox = true;
			if (!inBox) {
				$('.ftv_popbox').remove();
			}
		}
		if ($('.ftv_popbox_h').length != 0) {
			var inBox = false;
			if (e.clientX > $('.ftv_popbox_h').offset().left && e.clientX < $('.ftv_popbox_h').offset().left + parseInt($('.ftv_popbox_h').css('width')) && e.clientY > $('.ftv_popbox_h').offset().top && e.clientY < $('.ftv_popbox_h').offset().top + parseInt($('.ftv_popbox_h').css('height')))
				inBox = true;
			if (!inBox) {
				$('.ftv_popbox_h').hide();
			}
		}
	}
	
	// 关闭
	_this.closePopbox = function() {
		$('.ftv_popbox').remove();
		$('.ftv_popbox_h').hide();
	}
	
	init();
}

/* 页面类 */
function FTV_Page() {
	// 变量
	var _this = this;
	var action;
	var func;
	var paraArr;
	var name;
	var successFunc;
	var cntUrl;
	var lastUrl;
	var firstLoad;
	var testId;

	// 初始化函数
	var init = function() {
		action = '';
		func = '';
		paraArr = new Object();
		name = '';
		successFunc = new Array();
		cntUrl = '';
		lastUrl = '';
		firstLoad = true;
		testId = '';
		if (typeof windowResize != 'undefined')
			_this.success(windowResize);
		$('.ftv_link').live('click', linkClick);
		$(document).ready(parse);
		$(window).bind('hashchange', parse);
		$('.ftv_refresh_t').click(refresh);
	}
	
	// 链接点击
	var linkClick = function(u) {
		if (u == cntUrl) return;
		// 直接跳转
		if ($(this).attr('md_jump_url') != null) {
			location.href = $(this).attr('md_jump_url');
		}
		// 重新设定参数
		var url = u;
		var notChangeHash = true;
		if ($(this).attr('md_url') != null) {
			url = $(this).attr('md_url');
			notChangeHash = false;
		}
		if (url == 'stop') return;
		var para = $(this).attr('md_para');
		if (typeof(para) == 'string' && para != '') {
			// 仅设定参数
			var paras = para.split('/');
			for (var i = 1; i < paras.length; i += 2) {
				paraArr[paras[i]] = paras[i+1];
			}
		}
		else {
			// 完整路径
			paraArr = new Object();
			var paras = url.split('/');
			action = paras[1];
			func = paras[2];
			if (action == null || func == null) {
				action = 'test';
				func = 'index';
			}
			for (var i = 3; i < paras.length; i += 2) {
				paraArr[paras[i]] = paras[i+1];
			}
		}
		if (paraArr['test_id'] != null && paraArr['test_id'] != '')
			testId = paraArr['test_id'];
		else {
			if (testId != null || testId != '') {
				paraArr['test_id'] = testId;
				notChangeHash = false;
			}
		}
		// 改变hash
		var newUrl = '/' + action + '/' + func;
		for (var i in paraArr) {
			newUrl += '/' + i + '/' + paraArr[i];
		}
		lastUrl = cntUrl;
		cntUrl = newUrl;
		if (!notChangeHash) location.hash = $.base64.btoa(newUrl);
		fetch();
	}
	
	// 获取页面
	var fetch = function() {
		$.get('/' + action + '/' + func, paraArr,
			function(ret) {
				ret = eval('(' + ret + ')');
				// 跳转
				if (ret.jumpUrl != null) {
					location.href = ret.jumpUrl;
					return;
				}
				if (ret.linkUrl != null) {
					location.hash = $.base64.btoa(ret.linkUrl);
					return;
				}
				if (ret.backward == 1) {
					location.hash = $.base64.btoa(lastUrl);
					return;
				}
				if (ret.refresh == 1) {
					fresh();
					return;
				}
				// 设置标题和左面板
				if (action != 'test' || func != 'index') {
					if (name == '') {
						$.get('/test/getName', {test_id : paraArr['test_id']},
							function(ret) {
								ret = eval('(' + ret + ')');
								name = ret.name;
								$('.header .test .name').html(name);
								$('.header .test .name').show();
								$('.header .test .down').show();
								if (action == 'user') {
									$('.header .test .name').hide();
									$('.header .test .down').hide();
								}
								windowResize();
							}
						);
						$('.left_panel .list').hide(0);
					}
					// 测评页
					$('.left_panel').show();
					$('.left_panel').css('width', '210');
					$('.page_head').show();
					$('.page_head').css('height', '50');
					if (ret.item1 != null) {
						$('.left_panel .item').removeClass('active');
						$('.left_panel .item_' + ret.item1).addClass('active');
						if ($('.left_panel .item_' + ret.item1).next().css('display') == 'none') {
							$('.left_panel .list').hide(200);
							$('.left_panel .item_' + ret.item1).next().show(200);
						}
					}
					if (ret.item2 != null) {
						$('.left_panel .item_' + ret.item1).next().find('.it').removeClass('ac');
						$('.left_panel .item_' + ret.item1).next().find('.it_' + ret.item2).addClass('ac');
					}
					if (ret.pageTitle != null) {
						$('.page_head .title').html(ret.pageTitle);
					}
					$('.header .new_test').hide();
					$('.header .new_candidate').show();
					
					// 用户页
					if (action == 'user') {
						$('.header .test .name').hide();
						$('.header .test .down').hide();
						$('.header .new_test').hide();
						$('.header .new_candidate').hide();
					}
				}
				else {
					// 列表页
					$('.header .test .name').hide();
					$('.header .test .down').hide();
					$('.left_panel').hide();
					$('.left_panel').css('width', '0');
					$('.page_head').hide();
					$('.page_head').css('height', '0');
					$('.header .new_test').show();
					$('.header .new_candidate').hide();
					name = '';
					windowResize();
				}
				$('.page_content').html(ret.content);
				$('.page_content').scrollTop(0);
				success();
			}
		);	
	}
	
	// 成功加载页面
	var success = function () {
		for (var i in successFunc) {
			successFunc[i].call();
		}
		if (firstLoad) {
			firstLoad = false;
			setTimeout('$(".loading").fadeOut(100)', 1000);
		}
	}
	
	// 设置成功后需要调用的函数
	_this.success = function(func) {
		successFunc.push(func);
	}
	
	// 解析路径
	var parse = function() {
		var hash = window.location.hash;
		if (hash != '') {
			hash = hash.split('#');
			hash = hash[1];
		} else {
			location.hash = $.base64.btoa('/test/index');
			return;
		}
		if ($.base64.atob(hash).length > 50)
			return;
		linkClick($.base64.atob(hash));
	}
	
	// 刷新页面
	var refresh = function() {
		fetch();
	}
	
	// 获取action
	_this.getAction = function() {
		return action;
	}
	
	// 获取func
	_this.getFunc = function() {
		return func;
	}
	
	// 刷新页面
	
	init();
}

/* 表单类 */
function FTV_Form() {
	var _this = this;

	var init = function() {
		active();
		ftvPage.success(active);
		$('.myform').live('submit', submit);
	}
	
	// 激活各个表单
	var active = function() {
		$('.myform').each(function() {
			var name = $(this).attr('name');
			if (name == '') return;
			generate(name);
			$(this).find('.submit input[type="submit"]').click(function() {
				$(this).submit();
			});
			$(this).find('.file').click(function() {
				var formChildren = $('.iframe_' + name)[0].contentWindow.document.getElementById("form").childNodes;
				for (var i = 0; i < formChildren.length; i++) {
					if (formChildren[i].getAttribute('name') != $(this).attr('name')) continue;
					formChildren[i].click();
					break;
				}
			});
		});
	}
	
	// 提交表单
	var submit = function(e) {
		// 初始化
		var thisObj = this;
		if ($(thisObj).find('.disabled').length != 0) {
			e.preventDefault();
			return;
		}
		var submitBtn = $(thisObj).find('input[type="submit"]');
		if ($(thisObj).find('.submittingTip').length != 0) {
			submitBtn.val($(thisObj).find('.submittingTip').html());
		} else submitBtn.val('正在提交');
		submitBtn.addClass('mybtn_disabled');
		$(thisObj).append('<span class="disabled"></span>');
		if ($(thisObj).find('.force').length != 0) {
			this.submit();
			return;
		}
		submitBtn.blur();
		e.preventDefault();

		// 清空之前的数据
		var formObj = $('.iframe_' + $(thisObj).attr('name'))[0].contentWindow.document.getElementById("form");
		var formChildren = formObj.childNodes;
		var pointer = 0;
		while (pointer < formChildren.length) {
			if (formChildren[pointer].getAttribute('type') != 'file')
				formObj.removeChild(formChildren[pointer]);
			else
				pointer++;
		}
		
		// 获取数据
		var data = new Object();
		// 获取input的值
		var inputs = $(thisObj).find('input');
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].name == '' || (inputs[i].getAttribute('class') != null && inputs[i].getAttribute('class').indexOf('file') != -1)) continue;
			if (inputs[i].type == 'radio' && !inputs[i].checked) continue;
			if (inputs[i].type == 'checkbox' && !inputs[i].checked) continue;
			data[inputs[i].name] = inputs[i].value;
			var inputObj = document.createElement('input');
			inputObj.setAttribute('type', inputs[i].type);
			inputObj.setAttribute('name', inputs[i].name);
			inputObj.setAttribute('value', inputs[i].value);
			if (inputs[i].checked)
				inputObj.setAttribute('checked', 'true');
			formObj.appendChild(inputObj);
		}
		// 获取textarea的值
		var textareas = $(thisObj).find('textarea');
		for (var i = 0; i < textareas.length; i++) {
			if (textareas[i].name == '') continue;
			data[textareas[i].name] = textareas[i].value;
			var textareaObj = document.createElement('textarea');
			textareaObj.setAttribute('name', textareas[i].name);
			textareaObj.value = textareas[i].value;
			formObj.appendChild(textareaObj);
		}
		// 获取select的值
		var selects = $(thisObj).find('select');
		for (var i = 0; i < selects.length; i++) {
			if (selects[i].name == '') continue;
			data[selects[i].name] = selects[i].value;
			var selectObj = document.createElement('select');
			selectObj.setAttribute('name', selects[i].name);
			selectObj.innerHTML = '<option value="' + selects[i].value + '" selected="selected">' + selects[i].value + '</option>';
			formObj.appendChild(selectObj);
		}
		formObj.submit();
		
		$('.iframe_' + $(thisObj).attr('name')).load(function() {
			var resStr = getInnerText($('.iframe_' + $(thisObj).attr('name'))[0].contentWindow.document.body);
			var res = eval('(' + resStr + ')');
			$(thisObj).find('.tip').html('');
			if(res.status == 'success') {
				if (res.js != null) eval(res.js);
				// 设定名称
				if (res.name != null) {
					$('.header .test .name').html(res.name);
					windowResize();
				}
				// 刷新
				if (res.refresh != null) {
					$('.ftv_refresh_t').click();
					return;
				}
				// 是否跳转
				if (res.jumpUrl != null) {
					location.href = res.jumpUrl;
				}
				else
				if (res.linkUrl != null) {
					location.hash = $.base64.btoa(res.linkUrl);
				}
				else {
					if ($(thisObj).find('.successTip').length != 0) {
						submitBtn.val($(thisObj).find('.successTip').html());
					} else submitBtn.val('提交成功');
					submitBtn.addClass('btn-success');
				}
				// 显示成功信息
				for(var name in res.tip) {
					var deep = 0;
					var tipObj = $(thisObj).find("[name='" + name + "']").parent();
					while (tipObj != null && (tipObj.attr('class') == null || tipObj.attr('class').indexOf('item') == -1)) {
						deep++;
						if (deep > 100) break;
						tipObj = tipObj.parent();
					}
					tipObj = tipObj.find('.tip');
					tipObj.addClass('tip_success');
					tipObj.removeClass('tip_error');
					tipObj.html('<span class="fa fa-check"></span> ' + res.tip[name]);
				}
			} else {
				// 失败
				if ($(thisObj).find('.failTip').length != 0) {
					submitBtn.val($(thisObj).find('.failTip').html());
				} else submitBtn.val('提交失败');
				submitBtn.addClass('btn-danger');
				// 显示失败信息
				for(var name in res.error) {
					var deep = 0;
					var tipObj = $(thisObj).find("[name='" + name + "']").parent();
					while (tipObj != null && (tipObj.attr('class') == null || tipObj.attr('class').indexOf('item') == -1)) {
						deep++;
						if (deep > 100) break;
						tipObj = tipObj.parent();
					}
					tipObj = tipObj.find('.tip');
					tipObj.addClass('tip_error');
					tipObj.removeClass('tip_success');
					tipObj.html('<span class="fa fa-times"></span> ' + res.error[name]);
				}
			}
			generate($(thisObj).attr('name'));
			// 清除提示按钮
			$(this).delay(1500).queue(function() {
				if ($(thisObj).find('.submitTip').length != 0) {
					submitBtn.val($(thisObj).find('.submitTip').html());
				} else submitBtn.val('提交');
				submitBtn.removeClass('btn-success');
				submitBtn.removeClass('btn-danger');
				$(thisObj).find('.disabled').remove();
			});
		});
	}
	
	// iframe生成
	var generate = function(name) {
		var thisObj = $('.myform[name="' + name + '"]');
		$('.iframe_' + name).remove();
		var iframe = '<iframe class="iframe_' + $(thisObj).attr('name') + ' hidden"></iframe>';
		if ($('.page_content').length != 0)
			$('.page_content').append(iframe);
		else
			$('body').append(iframe);

		// 页面内容
		var html = '';
		html += '<form id="form" action="' + $(thisObj).attr('action') + '" method="' + $(thisObj).attr('method') + '" enctype="' + $(thisObj).attr('enctype') + '">';
		// 获取file的值
		var files = $(thisObj).find('.file');
		for (var i = 0; i < files.length; i++) {
			if (files[i].name == '') continue;
			html += '<input type="file" name="' + files[i].name + '" ';
			html += 'onchange="parent.ftvForm.changeFileText(\'' + $(thisObj).attr('name') + '\', \'' + files[i].name + '\', this.value)"';
			if (files[i].getAttribute('md_multiple') == '1') html += 'multiple="multiple"';
			html += '>';
		}
		// 其他值在提交表单时自动添加
		html += '</form>';
		
		// 设定上传文件的值
		$(thisObj).find('.file').each(function() {
			$(this).val($(this).attr('md_default_value'));
		});

		$('.iframe_' + name)[0].contentWindow.document.open();
		$('.iframe_' + name)[0].contentWindow.document.write(html);
		$('.iframe_' + name)[0].contentWindow.document.close();
	}
	
	// 更改
	_this.changeFileText = function(formName, fileBtnName, value) {
		$('.myform[name="' + formName + '"] input[name="' + fileBtnName + '"]').val(value.match(/[^\/\\]*$/)[0]);
		if ($('.myform[name="' + formName + '"] input[name="' + fileBtnName + '"]').attr('md_auto_submit') == 1) {
			$('.myform[name="' + formName + '"] input[name="' + fileBtnName + '"]').val('上传中');
			$('.myform[name="' + formName + '"]').submit();
		}
	}
	
	init();
}

/* 选择名称类 */
function FTV_NamePicker() {
	var _this = this;
	var data = new Object;
	
	// 初始化函数
	function init() {
		createIcon();
	}
	
	// 触发器点击
	$('.ftv_name_picker_t').bind('click focus', function(e) {
		var thisObj = $(this);
		e.cancelBubble = true;
		e.stopPropagation();
		ftvCommon.closePopbox();
		
		// 查看数据是否存在
		var url = $(this).attr('md_url');
		if (data[url] == null) {
			$.get(url, {},
				function(ret) {
					ret = eval('(' + ret + ')');
					data[url] = ret;
					thisObj.click();
				}
			);
			return;
		}
		
		// 复制数据
		var dataCnt = new Object();
		dataCnt.length = data[url].length;
		for (var i = 0; i < data[url].length; i++) {
			dataCnt[i] = data[url][i];
			dataCnt[i].flag = false;
		}
		
		// 将结果排序
		var text = $(this).val();
		if (text.length != 0) {
			var index = 0;
			// 1、将前缀相同的排到前面
			for (var i = 0; i < dataCnt.length; i++) {
				if (index > 10) break;
				if (dataCnt[i].name.substr(0, text.length) == text) {
					dataCnt[i].flag = true;
					var t = dataCnt[index];
					dataCnt[index] = dataCnt[i];
					dataCnt[i] = t;
					index++;
				}
			}
			// 2、将名称中含有关键词的放到前面
			for (var i = index; i < dataCnt.length; i++) {
				if (index > 10) break;
				if (dataCnt[i].name.indexOf(text) != -1) {
					dataCnt[i].flag = true;
					var t = dataCnt[index];
					dataCnt[index] = dataCnt[i];
					dataCnt[i] = t;
					index++;
				}
			}
		}

		// 创建div
		var div = $('<div></div>');
		div.addClass('ftv_name_picker');
		div.addClass('ftv_popbox');
		div.append('<table></table>');
		for (var i = 0; i < dataCnt.length; i++) {
			if (i > 10) break;
			if (text != '' && dataCnt[i].flag != true) break;
			if (dataCnt[i].id != $('[name="' + $(this).attr('md_name') + '"]').val()) 
				div.find('table').append('<tr><td>' + dataCnt[i].id + '</td><td>--</td><td>' + dataCnt[i].name + '</td><td><span class="glyphicon glyphicon-ok"></span></td></tr>');
			else
				div.find('table').append('<tr class="active"><td>' + dataCnt[i].id + '</td><td>--</td><td>' + dataCnt[i].name + '</td><td><span class="glyphicon glyphicon-ok"></span></td></tr>');
		}
		if (text != '' && (dataCnt[0] == null || dataCnt[0].flag != true)) {
			div.find('table').append('<tr md_disabled="disabled"><td style="text-align:center;"><span class="glyphicon glyphicon-remove"></span>&nbsp;未找到相应结果</td></tr>');
		}
		div.appendTo('.page_content');
		
		// 调整宽度
		$('.ftv_name_picker').css('min-width', $(this).css('width'));
		while (parseInt($('.ftv_name_picker td').css('height')) > 40) {
			$('.ftv_name_picker').css('width', parseInt($('.ftv_name_picker').css('width')) + 20);
		}
		$('.ftv_name_picker').css('left', $(this).offset().left);
		$('.ftv_name_picker').css('top', $(this).offset().top + 41);
		
		// 点击条目
		$('.ftv_name_picker tr').click(function() {
			if ($(this).attr('md_disabled') == 'disabled') return;
			$('[name="' + thisObj.attr('md_name') + '"]').val($(this).find('td:first-child').html());
			thisObj.val($(this).find('td:nth-child(3)').html());
			ftvCommon.closePopbox();
		});

	});
	
	// 文本框变化
	$('.ftv_name_picker_t').bind('input propertychange', function() {
		$(this).click();
	});

	// 文本框变化（失焦）
	$('.ftv_name_picker_t').change(function() {
		var id = $('[name="' + $(this).attr('md_name') + '"]').val();
		if (id != '') {
			for (var i = 0; i < data[$(this).attr('md_url')].length; i++)
			if (data[$(this).attr('md_url')][i].id == id) {
				$(this).val(data[$(this).attr('md_url')][i].name);
				break;
			}
		}
	});	
	
	// 为每个input创建图标
	var createIcon = function() {
		$('.ftv_name_picker_icon').remove();
		$('.ftv_name_picker_t').each(function() {
			var thisObj = this;
			var span = $('<span></span>');
			span.addClass('ftv_name_picker_icon');
			span.addClass('fa');
			span.addClass('fa-list-alt');
			span.css('position', 'absolute');
			span.css('color', '#9b9b9b');
			span.css('cursor', 'pointer');
			if ($(this).parent().css('position') == 'static') {
				$(this).parent().css('position', 'relative');
			}
			span.css('left', $(this).offset().left - $(this).parent().offset().left + parseInt($(this).css('width')) - 30);
			span.css('top', $(this).offset().top - $(this).parent().offset().top + parseInt($(this).css('height')) / 2 - 6);
			span.click(function(e) {
				e.cancelBubble = true;
				e.stopPropagation();
				thisObj.click();
			});	
			span.appendTo($(this).parent());
		});
	}
	_this.createIcon = createIcon;
	
	init();
}

/* 日历选择器 */
function FTV_DatePicker() {
	// 变量
	var _this = this;
	var year;
	var month;
	var day;
	var oYear;
	var inputObj;
	
	// 初始化函数
	var init = function() {
		createIcon();
		ftvPage.success(createIcon);
	}
	
	// 触发
	$('.ftv_date_picker_t').live('click focus', function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
		
		inputObj = $(this);
		var date = new Date(parseInt($('[name="' + inputObj.attr('md_name') + '"]').val()) * 1000);
		if (date == 'Invalid Date') date = new Date();
		year = 1900 + date.getYear();
		oYear = year;
		month = 1 + date.getMonth();
		day = date.getDate();
		create();
	});
	
	// 创建DIV
	var create = function() {
		ftvCommon.closePopbox();

		var html = '';
		html += '<div class="ftv_popbox ftv_date_picker" onselectstart="return false"><div class="sel"><div class="prev">&nbsp;<span class="fa fa-chevron-left"></span></div><div class="date"><select class="year">';
		// 年份
		for (var i = oYear - 3; i <= oYear + 3; i++)
		if (i != year)
			html += '<option value="' + i + '">' + i + '年</option>';
		else
			html += '<option value="' + i + '" selected="selected">' + i + '年</option>';
		html += '</select>&nbsp;&nbsp;<select class="month">';
		// 月份
		for (var i = 1; i <= 12; i++)
		if (i != month)
			html += '<option value="' + i + '">' + i + '月</option>';
		else
			html += '<option value="' + i + '" selected="selected">' + i + '月</option>';
		html += '</select></div><div class="next"><span class="fa fa-chevron-right"></span>&nbsp;</div><div class="clear"></div></div>';
		html += '<div class="week"><table><tr class="title"><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr>';
		// 天数
		var totDay = 31;
		if (month == '4' || month == '6' || month == '9' || month == '11') totDay = 30;
		if (month == 2) {
			if ((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0)) totDay = 29;
			else totDay = 28;
		}
		if (day > totDay) day == totDay;
  		var week = 0;
		for (var i = 1; i <= totDay; i++) {
			if (week == 0) html += '<tr class="content">';
			while (new Date(year, month - 1, i).getDay() != week) {
				html += '<td></td>';
				week = (week + 1) % 7;
			}
			if (day == i)
				html += '<td><span class="cnt">' + i + '</span></td>';
			else
				html += '<td><span>' + i + '</span></td>';
			if (week == 6) html += '</tr>';
			week = (week + 1) % 7;
		}
		html += '</table></div></div>';
		
		$('.page_content').append(html);
		$('.ftv_date_picker').css('left', inputObj.offset().left - parseInt($('.left_panel').css('width')));
		$('.ftv_date_picker').css('top', inputObj.offset().top + parseInt(inputObj.css('height')) + $('.page_content').scrollTop() + 3 - parseInt($('.header').css('height')) - parseInt($('.page_head').css('height')));
		$('.ftv_date_picker .prev').click(prevClick);
		$('.ftv_date_picker .next').click(nextClick);
		$('.ftv_date_picker .year').change(yearChange);
		$('.ftv_date_picker .month').change(monthChange);
		$('.ftv_date_picker .week .content span').click(dayClick);
	}
	
	// 上一月
	var prevClick = function() {
		month--;
		if (month == 0) {
			year--;
			month = 12;
			if (year < oYear - 3) {
				year++;
				month = 1;
			}
		}
		create();
	}
	
	// 下一月
	var nextClick = function() {
		month++;
		if (month == 13) {
			year++;
			month = 1;
			if (year > oYear + 3) {
				year--;
				month = 12;
			}
		}
		create();
	}
	
	// 年份变化
	var yearChange = function() {
		year = $(this).val();
		create();
	}
	
	// 月份变化
	var monthChange = function() {
		month = $(this).val();
		create();
	}
	
	// 日期点击
	var dayClick = function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
		var s = year;
		if (month < 10) s += '-0' + month;
		else s += '-' + month;
		var d = $(this).html();
		if (d < 10) s += '-0' + d;
		else s += '-' + d;
		inputObj.val(s);
		$('[name="' + inputObj.attr('md_name') + '"]').val(new Date(year, month - 1, d).getTime() / 1000);
		$('.ftv_date_picker').remove();
		if (inputObj.attr('md_time') == '1') {
			inputObj.val(s + ' 00:00');
			ftvTimePicker.trigger(inputObj, true);
		}
	}

	// 为每个input创建图标
	var createIcon = function() {
		$('.ftv_date_picker_icon').remove();
		$('.ftv_date_picker_t').each(function() {
			var thisObj = this;
			var span = $('<span></span>');
			span.addClass('ftv_date_picker_icon');
			span.addClass('fa');
			span.addClass('fa-calendar');
			span.css('position', 'absolute');
			span.css('color', '#9b9b9b');
			span.css('cursor', 'pointer');
			if ($(this).parent().css('position') == 'static') {
				$(this).parent().css('position', 'relative');
			}
			span.css('left', $(this).offset().left - $(this).parent().offset().left + parseInt($(this).css('width')) - 22);
			span.css('top', $(this).offset().top - $(this).parent().offset().top + parseInt($(this).css('height')) / 2 - 8);
			span.click(function(e) {
				e.cancelBubble = true;
				e.stopPropagation();
				thisObj.click();
			});
			span.appendTo($(this).parent());
		});
	}
	_this.createIcon = createIcon;
	
	init();
}

/* 时间选择器 */
function FTV_TimePicker() {
	// 变量
	var _this = this;
	var inputObj;
	var append;
	var hour;
	var minute;
	
	// 初始化函数
	var init = function() {
		createIcon();
		ftvPage.success(createIcon);
	}
	
	// 触发
	$('.ftv_time_picker_t').live('click focus', function(e) {
		e.cancelBubble = true;
		e.stopPropagation();

		inputObj = $(this);
		append = false;
		create();
	});
	
	// 其他类触发
	_this.trigger = function(obj, ap, da) {
		inputObj = obj;
		if (ap == null) append = false;
		else append = ap;
		create();
	}
	
	// 创建DIV
	var create = function() {
		ftvCommon.closePopbox();
		
		// 默认值
		hour = 0;
		minute = 0;
		var date;
		if (parseInt($('[name="' + inputObj.attr('md_name') + '"]').val()) < 86400)
			date = new Date(1381852800000 + parseInt($('[name="' + inputObj.attr('md_name') + '"]').val() * 1000));
		else
			date = new Date(parseInt($('[name="' + inputObj.attr('md_name') + '"]').val() * 1000));
		if (date != 'Invalid Date') {
			hour = date.getHours();
			minute = date.getMinutes();
		}
		if (hour < 10) hour = '0' + hour;
		if (minute < 10) minute = '0' + minute;

		var div = $('<div></div>');
		div.addClass('ftv_popbox');
		div.addClass('ftv_time_picker');
		div.append('<div class="time"><span class="hour">' + hour + '</span>:<span class="minute">' + minute + '</span></div>');
		div.append('<div class="content"></div>');
		div.appendTo('.page_content');
	
		// 调整宽度
		$('.ftv_time_picker').css('width', '200px');
		
		//$('.ftv_time_picker').css('width', inputObj.css('width'));
		
		$('.ftv_time_picker').css('left', inputObj.offset().left - parseInt($('.left_panel').css('width')));
		$('.ftv_time_picker').css('top', inputObj.offset().top + parseInt(inputObj.css('height')) + $('.page_content').scrollTop() + 3 - parseInt($('.header').css('height')) - parseInt($('.page_head').css('height')));
		
		// 小时点击
		$('.ftv_popbox .hour').click(hourClick);
		$('.ftv_popbox .minute').click(minuteClick);
		$('.ftv_popbox').click(function() {	
			e.cancelBubble = true;
			e.stopPropagation();
		});
		
		$('.ftv_popbox .hour').click();
	}
	
	// 小时点击
	var hourClick = function() {
		$('.ftv_popbox .time span').removeClass('active');
		$('.ftv_popbox .time .hour').addClass('active');
		$('.ftv_popbox .content table').remove();
		$('.ftv_popbox .content').append('<table></table>');
		for (var i = 0; i < 24; i++) {
			var hour = i;
			if (hour < 10) hour = '0' + i;
			if (i % 4 == 0)
				$('.ftv_popbox .content table').append('<tr></tr>');
			$('.ftv_popbox .content table tr:last-child').append('<td>' + hour + '</td>');
		}
		$('.ftv_popbox td').click(hourSelect);
	}
	
	// 小时选择
	var hourSelect = function(e) {
		e.cancelBubble = true;
		e.stopPropagation();
		$('.ftv_popbox .hour').html($(this).html());
		$('.ftv_popbox .minute').click();
	}
	
	// 分钟点击
	var minuteClick = function() {
		$('.ftv_popbox .time span').removeClass('active');
		$('.ftv_popbox .time .minute').addClass('active');
		$('.ftv_popbox .content table').remove();
		$('.ftv_popbox .content').append('<table></table>');
		for (var i = 0; i < 60; i++) {
			if (i % 5 != 0) continue;
			var minute = i;
			if (minute < 10) minute = '0' + i;
			if (i % 20 == 0)
				$('.ftv_popbox .content table').append('<tr></tr>');
			$('.ftv_popbox .content table tr:last-child').append('<td>' + minute + '</td>');
		}
		$('.ftv_popbox td').click(minuteSelect);
	}
	
	// 分钟选择
	var minuteSelect = function() {
		$('.ftv_popbox .minute').html($(this).html());
		if (append) {
			$('[name="' + inputObj.attr('md_name') + '"]').val(parseInt($('[name="' + inputObj.attr('md_name') + '"]').val()) + parseInt($('.ftv_popbox .hour').html()) * 3600 + parseInt($('.ftv_popbox .minute').html()) * 60);
			inputObj.val(inputObj.val().substr(0, 10) + ' ' + $('.ftv_popbox .hour').html() + ':' + $('.ftv_popbox .minute').html());
		} else {
			$('[name="' + inputObj.attr('md_name') + '"]').val(parseInt($('.ftv_popbox .hour').html()) * 3600 + parseInt($('.ftv_popbox .minute').html()) * 60);
			inputObj.val($('.ftv_popbox .hour').html() + ':' + $('.ftv_popbox .minute').html());
		}
		ftvCommon.closePopbox();
	}

	// 为每个input创建图标
	var createIcon = function() {
		$('.ftv_time_picker_icon').remove();
		$('.ftv_time_picker_t').each(function() {
			var thisObj = this;
			var span = $('<span></span>');
			span.addClass('ftv_time_picker_icon');
			span.addClass('fa');
			span.addClass('fa-clock-o');
			span.css('position', 'absolute');
			span.css('color', '#9b9b9b');
			span.css('cursor', 'pointer');
			if ($(this).parent().css('position') == 'static') {
				$(this).parent().css('position', 'relative');
			}
			span.css('left', $(this).offset().left - $(this).parent().offset().left + parseInt($(this).css('width')) - 22);
			span.css('top', $(this).offset().top - $(this).parent().offset().top + parseInt($(this).css('height')) / 2 - 8);
			span.click(function(e) {
				e.cancelBubble = true;
				e.stopPropagation();
				thisObj.click();
			});
			span.appendTo($(this).parent());
		});
	}
	_this.createIcon = createIcon;
	
	init();
}

/* 页面弹出类 */
function FTV_Popover(sel, config) {
	var _this = this;
	var sel = sel;
	var config = config;
	
	// 初始化函数
	function init() {
		// 生成遮罩
		if (config == null || config.mask != false) {
			var mask = $('<div></div>');
			mask.addClass('ftv_popover_mask');
			mask.attr('md_sel', sel);
			mask.appendTo('body');
			mask.animate({opacity: 0.75}, 280, 'swing');

			// 关闭
			$('.ftv_popover_mask').click(_this.close);
		}
		
		// 生成内容
		$(sel).removeClass('ftv_popover_content');
		$(sel).addClass('ftv_popover_content');
		$(sel).css('position', 'fixed');
		$(sel).css('opacity', 0);
		$(sel).show();
		$(sel).css('left', (parseInt($('body').css('width')) - parseInt($(sel).css('width'))) / 2);
		$(sel).css('top', (parseInt($('body').css('height')) - parseInt($(sel).css('height'))) * 2 / 5);
		$(sel).animate({opacity: 1}, 280);
	}
	
	// 关闭弹出页面
	_this.close = function(callback) {
		var cntSel = $('.ftv_popover_mask').attr('md_sel');
		if (cntSel == null) cntSel = sel;
		$(cntSel).animate({opacity: 0}, 120, 'swing', function() {
			$(cntSel).hide();
			if(typeof callback == 'function')
				callback();
		});
		$('.ftv_popover_mask').animate({opacity: 0}, 120, 'swing', function() {
			$('.ftv_popover_mask').remove();
		});
	}
	
	init();
}

/* 页面布局 TODO */
function FTV_Layout() {
	var _this = this;
	
	var init = function() {
	}
	
	_this.layout = function() {
		windowResize();
	}
	
	init();
}

/* 触发所有类 */
$().ready(function() {
	window.ftvCommon = new FTV_Common();
	window.ftvPage = new FTV_Page();
	window.ftvForm = new FTV_Form();
	window.ftvNamePicker = new FTV_NamePicker();
	window.ftvDatePicker = new FTV_DatePicker();
	window.ftvTimePicker = new FTV_TimePicker();
	window.ftvLayout = new FTV_Layout();
});
