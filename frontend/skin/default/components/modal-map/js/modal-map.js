
(function($) {
    "use strict";

    $.widget( "livestreet.modalMap", $.livestreet.ymapsGeo, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors:{
                modal:'.js-ymaps-modal',
                container:null,
                selects:"select",
            },
            params: {}
        },
        
        _create: function () {
            this._super();   
            $(this.option('selectors.modal')).lsModal();
            this.element.lsModalToggle();
            this.elements.modal = $('#'+this.element.data('lsmodaltoggleModal')).lsModal();
           
        },
        /*
         * Показ статичной карты в модальном окне
         */
        initYmaps:function(){
            this.showStaticMap(this.elements.modal.find('.ls-modal-body').empty(), this.element.data());               
        }
        
    });
})(jQuery);

