// 全选
var totChecked = 0;
$('input[type="checkbox"]').click(function() {
	if ($(this).attr('class') == 'select_all') {
		if ($(this).attr('checked') == 'checked') {
			$('input[type="checkbox"]').attr('checked', 'checked');
			totChecked = $('input[type="checkbox"]').length - 1;
		}
		else {
			$('input[type="checkbox"]').attr('checked', null);
			totChecked = 0;
		}
	} else {
		if ($(this).attr('checked') == 'checked')
			totChecked++;
		else
			totChecked--;
		if (totChecked == $('input[type="checkbox"]').length - 1)
			$('.select_all').attr('checked', 'checked');
		else
			$('.select_all').attr('checked', null);
	}
});

// 获取选中的编号列表
function getIdList() {
	var idList = '';
	var tot = $('tr').length - 1;
	for (var i = 2; i < tot + 2; i++) {
		var obj = $('tr:nth-child('+i+')');
		if (obj.find('input[type="checkbox"]').attr('checked') == 'checked')
			idList += obj.find('.id').html() + '|';
	}
	return idList;
}

// 链接点击
$('.report_link').click(function() {
	if (getIdList() == '') 
		$(this).attr('md_url', 'stop');
	else
		$(this).attr('md_url', $(this).attr('md_tmp_url') + '/idList/' + getIdList());
});