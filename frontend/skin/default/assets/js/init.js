
jQuery(document).ready(function($){
    
    let mapOptions = ls.registry.get('ymapsOptions');
    
    /*
     * Инит карты в настройках
     */
    $('.js-ymaps-field-location').ymapsFieldLocation( mapOptions ); 

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
        /*
         * Добавляем контейнер карты 
         */
        $('.js-search-ajax-users, .js-search-ajax-ads').append('<div class="js-search-map-container"></div>')
            
        /*
         * Добавляем  карту
         */
        $('.js-search-map-container').ymapsJsMap( mapOptions ).hide();
        /*
         * Инициация кнопки карты
         */
        $('.js-search-toggle-view').lsToggleButtons({
            aftertoggletomap:function(data, el){
                var lsSearch = $('.js-search-ajax-users, .js-search-ajax-ads');
                
                /*
                 * Меняем url поиска
                 */                
                lsSearch.lsSearchAjax("option", "urls.searchtmp", lsSearch.lsSearchAjax("option", "urls.search"));
                lsSearch.lsSearchAjax("option", "urls.search",  mapOptions.search_url);
                /*
                 * Скрываем элементы поиска
                 */
                var list = lsSearch.lsSearchAjax('getElement','list');
                $(list).hide();
                
                var more = lsSearch.lsSearchAjax('getElement','more');
                $(more).hide();
                
                /*
                 * Показываем карту вместо списка
                 */
                let mapContainer = $('.js-search-map-container')
                mapContainer.show();
                /*
                 * Сохраняем времемнный колбэк поиска
                 */
                lsSearch.lsSearchAjax("option", "tmpafterupdate", lsSearch.lsSearchAjax("option", "afterupdate"));
                
                lsSearch.lsSearchAjax("option", "afterupdate", function(e,data){
                    $(list).hide();
                    let users = data.response.objects || [];
                    if(mapContainer.ymapsJsMap('isMapReady')){
                        mapContainer.ymapsJsMap('clearMap');
                        mapContainer.ymapsJsMap('showObjectsOnMap', data.response.objects);
                    }
                });
                lsSearch.lsSearchAjax( "update");
                
            },
            aftertogglefrommap:function(data, el){
                var lsSearch = $('.js-search-ajax-users, .js-search-ajax-ads');
                /*
                 * Возвращаем url поиска
                 */
                lsSearch.lsSearchAjax("option", "urls.search", lsSearch.lsSearchAjax("option", "urls.searchtmp"));
                
                /*
                 * Скрываем карту
                 */
                $('.js-search-map-container').hide();
                
                /*
                 * Восстанаиливаем колбэк поиска
                 */
                lsSearch.lsSearchAjax("option", "afterupdate", lsSearch.lsSearchAjax("option", "tmpafterupdate"));
                lsSearch.lsSearchAjax( "update");
                
            }
        });

    }, 1);
});