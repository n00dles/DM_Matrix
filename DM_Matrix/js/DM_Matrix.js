
jQuery(document).ready(function() { 

$('#nav_DM_Matrix').insertAfter($('#nav_pages'));

//$('#maincontent').css('width','960px');

$("button.form_submit").live("click", function(){
		errors=false;
		$('.required').each(function(index) { 
			if ($(this).removeClass('formerror'));
			if ($(this).val()=="") {
				$(this).addClass('formerror');
				errors=true;
			}
		})
		if (errors==false){
			$(this).closest('form').submit();
		}
		return false;
	});	


$('#post-type').change(function() {
	//alert("$('#post-type').selected()");
})


$('.datepicker').each(function(){
    $(this).datepicker({ dateFormat: 'dd-mm-yy' });
});

$('.datetimepicker').each(function(){
	$(this).datetimepicker({ dateFormat: 'dd-mm-yy' });	
})

$('#dm_addnew').live("click", function(){
	$('#DM_addnew_row').stop().slideUp();
	$(this).next().stop().slideToggle();
	//alert("add new");	
	return false;	
})
	
	
$('#addfield').live("click", function(){
	errors=false;
		$('.required').each(function(index) { 
			if ($(this).removeClass('error'));
			if ($(this).val()=="") {
				$(this).addClass('error');
				errors=true;
			}
		})
		if (errors==false){
			$(this).closest('form').submit();
		}
})

})
