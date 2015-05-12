
$(document).ready(function() {
	//календарь для даты отгрузки
	$('#datepicker1').datetimepicker({
		minDate:new Date(),
		// disabledDates:['07.05.2015'],
		timepicker:false,
	 	dayOfWeekStart: 1,
	 	onGenerate:function( ct ){
			$(this).find('.xdsoft_date.xdsoft_weekend')
				.addClass('xdsoft_disabled');
			$(this).find('.xdsoft_date');
		},
		onSelectDate: function(ct){
			//$('#datepicker1').removeAttr('readonly').removeClass('input_disabled');
			$('#btn_date_var').click();
		},
	 	format:'d.m.Y',
	 	
	});
	// время для даты отгрузки
	$('#timepicker1').datetimepicker({
	 datepicker:false,
	 format:'H:i',
	 // minTime:'9:00',
	 // maxTime:'21:00'
	 allowTimes:[
		  '09:00', '10:00', '11:00', '12:00','13:00', '14:00','15:00', 
		  '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'
	 ]
	});	
});

// отработка клика по быстрым кнопкам
$(document).on('click','#btn_make_std',function(){
	$(this).addClass('checked');
	$('#btn_make_var').removeClass('checked');
	$(this).parent().find('input').attr('readonly','true').addClass('input_disabled').val(10);
});
$(document).on('click','#btn_make_var',function(){
	$(this).addClass('checked');
	$('#btn_make_std').removeClass('checked');
	$(this).parent().find('input').removeAttr('readonly').removeClass('input_disabled');
});

$(document).on('click','#btn_date_std',function(){
	var d = new Date();
	var curr_date = d.getDate();
	var curr_month = d.getMonth() + 1;
	var curr_year = d.getFullYear();
	// указать дату отгрузки
	// решить как будет производиться обсчет выходных дней и праздников
	// исключить выходные из подсчёта рабочих дней
	cmm = (curr_date+5) + "." + curr_month + "." + curr_year;	
	$(this).addClass('checked');
	$('#btn_date_var').removeClass('checked');
	$('#datepicker1').val(cmm).attr('readonly','true').addClass('input_disabled');
});
$(document).on('click','#btn_date_var',function(){	
	$(this).addClass('checked');
	$('#btn_date_std').removeClass('checked');
	$(this).parent().find('input').removeAttr('readonly').removeClass('input_disabled');
});



$(document).on('click', '#variants_name .variant_name', function(){
	// отработка показа / скрытия вариантов расчёта
	// при клике по кнопкам вариантов
	$('.variant_name').removeClass('checked');
	$(this).addClass('checked');	
	var id = $(this).attr('data-cont_id');
	$('.variant_content_block').css({'display':'none'});
	$('#'+id).css({'display':'block'});
});

$(document).on('click','#choose_end_variant',function(){
	var id = $('#variants_name .variant_name.checked ').attr('data-id');

	var row_id = $('#claim_number').attr('data-order');

	$('#variants_name .variant_name').removeClass('osnovnoy');
	$('#variants_name .variant_name.checked').addClass('osnovnoy');

	$.post('', 
		{
			global_change: 'AJAX',
			change_name: 'change_draft',
			id:id,
			row_id:row_id
		}, function(data, textStatus, xhr) {
		if(data['response']!='1'){
			alert('что-то пошло не так.');
		}
	},'json');
})

// колькуляция и сохранение изменённых данных от тираже в таблице размеров
$(document).on('keyup','.val_tirage, .val_tirage_dop', function(){
	var summ = 0;
	$('#'+$('.variant_name.checked').attr('data-cont_id')+' .'+$(this).attr('class')).each(function(index, el) {
		summ += Number($(this).val());
	});
	console.log('-'+$(this).attr('class')+'- = -val_tirag-');
	if($(this).attr('class') == 'val_tirage'){
		var id = '#'+$('.variant_name.checked').attr('data-cont_id')+' .tirage_var';
	}else{
		var id = '#'+$('.variant_name.checked').attr('data-cont_id')+' .dop_tirage_var';	
	}
	$(id).val(summ);


	$.post('', {
		global_change: 'AJAX',
		change_name: 'size_in_var',
		val:$(this).val(),
		key:$(this).attr('data-id_size'),
		dop:$(this).attr('data-dop'),
		id: $(this).attr('data-var_id')
	}, function(data, textStatus, xhr) {
		console.log(data);
	});
});

$(document).on('click','.btn_var_std[name="std"]',function(){
	
	$(this).addClass('checked');
	$(this).parent().find('input').val(10);
});

$(document).on('keyup','.fddtime_rd2',function(){
	if($(this).val()!='10'){
		$(this).prev().removeClass('checked');
	}else{
		if(!$(this).prev().hasClass('checked')){
			$(this).prev().addClass('checked');
		}		
	}
});


// отслеживание нажатий функциональных клавиш с клавиатуры
$(document).keydown(function(e) {
	if(e.keyCode == 27){//ESC	
	// alert();
	}	
	if(e.keyCode == 38){//вверх		
		// alert()
		var id = '#'+$('.variant_name.checked').attr('data-cont_id')+' .fddtime_rd2';
		if($(id).is( ":focus" )){			
			$(id).val(Number($(id).val())+1);
			$(id).setCursorPosition($(id).val().length);
		}		
	}
	if(e.keyCode == 40){//вниз		
		// alert()
		var id = '#'+$('.variant_name.checked').attr('data-cont_id')+' .fddtime_rd2';
		if($(id).is( ":focus" )){			
			$(id).val(Number($(id).val())-1);
			// $(id).setCursorPosition($(id).val().length);
		}	
	}
	

});


