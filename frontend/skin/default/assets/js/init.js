
jQuery(document).ready(function($){
    
    /*
     * Инит карты в настройках
     */
    if (typeof(ymaps) == "undefined"){
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
    }
    
    /*
     * Инициация карты в поиске
     */
    ls.hook.add('ls_template_init_end', function(){
        /*
         * Инициация кнопки карты
         */
        $('.js-search-toggle-view').lsToggleButtons({
            aftertoggletomap:function(data, el){
                var lsSearchUsers = $('.js-search-ajax-users');
                
                lsSearchUsers.lsSearchAjax('option','beforeupdate', function(n, data){
                    data.setParam('map', 1);
                });
                
                var list = lsSearchUsers.lsSearchAjax('getElement','list');
                $(list).hide();
                
                var more = lsSearchUsers.lsSearchAjax('getElement','more');
                $(more).hide();
                /*
                 * Если нет контейнера карты создаем
                 */
                var mapContainer = $('.js-search-map-container');
                if(!mapContainer.length){
                    mapContainer = $('<div class="js-search-map-container"></div>');
                    lsSearchUsers.append(mapContainer);
                }
                /*
                 * Показываем карту вместо списка
                 */
                mapContainer.searchMap(Object.assign(ls.registry.get('ymapsOptions'), {
                        ymaps:(typeof(ymaps) == "undefined")?null:ymaps
                    })
                );
                mapContainer.show();
                /*
                 * Сохраняем времемнный колбэк поиска
                 */
                lsSearchUsers.lsSearchAjax("option", "tmpafterupdate", lsSearchUsers.lsSearchAjax("option", "afterupdate"));
                
                lsSearchUsers.lsSearchAjax("option", "afterupdate", function(){
                    if(data.response.users.length){
                        mapContainer.searchMap('clearMap');
                        mapContainer.searchMap('showUsersOnMap', data.response.users);
                    }
                });
                lsSearchUsers.lsSearchAjax("option", "update");
                
            },
            aftertogglefrommap:function(data, el){
                var lsSearchUsers = $('.js-search-ajax-users');
                
                lsSearchUsers.lsSearchAjax('option','beforeupdate', null);
                
                $('.js-search-map-container').hide();
                
                var list = lsSearchUsers.lsSearchAjax('getElement','list');
                $(list).show();
                 var more = lsSearchUsers.lsSearchAjax('getElement','more');
                $(more).show();
                
                /*
                 * Восстанаиливаем колбэк поиска
                 */
                lsSearchUsers.lsSearchAjax("option", "afterupdate", lsSearchUsers.lsSearchAjax("option", "tmpafterupdate"));
                
            }
        });

    }, 1);
});