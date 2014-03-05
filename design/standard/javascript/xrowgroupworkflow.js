jQuery(document).ready(function() {
    $('.editFieldButtonCopy').each(function(){
        $(this).click(function(){
            var classname = $(this).attr('class'),
                charscount = classname.length,
                groupID = $(this).attr('id').substr(charscount);
            $.ez('xrowgroupwf::copyGroup', {'groupID' : groupID}, function(data) {
                if(!data.content.error){
                    $('#xrowGroupWorkflowOverview').html(data.content);
                }
            });
        });
    });
    $('.editFieldButtonRemove').each(function(){
        $(this).click(function(){
            var classname = $(this).attr('class'),
                charscount = classname.length,
                groupID = $(this).attr('id').substr(charscount);
            $.ez('xrowgroupwf::removeGroup', {'groupID' : groupID}, function(data) {
                if(!data.content.error){
                    $('#xrowGroupWorkflowOverview').html(data.content);
                }
            });
        });
    });

    var datePickerLocale = $(".xrowGroupWorkflowDate").data('locale');
    $.datepicker.setDefaults(datePickerLocale);
    $(".xrowGroupWorkflowDate").each(function(){
        $(this).datepicker({minDate: 0, changeMonth: true, changeYear: true, showWeek: true});
    });

    /*$('.xrowGroupWorkflowBrowseButton').each(function(){
        $(this).click(function(e) {
            var classname = 'xrowGroupWorkflowBrowseButton',
                charscount = classname.length,
                id = $(this).attr('id').substr(charscount),
                url = $(this).data('url'), 
                page_top = e.pageY - 400,
                body_half_width = $('body').width() / 2;
            if (body_half_width > 510)
                var page_left = body_half_width - 200;
            else
                var page_left = body_half_width - 300;
            var innerHTML = '<div id="mce_' + id + '" class="clearlooks2" style="width: 800px; height: 500px; top: ' + page_top + 'px; left: ' + page_left + 'px; overflow: auto; z-index: 300020;">'
                         + '    <div id="mce_' + id + '_top" class="mceTop"><div class="mceLeft"></div><div class="mceCenter"></div><div class="mceRight"></div><span id="mce_' + id + '_title">Image (SVG) Editor</span></div>'
                         + '    <div id="mce_' + id + '_middle" class="mceMiddle">'
                         + '        <div id="mce_' + id + '_left" class="mceLeft"></div>'
                         + '        <span id="mce_' + id + '_content">'
                         + '            <iframe src="' + url + '" class="xrowGroupWorkflowFrame" id="' + $(this).attr('id')+'_con" name="xrowGroupWorkflowFrame_' + $(this).attr('id') + '" style="border: 0pt none; width: 800px; height: 480px;" />'
                         + '        </span>'
                         + '        <div id="mce_' + id + '_right" class="mceRight"></div>'
                         + '    </div>'
                         + '    <div id="mce_' + id + '_bottom" class="mceBottom"><div class="mceLeft"></div><div class="mceCenter"></div><div class="mceRight"></div><span id="mce_' + id + '_status">Content</span></div>'
                         + '    <a class="mceClose" id="mce_' + id + '_close"></a>'
                         + '</div>';
            var blocker = '<div id="mceModalBlocker" class="clearlooks2_modalBlocker" style="z-index: 300017; display: block;"></div>';
            $('body').append(innerHTML);
            $('body').append(blocker);
            $('a#mce_' + id + '_close').bind('click', function(e) {
                $('#mce_' + id).remove();
                $('#mceModalBlocker').remove();
            });
        });
    });*/
});

//var html = '<div id="mce_4" role="dialog" aria-labelledby="mce_4_title" class="clearlooks2" style="width: 510px; height: 509px; top: 170px; left: 710px; overflow: auto; z-index: 300000;" aria-hidden="false"><div id="mce_4_wrapper" class="mceWrapper mceResizable mceMovable mceFocus"><div id="mce_4_top" class="mceTop"><div class="mceLeft"></div><div class="mceCenter"></div><div class="mceRight"></div><span id="mce_4_title">Upload new Image</span></div><div id="mce_4_middle" class="mceMiddle"><div id="mce_4_left" class="mceLeft" tabindex="0"></div><span id="mce_4_content"><iframe frameborder="0" id="mce_4_ifr" src="/eng_admin/ezoe/upload/38706/404/images/" style="border: 0px none; width: 500px; height: 480px;"></iframe></span><div id="mce_4_right" class="mceRight" tabindex="0"></div></div><div id="mce_4_bottom" class="mceBottom"><div class="mceLeft"></div><div class="mceCenter"></div><div class="mceRight"></div><span id="mce_4_status">Content</span></div><a class="mceMove" tabindex="-1" href="javascript:;"></a><a class="mceMin" tabindex="-1" href="javascript:;" onmousedown="return false;"></a><a class="mceMax" tabindex="-1" href="javascript:;" onmousedown="return false;"></a><a class="mceMed" tabindex="-1" href="javascript:;" onmousedown="return false;"></a><a class="mceClose" tabindex="-1" href="javascript:;" onmousedown="return false;"></a><a id="mce_4_resize_n" class="mceResize mceResizeN" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_s" class="mceResize mceResizeS" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_w" class="mceResize mceResizeW" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_e" class="mceResize mceResizeE" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_nw" class="mceResize mceResizeNW" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_ne" class="mceResize mceResizeNE" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_sw" class="mceResize mceResizeSW" tabindex="-1" href="javascript:;"></a><a id="mce_4_resize_se" class="mceResize mceResizeSE" tabindex="-1" href="javascript:;"></a></div></div>';