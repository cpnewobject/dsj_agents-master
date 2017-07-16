$(function () {
	$('#sidebar .slide_btn').click(function(){
		$(this).siblings('li').slideToggle('fast');
		if($(this).children('.icon_rt').hasClass('rote')){
			$(this).children('.icon_rt').removeClass('rote');
		}else{
			$(this).children('.icon_rt').addClass('rote');
		}
	});

	$('.order_shaxu .time_mun').click(function(){
		if($(this).hasClass('mun_active')){
			$(this).removeClass('mun_active');
		}else{
			$(this).addClass('mun_active').siblings('.time_mun').removeClass('mun_active');
		}
	});
	//日期范围 options
	var start = {
	    format: 'YYYY-MM-DD',
	    minDate: '2014-1-1 00:00:00',
	    festival:true,
	    isTime:false,
	    maxDate: $.nowDate(0),
	    choosefun: function(elem,datas){
	        end.minDate = datas;
	    }
	};
	var end = {
	    format: 'YYYY-MM-DD',
	    minDate: '2014-1-1 00:00:00',
	    festival:true,
	    isTime:false,
	   	maxDate:$.nowDate(0)
	};
	$('.jetime_start').jeDate(start);
	$('.jetime_end').jeDate(end);
	$(document).on('click','.jetime_start',function(){
		$('.jetime_start').jeDate(start);
	});
	$(document).on('click','.jetime_end',function(){
		$('.jetime_end').jeDate(end);
	});

});