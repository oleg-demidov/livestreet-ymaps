
jQuery(document).ready(function($){
    /*
     * ymaps геолокация
     */ 
    var elAjaxGeo = $('.ymaps-ajax-geo');
    if(elAjaxGeo.length){
        elAjaxGeo.lsFieldAjaxGeo({
            urls: {
                geo: aRouter.ymaps + 'ajax-geo',
                countries: aRouter.ymaps + 'ajax-countries',
                regions: aRouter.ymaps + 'ajax-regions',
                cities: aRouter.ymaps + 'ajax-cities'
            },
            selectors:{
                form:'form'
            }
        });
    }
    
    var elFieldLocation = $('.js-ymaps-field-location');
    if(elFieldLocation.length){
        elFieldLocation.ymapsFieldLocation( ls.registry.get('ymaps_options') );
        
        if(elAjaxGeo.length){
            elAjaxGeo.lsFieldAjaxGeo('option', 'afterchange', function(e,data){
                elFieldLocation.ymapsFieldLocation( 'geocoderToMap', data.context.elements.input.val() )
            });

            elAjaxGeo.lsFieldAjaxGeo('option', 'afterclear', function(e,data){ 
                elFieldLocation.ymapsFieldLocation( 'clearForm' );
            })
        }
    }
});