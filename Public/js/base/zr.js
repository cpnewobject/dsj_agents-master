/**
 * Created by rong on 2016/12/7.
 */
$(function(){
    // 悬浮选择
    $('.z-select .select-sub li').on('click', function () {
        var _This = $(this).parent().siblings('.select-cur');
        var mytitle = $(this).attr('title');
        var mySelect = $(this).html();
        $('#keyword').attr('placeholder','请输入'+mySelect);
        _This.html(mySelect);
        _This.attr({title: mytitle});
    });
    // 悬浮选择
    $('.j-select .select-sub li').on('click', function () {
        var _This = $(this).parent().siblings('.select-cur');
        var mytitle = $(this).attr('title');
        var mySelect = $(this).html();
        _This.html(mySelect);
        _This.attr({title: mytitle});
    });
    // 全选
    $('thead .checked-all').on('click', function () {
        if (this.checked) {
            $(this).parents('table').find('tbody input').prop("checked","checked");
        } else {
            $(this).parents('table').find('tbody input').prop("checked",'');
        }
    });
    // 动态监测按钮
    $(' .z-check').on('change',function(){
        if($('tbody .z-check').is(':checked')){
            $(this).parents('.z-list-table-wrap').siblings('.z-tool-wrap').find('.cg.btn-gray').removeClass('btn-gray');
        }else{
            $('.cg').addClass('btn-gray');
            $('thead .checked-all').prop("checked",'');
        }

    });
    // 重置按钮
    $('.btn-reset').on('click',function(){
        $(this).parents('form').find('input').val('');

    });
});
