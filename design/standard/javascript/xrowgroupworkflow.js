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

    var datePickerClassName = 'xrowGroupWorkflowDate',
    datePickerLocale = $('.'+datePickerClassName).data('locale');
    $.datepicker.setDefaults(datePickerLocale);
    $('.'+datePickerClassName).each(function(){
        var oldValue = $(this).val();
        var i = ($(this).attr('id') + '').indexOf(datePickerClassName, 0);
        if(i >= 0) {
            var charscount = datePickerClassName.length,
                id = $(this).attr('id').substr(charscount);
        }
        $(this).datepicker({
            onClose: function (dateText, inst) {
                if(oldValue != dateText && typeof id != 'undefined')
                    setButtonToSave(id);
            },
            minDate: 0,
            changeMonth: true,
            changeYear: true,
            showWeek: true});
    });
});

var setButtonToSave = function(id) {
    $('#xrowGroupWorkflowSave'+id).removeClass('button').addClass('defaultbutton');
};