
jQuery(document).ready(function() { 

$('#nav_DM_Matrix').insertAfter($('#nav_pages'));

//$('#maincontent').css('width','960px');

$("a.form_submit").live("click", function(){
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
		return false;
	});	


$('#post-type').change(function() {
	alert("$('#post-type').selected()");
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
