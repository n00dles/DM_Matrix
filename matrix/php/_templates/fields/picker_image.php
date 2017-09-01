<script type="text/javascript">
/* global jQuery */
jQuery(function($) {
    $('#browse-<?php echo $name; ?>').click(function(event) {
        window.open('<?php echo $url; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
    });
});
</script>

<input class="text imagepicker" type="text" <?php echo $properties; ?>/>
<span class="edit-nav">
    <a id="browse-<?php echo $name; ?>" href="javascript:void(0);">Browse</a>
</span>