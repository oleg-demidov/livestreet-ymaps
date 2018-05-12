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
            delayLoad:200,
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
                clear:".input-close-but",
                country:".js-field-geo-country",
                region:".js-field-geo-region",
                city:".js-field-geo-city",
            },
            params: {},
            i18n: {
                
            },
            afterchange:null,
            afterclear:null
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
            
            if(this.elements.input.val()){
                this.elements.clear.css('display', 'block');
            }
            
            this.elements.clear.on('click', function(event){
                $(event.currentTarget).css('display', 'none');
                this.elements.input.val('');
                this.elements.input.attr('title', '');
                this.clearFields();
                
                this._trigger( 'afterchange', null, { context: this } );
                //this.show();
            }.bind(this));
            
            this.option('aftershow', this.startLoad.bind(this)); 
            /*if(this.elements.input.data('showDropdown')){
                this.option('aftershow', this.startLoad.bind(this)); 
            }else{
                this.toggle = function(){};
            }*/
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
            
            
        },
        hoverLi:function(event){ 
            this.delay(this.nextLoad.bind(this, $(event.currentTarget) ), this.option('delayLoad')) 
        },
        clickItem:function(event){
            this.hide();
            
            var data = this.giveData(event.currentTarget);
            var nameArr = [];
            
            
            $.each(data, function(key, dataVal){ 
                this.elements[dataVal.type].val(dataVal.id).change();
                nameArr.push(dataVal.text);
                
            }.bind(this));
            this.elements.country.keyup();
                        
            this.elements.input.val(nameArr.join(', ')).removeClass('default-text');      
            this.elements.input.attr('title', nameArr.join(', '));
            this.elements.clear.css('display', 'block');
            
            this._trigger( 'afterchange', null, { context: this } );
            
            return false;
        },
        giveData:function(element){
            var data = [];
            var countryExist = false;
            
            $(element).find('a').parents('li').each(function(i,el){
                var dataEl = $(el).data();
                if(dataEl.type === 'country'){
                    countryExist = true;
                }
                if(data.length && data[data.length-1].id == dataEl.id){
                    return;
                }
                data.push(dataEl);
                
            });
            
            if(!countryExist){
                data.push( this.elements.menu.find('li:not(.ls-nav-item--has-children)').data() );
            }
            return data;
        },
        change:function(){
            if(this.elements.input.val() === ''){
                this.show();
                return;
            }
            this._load('geo',{query:this.elements.input.val()}, function(response){
                /*if(this.elements.menu.attr('aria-hidden')){
                    this.show();
                }*/
                var liItems = $(response.html ).children();
                liItems.click(this.clickItem.bind(this));
                this.elements.menu.empty().append( liItems );
                //console.log(response)
            }.bind(this), {showProgress:false});
        },
        loadList:function(type, params, call){
            this._load( type, Object.assign({ target_type: this.type }, params), function( response ) {
                call(response);                
            }.bind( this), {showProgress:false});
        },
        clearFields:function(){
            this.elements.country.val('');
            this.elements.region.val('');
            this.elements.city.val('').keyup();
        }
    });
})(jQuery);