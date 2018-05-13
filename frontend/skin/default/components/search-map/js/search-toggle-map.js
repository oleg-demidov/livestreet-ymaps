(function($) {
    "use strict";

    $.widget( "livestreet.searchToggleMap", $.livestreet.ymapsGeo, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors:{
                showList:".js-show-list",
                showMap:".js-show-map",
                mapContainer:".fl-map-container"
            },
            params: {}
        },        
        usersCollection:null,
        _create: function () { 
            this._super();
            
            $(this.option('selectors.map')).remove();
            
        },
        tmpAfterupdate:null,
        /*
         * После подгрузки ymaps вставляем элементы, включаем карту, устанавливаем обработчики на кнопки
         */
        initYmaps:function(){
            
           
            this.createJsMap(this.elements.mapContainer);
            
            this.elements.showMap.on('click', this.showYMap.bind(this));
            this.elements.showList.on('click', this.showList.bind(this));
            
            this.hideJsMap();
            
            //this.addCountUsers();
            
            if(this.option('default')){
                this.showYMap();
            }       
        },
        /*
         * Пернеключаем на карту
         */
        showYMap:function(){
            this.elements.showMap.prop('disabled', true);
            this.elements.showList.prop('disabled', false);
            
            this.elements.resultList.hide();
            this.showJsMap();
            
            this.element.flSearchAjax("setParam",'bMap',1);
            
            this.tmpAfterupdate = this.element.flSearchAjax("option", "afterupdate");
            this.element.flSearchAjax("option",'afterupdate', function ( event, data ) {
                //console.log(data.response);
                this.clearMap();
                if(data.response.users.length){
                    this.showUsersOnMap(data.response.users);
                }
            }.bind(this));
            
            this.element.flSearchAjax("update");
           /* var el = $(this.option('selectors.resultList'));
            el.hide();
            
            el.lsSearchAjax("option",'urls.search',aRouter.ymaps + 'ajax-serach-users/');
            
            el.lsSearchAjax("update");*/
        },
        /*
         * Переключаем на список
         */
        showList:function(){
            this.hideJsMap();
            this.elements.resultList.show();
            
            this.elements.showMap.prop('disabled', false);
            this.elements.showList.prop('disabled', true);
            
            this.element.flSearchAjax("setParam",'bMap',undefined);
            
            this.element.flSearchAjax("option",'afterupdate',this.tmpAfterupdate);
            this.element.flSearchAjax("update");
            /*var el = $(this.option('selectors.resultList'));
            el.show();
            el.lsSearchAjax("setParam",'bMap',0);
            el.lsSearchAjax("option",'urls.search',aRouter.people + 'ajax-search/');
            el.lsSearchAjax("option",'afterupdate', function ( event, data ) {
                data.context.getElement( 'more' ).lsMore( 'option', 'params.next_page', 2 );
            });
            el.lsSearchAjax("update");*/
        },
        /*
         * Индикатор количества пользователей
         */
        showCountUsers:function(count){
            var layoutResult = this.option('layoutResult');
            this.elements.elCount.html(layoutResult.replace(/\?/, count ));
        },
        addCountUsers:function(){
            this.elements.elCount = $('<h3 class="h3 js-user-list-search-title" style=""></h3>');
            this.elements.elCount.css({ position:'absolute', zIndex:'100'})
            //this.elements.map.prepend(this.elements.elCount);
        },
        /*
         * Принимаем массив пользователей и вставляем на карту
         */
        showUsersOnMap:function(users){
            var aObjects = $.map(users, function(user, index){
                return new ymaps.Placemark(
                    [user.long,user.lat ], 
                    {
                        clusterCaption: user.user_profile_name || user.user_login,
                        iconContent: user.user_profile_name || user.user_login,
                        iconContentSize: 100,
                        balloonContent: '<a href="'+user.path+'"><img src="'+user.avatar+'"/></a><br>' + 
                                '<a href="'+user.path+'">'+(user.user_profile_name || user.user_login)+'</a>',
                        
                    },{
                        preset:this.option('point.preset'),
                        
                    }
                );
            }.bind(this));
            this.addObjectsClusterer(aObjects);
            
            this.map.setBounds(this.clusterer.getBounds());
            
            
        }
    });
})(jQuery);
