
(function($) {
    "use strict";

    $.widget( "livestreet.settingsMap", $.livestreet.ymapsGeo, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors:{
                container:null,
                selects:"select",
                input:"input"
            },
            params: {}
        },
        circle:null,
        point:null,
        
        _create: function () {
            this._super();
            this.addElement(
                this.element, 
                {name:'link', selector:".show_map"}, 
                {tag:"a", class:"show_map", href:"#"},
                '<i class="fa fa-map-marker  "></i>Отметить на карте');
            this.createFormContainer();
            if( (typeof this.option('data') === "object") && (this.option('data') !== null) ){
                this.addToForm(this.option('data'));
            }
            
        },
        /*
         * Установка обработчиков и показ статичной карты
         */
        initYmaps:function(){
            this._on(this.elements.selects,{ change: 'change' })
            this._on(this.elements.link,{ click: 'clickLink' });
            
            this.showStaticMap();
               
        },
        /*
         * Вызов метода статичной карты без перекрытия
         */
        showStaticMap:function(){
            var dataForm = this.getDataForm(); 
            if( dataForm !== null ){
                $.livestreet.ymapsGeo.prototype.showStaticMap.call(this, this.element, dataForm);
                return;
            }
        },
        clickLink:function(){
            this.change();
            return false;
        },
        /*
         * Обрабочик изменения селекта Собираем данные и отправляем в поиск обьектов
         */
        change:function(){
            
            this.hideStaticMap();
            var geos = [];
            this.elements.selects.each(function(i,select){
                var option = $(select).find('option:selected');
                if(option.val()){
                    geos.push( option.text().trim() );
                }
            });
            
            geos.push( this.elements.input.val().trim() );
            
            var geos = geos.join(' ');
            this.geocoder(geos,this.showGeocoderOnMap.bind(this));
            
        },
        /*
         * Устанавливаем карту в соответствии с результатом поиска обьектов
         */
        showGeocoderOnMap:function(geoObjects){
            if(this.map === null){
                this.createJsMap(this.element);
                this._addButton();
                this.map.cursors.push('pointer');
                this.map.events.add('click', this.mapClickHandler.bind(this));
            }
            this.clearMap();
            delete this.circle;
            delete this.point;
            if(!geoObjects.getLength()){
                return;
            }
            
            var oGeo = geoObjects.get(0);
            this.showJsMap();
             
            this.map.setBounds(oGeo.properties.get('boundedBy'));
        },
        mapButtonHandler:function(){
            this.showStaticMap();
            this.hideJsMap();
        },
        /*
         * Обработчик клика по карте
         */
        mapClickHandler:function(event){
            var aCoord = event.get('coords');
            if(this.circle === null){
                this.addCircle(aCoord, this.map.getBounds());
            }else{
                this.circle.geometry.setCoordinates(aCoord);
                this.point.geometry.setCoordinates(aCoord);
            }
            
            var mapBounds = this.map.getBounds();
            this.circle.geometry.setRadius(this.getRadiusByBounds(mapBounds));
            
            this.map.setBounds(this.circle.geometry.getBounds());
            
            this.updateForm(this.getCoords());           
        },
        
        
        buttonHandler:function(){
            this.showStaticMap();
            this.hideJsMap();
        },
        /*
         * Получаем данные с карты
         */
        getCoords:function(){
            var data = {};
            if(this.circle === undefined){
                return data;
            }
            var coords = this.circle.geometry.getCoordinates();
            var radius = this.circle.geometry.getRadius();

            if(Array.isArray(coords)){
                data.long = coords[0];
                data.lat = coords[1];
                data.radius = radius;
                data.zoom = this.map.getZoom();
            }
            return data;
        },
        /*
         * Добавляем круг и метку на карту
         */
        addCircle:function(aCenter, aBounds, radius){
            this.clearMap();
            if(radius === undefined){
                radius = this.getRadiusByBounds(aBounds)
                if(radius < 0){
                    radius = this.option('circle.radius');
                }
            }
            
            var cfgCircle = this.option('circle');

            var optCircle = cfgCircle.options;
            if(!cfgCircle.show){
                optCircle.fillOpacity = 0.0;
                optCircle.strokeWidth = 0;
            }
            this.circle = new ymaps.Circle([
                    aCenter,
                    radius
                ], 
                this.option('circle.properties'), 
                optCircle
            );
    
            var propPoint = this.option('point.properties');
            this.point = new ymaps.Placemark(
                    aCenter, 
                propPoint,
                this.option('point.options')
            );
            
            this.map.geoObjects.add(this.circle).add(this.point);
            this.map.setBounds(this.circle.geometry.getBounds());
                        
            this.circle.events.add('click', this.mapClickHandler.bind(this));
            
        },
        /*
         * Методы работы с данными формы
         */
        addToForm:function(data){
            $.each(data, function(key, val){
                this.elements.fields.append('<input type="hidden" name="'
                        +this.option('field_name')+'['+key+']" value="'+val+'">');
            }.bind(this));            
        },
        updateForm(data){
            $.each(data, function(key, val){
                this.elements.fields.find('input[name="'
                        +this.option('field_name')+'['+key+']"]').remove();
                this.elements.fields.append('<input type="hidden" name="'
                        +this.option('field_name')+'['+key+']" value="'+val+'">');
            }.bind(this));
        },
        getDataForm:function(){
            var fields = this.elements.fields.find('input');
            if(fields.length == 0){
                return null;
            }
            return fields.serializeJSON()[this.option('field_name')];
        },
        clearForm:function(){
            this.elements.fields.empty();
        },
        createFormContainer:function(){
            if(this.elements.fields === undefined){ 
                this.addElement(this.element, {name:'fields', selector:".fields"}, {tag:"div", class:"fields"});
            }
        }
    });
})(jQuery);

