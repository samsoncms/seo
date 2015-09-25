/**
 * Created by molodyko on 25.09.2015.
 */

s('.#site_map_field_tab').pageInit(function(parent){
    console.log('hello');

    s('.refresh-btn').ajaxClick(function(response, e){
        console.log(e);
        s('.result-block', e.parent()).html('Time: '+response.time+', Count: '+response.count);
    },function(){
        return true;
    });
});