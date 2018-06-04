
jQuery(document).ready(function($){
    /*
     * ymaps геолокация
     */ 
    $('.ymaps-ajax-geo').lsFieldAjaxGeo({
        urls: {
            geo: aRouter.ymaps + 'ajax-geo',
            countries: aRouter.ymaps + 'ajax-countries',
            regions: aRouter.ymaps + 'ajax-regions',
            cities: aRouter.ymaps + 'ajax-cities'
        }
    });
    
    ls.hook.add('ls_template_init_end', function(){
        $('.js-search-ajax-users').lsSearchAjax('addFilter', {
            type: 'text',
            name: 'geo[country]',
            selector: '.js-field-geo-country'
            
        });
        $('.js-search-ajax-users').lsSearchAjax('addFilter', {
            type: 'text',
            name: 'geo[region]',
            selector: '.js-field-geo-region'
            
        });
        $('.js-search-ajax-users').lsSearchAjax('addFilter', {
            type: 'text',
            name: 'geo[city]',
            selector: '.js-field-geo-city'
        });
    }, 1);
    
});