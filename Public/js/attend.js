function FTV_Question() {
	var _this = this;
	var testId;
	var hasLoad;
	
	var init = function() {
		testId = $('.g_test_id').html();
		hasLoad = new Array();
		$('.ques_load').click(_this.load);
	}
	
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
				
				// 名称
				if (res.base.type_id == 4)
					div.append('<div class="name">' + res.detail.name + '</div>');

				// 描述
				div.append('<div class="desc">' + res.detail.desc + '</div>');
				
				// 作答
				if (res.base.type_id == 4) {
					
				}
				
				div.appendTo('.question');
			}
		);
	}
	
	init();
}

$().ready(function() {
	window.ftvQuestion = new FTV_Question();
	ftvQuestion.load(1);
});