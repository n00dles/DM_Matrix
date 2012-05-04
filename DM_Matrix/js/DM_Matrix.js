

jQuery(document).ready(function() { 

$("#editpages").tablesorter({widgets: ['zebra']}) 
.tablesorterPager({container: $("#pager")}); 

//$('#nav_DM_Matrix').insertAfter($('#nav_pages'));


$("button.form_submit").on("click", function(){
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


$('.imagepicker').live("change",function() {
	//alert("changed..");
})

$('select#post-type').on("change",function() {
	//alert($(this).val());
	fieldtype=$(this).val();
	switch (fieldtype){
		case 'dropdown':
			$('#fieldoptions').html('test');
			break; 
		default: 
			$('#fieldoptions').html('');
			break; 
	}
})


$('.datepicker').each(function(){
    $(this).datepicker({ dateFormat: 'dd-mm-yy' });
});

$('.datetimepicker').each(function(){
	$(this).datetimepicker({ dateFormat: 'dd-mm-yy' });	
})

$('#dm_addnew').on("click", function(){
	$('#DM_addnew_row').stop().slideUp();
	$(this).next().stop().slideToggle();
	//alert("add new");	
	return false;	
})
	
	
$('#addfield').on("click", function(){
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


  $('.mtrx_but').button();
  $('.mtrx_but_add').button({icons:{primary: "mtrx_dbadd"}});


})



function makeSlug(element) {
    var slug = jQuery.trim($('#'+element).val()) // Trimming recommended by Brooke Dukes - http://www.thewebsitetailor.com/2008/04/jquery-slug-plugin/comment-page-1/#comment-23
    .replace(/\s+/g,'-').replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase() // See http://www.djangosnippets.org/snippets/1488/ 
    .replace(/\-{2,}/g,'-'); // If we end up with any 'multiple hyphens', replace with just one. Temporary bugfix for input 'this & that'=>'this--that'
    $('#' + element+"-slug").val(slug);
}



