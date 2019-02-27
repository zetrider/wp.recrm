var recrmPluginAdmin = {
    propertyShowClickHandler: function(e) {
        if(e) {
            e.preventDefault();
        }
        var $button  = $(this),
            $parent  = $button.closest('[data-recrm-property-show="parent"]'),
            $content = $parent.find('[data-recrm-property-show="content"]');

        $button.hide();
        $content.slideDown();
    }
}
jQuery(document).ready(function() {
    var $recrnPropertyShowBtn = $('[data-recrm-property-show="button"]');
    if($recrnPropertyShowBtn.length)
    {
        $recrnPropertyShowBtn.on('click', recrmPluginAdmin.propertyShowClickHandler)
    }
});
