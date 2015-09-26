/**
 * Created by molodyko on 25.09.2015.
 */

s('.#site_map_field_tab').pageInit(function(){

    s('.refresh-btn').ajaxClick(function(response, e){

        // Show result
        if (response.status == 1) {

            // Update content
            s('.result-block', e.parent()).html('Time: '+response.time+' sec, Count: '+response.count+' items');
        } else {

            s('.result-block', e.parent()).html('Error: '+response.error+'');
        }
    },function(){
        // TODO Add preloader
        return true;
    });

    // Hide current structure
    setTimeout(function(){

        var btnAdd = s('.sub-tab-content .material_table_add', s('#site_map_field_tab')).elements[0];
        var idCurrentStructurebtnAdd = btnAdd.a('href').replace(/.*\/(\d*)$/, '$1');

        s('li[value="'+idCurrentStructurebtnAdd+'"]', btnAdd.parent()).hide();
    }, 1000);


    // Hide span before select type element
    s('.select-structure-wrapper', s('#site_map_field_tab')).each(function(el){
        s('span', el.parent()).hide();
    });

});