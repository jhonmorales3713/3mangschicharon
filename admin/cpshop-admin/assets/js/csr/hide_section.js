$(".hide_section").click(function(){
	var divsection = $(this).data('value');
	if($("#"+divsection).is(":visible")){
		$("#"+divsection).hide('slow');
    }else{
        $("#"+divsection).toggle('slow');
    }
});