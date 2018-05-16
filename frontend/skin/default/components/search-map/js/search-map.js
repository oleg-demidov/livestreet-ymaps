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
            
            this.hideJsMap();
        }
    });
})(jQuery);
