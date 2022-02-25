function checkbox_status(){
    if ($("#checkbox-isautoassign").prop('checked')){
        $("#entry-isautoassign").val(1);
    } else {
        $("#entry-isautoassign").val(0);
    }
}

$('#checkbox-isautoassign').change(function() {
    if ($("#checkbox-isautoassign").prop('checked')){
        $("#entry-isautoassign").val(1);
        taginput_state(false);
    } else {
        $("#entry-isautoassign").val(0);
        taginput_state(true);
    }
});

function form_state(state){
    $( ".form-state" ).each(function( index ) {
        $(this).attr('disabled', state);
    });
    $( ".saveBtn" ).attr('hidden', state);
}

$("#back-button").click(function(e){
    window.location.href= $(this).data('value');
});

function taginput_state(val_state){
    $('.taginput-field').each(function( index ) {
    });
}