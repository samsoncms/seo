/**
 * Created by molodyko on 20.09.2015.
 */

// Click manual on the first tab of seo tab
s('#seo_field_tab').pageInit(function(block){
    var header = s('.sub-tab-header', block);
    if (header.length > 0) {
        $('span', header.DOMElement).addClass('active');
        setTimeout(function(){
            $('span', header.DOMElement).click();
        }, 100);
    }
});
