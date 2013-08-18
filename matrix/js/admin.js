/*
$(document).on('click', '.openAdvanced', function(e){
  $(this).closest('td').find('.advanced').slideToggle();
  return false;
}); // on


// fix type
function fixType(selector) {
  var type = selector.val();
  selector.closest('#metadata_window').find('.masks .mask').attr('name', '').hide();
  var s = selector.closest('#metadata_window').find('.mask.' + selector.val());
  var mask = s.val();
  
  s.attr('name', 'mask[]').stop().slideDown('fast');
  if (s.length == 0) {
    selector.closest('#metadata_window').find('.masks .blank').attr('name', 'mask[]');
    selector.closest('#metadata_window').find('.masks p').hide();
  }
  else {
    selector.closest('#metadata_window').find('.masks p').show();
  }
  fixMask(s);
}


// fix mask
function fixMask(selector) {
  var type = selector.closest('#metadata_window').find('.type').val();
  var mask = selector.val();

  selector.closest('#metadata_window').find('#menu-items > div:not(.' + type + '_' + mask + ')').stop().hide();
  var s = selector.closest('#metadata_window').find('.' + type + '_' + mask);
  s.slideDown('fast');
  
  if (s.length == 0) {
    selector.closest('#metadata_window').find('#menu-items').hide();
  }
  else {
    selector.closest('#metadata_window').find('#menu-items').show();
  }
}



$(document).ready(function() {
  $('.type').each(function() {
    fixType($(this));
  });
}); // ready

$('body').on('change', '.type', function() {
  fixType($(this));
}); // on

$('body').on('change', '.mask', function() {
  fixMask($(this));
}); // on

*/


function str2slug(selector) {
  var Text = selector.val();
  Text.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'').replace(/(-)+/g, '-');
  selector.val(Text);	
}