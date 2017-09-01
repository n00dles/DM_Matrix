<script>
/* global jQuery */
jQuery(function($) {
    /**
     * @param string
     * @return string
     */
    function textToSlug(text)
    {
        return text.toLowerCase()
                   .replace(/ /g,'-')
                   .replace(/[^\w-]+/g,'')
                   .replace(/(-)+/g, '-');
    }
    
    function keyUpCallback()
    {
        var text = $(this).val();
        var slug = textToSlug(text);
        $(this).val(slug); 
    }
    
    $("<?php echo $selector; ?>").keyup(keyUpCallback);
});
</script>

<input type="text" class="text slug" value="<?php echo $value; ?>" <?php echo $properties; ?>/>