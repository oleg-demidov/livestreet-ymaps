
jQuery(document).ready(function($){
    
    /*
     * Инит карты
     */
    
    $('.js-ymaps-field-location').ymapsFieldLocation( ls.registry.get('ymaps_options') ); 
        
    /*
     * Привязка карты к полю гео
     */
    
    $('.ymaps-ajax-geo').lsFieldAjaxGeo('option', 'afterchange', function(e,data){
        $('.js-ymaps-field-location').ymapsFieldLocation( 'geocoderToMap', data.context.elements.input.val() )
    });
    
    
    $('.ymaps-ajax-geo').lsFieldAjaxGeo('option', 'afterclear', function(e,data){ 
        $('.js-ymaps-field-location').ymapsFieldLocation( 'clearForm' );
    });
    
    /*
     * Инициация карты в поиске
     */
    ls.hook.add('ls_template_init_end', function(){
        
        var searchToggleOpt = Object.assign(ls.registry.get('ymapsOptions'), {
            selectors:{
                resultList:".js-search-ajax-masters"
            }
        });
        
        $('.js-search-ajax-users').searchToggleMap(Object.assign(ls.registry.get('ymapsOptions'), {
                selectors:{
                    resultList:".js-more-users-container"
                }
            })
        );
        
//        $('.js-search-ajax-ads').searchToggleMap(searchToggleOpt);
    }, 1);
});