(function($) {
    "use strict";

    $.widget( "livestreet.searchMap", $.livestreet.ymapsGeo, {
        /**
         * Дефолтные опции
         */
        options: {
            
            params: {}
        },        
        usersCollection:null,
        
        _create: function () { 
            this._super();            
            
        },
        tmpAfterupdate:null,
        /*
         * После подгрузки ymaps вставляем элементы, включаем карту, устанавливаем обработчики на кнопки
         */
        initYmaps:function(){
            
           
            this.createJsMap(this.element);
            
            this.showYMap();
        },
        /*
         * Принимаем массив пользователей и вставляем на карту
         */
        showUsersOnMap:function(users){
            var aObjects = $.map(users, function(user, index){
                return new this.ymaps.Placemark(
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
