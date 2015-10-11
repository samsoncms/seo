/**
 * Created by molodyko on 20.09.2015.
 */

// Click manual on the first tab of seo tab
SamsonCMS_InputSEO_TAB = function(block){
    var header = s('.sub-tab-header', block);
    if (header.length > 0) {
        $('span', header.DOMElement).addClass('active');
        setTimeout(function(){
            $('span', header.DOMElement).click();
        }, 100);
    }
};

if (SamsonCMS_Input != null) {

    // Bind input
    SamsonCMS_Input.bind(SamsonCMS_InputSEO_TAB, '#seo_field_tab');

}
