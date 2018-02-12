/**
 * Местоположение
 *
 * @module ls/field/geo
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldAjaxGeo", $.livestreet.lsDropdown, {
        /**
         * Дефолтные опции
         */
        options: {
            delayLoad:100,
            levelsLoad:[
                'countries',
                'regions',
                'cities'
            ],
            levelLoadStart:0,
            // Ссылки
            urls: {
                regions: null,
                geo: null
            },
            // Селекторы
            selectors: {
                toggle:".js-field-geo",
                input:".js-field-geo",
                form:null,
                clear:".input-close-but"
            },
            params: {},
            i18n: {
                select_region: '@field.geo.select_region',
                select_city: '@field.geo.select_city'
            }
        },        
        delay :(function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })(),
        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
            this.elements.form = $(this.option('selectors.form')+':first');
            
            this.elements.input.keyup(function() {
                this.delay(this.change.bind(this), this.option('delayLoad') );
            }.bind(this));
            
            this.elements.clear.on('click', function(event){
                $(event.currentTarget).css('display', 'none');
                this.elements.input.val('');
                this.clearForm();
                //this.show();
            }.bind(this));
            
            if(this.elements.input.val()){
                this.elements.clear.css('display', 'block');
            }
            
            this.option('aftershow', this.startLoad.bind(this)); 
            
        },
        startLoad:function(){
            var menuParent = this.elements.menu.parent();
            
            var countryCode = ls.registry.get('country_code');
            
            menuParent.data({
                levelLoad: 0
            });
            if(countryCode !== undefined && countryCode !== null){
                menuParent.data({
                    levelLoad: 1, 
                    country_code:countryCode
                });
            }
            
            this.nextLoad(menuParent);
        },
        nextLoad:function(menuItem){
            menuItem.off( 'mouseenter');
            
            var params = menuItem.data();
            
            var typeObjects =  this.option('levelsLoad')[params.levelLoad];
            
            this.loadList(typeObjects, params, function( response ){
                var liItems = $(response.html ).children();
                
                liItems.data({
                    levelLoad: params.levelLoad+1
                });
                    
                if(params.levelLoad<2){                 
                    liItems.on( 'mouseenter', this.hoverLi.bind(this));
                }
                var menuUl = menuItem.find('ul');
                menuUl.empty().append( liItems );
                menuUl.find('li:not(.ls-nav-item--has-children)').click(this.clickItem.bind(this));
            }.bind(this));
            
            this.option('aftershow', null);
        },
        hoverLi:function(event){ 
            this.delay(this.nextLoad.bind(this, $(event.currentTarget) ), this.option('delayLoad')) 
        },
        clickItem:function(event){
            this.hide();
            var data = $(event.currentTarget).data();
            
            this.elements.input.val(data.text);         
            this.elements.clear.css('display', 'block');
            
            this.clearForm();
            this.addForm('geo['+data.type+']', data.id);
            
            return false;
        },
        change:function(){
            this._load('geo',{query:this.elements.input.val()}, function(response){
                if(this.elements.menu.attr('aria-hidden')){
                    this.show();
                }
                var liItems = $(response.html ).children();
                liItems.click(this.clickItem.bind(this));
                this.elements.menu.empty().append( liItems );
                console.log(response)
            }.bind(this));
        },
        loadList:function(type, params, call){
            this._load( type, Object.assign({ target_type: this.type }, params), function( response ) {
                call(response);                
            }.bind( this));
        },
        addForm:function(key, value){
            var input = $(document.createElement('input'));
            input.attr({name:key, value:value, type:'hidden'}).addClass('appended-geo');
            this.elements.form.append(input);
        },
        removeForm:function(key){
            this.elements.form.find('input[name='+key+']').remove();
        },
        clearForm:function(){
            this.elements.form.find('.appended-geo').remove();
        }
        
    });
})(jQuery);