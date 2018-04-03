
(function($) {
    "use strict";

    $.widget( "livestreet.ymapsFieldLocation", $.livestreet.ymapsGeo, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors:{
                container:"#map",
                staticMap:".static_map",
                selects:"select",
                input:"input",
                fields:".fields"
            
            },
            params: {}
        },
        circle:null,
        point:null,
        colorbox:null,
        geocoderGeo:null,
        
        _create: function () { 
            this._super();
                        
            /*this.createFormContainer();
            
            if( (typeof this.option('data') === "object") && (this.option('data') !== null) ){
                this.addToForm(this.option('data'));
            }*/
            
        },
        /*
         * Установка обработчиков и показ статичной карты
         */
        initYmaps:function(){
            var state = this.option('map.state');
            
            this.colorbox = this.elements.staticMap.colorbox({
                inline:true, 
                width:state.width+46,
                height:state.height+82,
                onComplete:this.createOnCompleteMap.bind(this),
                onCleanup:this.destroyMap.bind(this)
            });
            
               
        },
        
        createOnCompleteMap:function(){ 
            $('#cboxLoadedContent').css('overflow', 'visible');
            
            var formData = this.getDataForm(); 
            var state = {};
            if(formData !== null){
                state = {
                    center: [ formData.long, formData.lat ],
                    zoom:formData.zoom
                }
            }
            this.createJsMap(this.elements.container, state);
            
            this._addButton($.colorbox.close);
            this.map.cursors.push('pointer');
            this.map.events.add('click', this.mapClickHandler.bind(this));
            
            this.afterShowMap();
            
            this._trigger( 'aftershowmap', null, { context: this } );
        },
        destroyMap:function(){
            if(this.circle === null){
                this.mapClickHandler({get:function(c){return this.map.getCenter()}.bind(this)})
            }
            this.map.destroy();
            this.circle = null;
            this.point = null;
            this.elements.container.css({width:0, height:0});
            
            this.updateStaticMap();
        },
        updateStaticMap:function(){
            this.elements.staticMap.find('img').attr('src', this.getStaticMapUrl(this.getDataForm()));            
        },
        clickLink:function(){ 
            $('#cboxLoadedContent').css('overflow', 'visible');
            this.elements.container.show();
            var data = this.getDataForm();
            if(data !== null){ 
                
                
                this.createMarkers([ data.long, data.lat], data.radius);
                this.addMarkers();
                
                this.map.options.set('restrictMapArea',true);
                return;
            }
            
            this.geocoderStart();
            return false;
        },
        
        /*
         * Обрабочик изменения селекта Собираем данные и отправляем в поиск обьектов
         */
        geocoderToMap:function(geos){
            
            /*this.hideStaticMap();
            
            var geos = [];
            this.elements.selects.each(function(i,select){
                var option = $(select).find('option:selected');
                if(option.val()){
                    geos.push( option.text().trim() );
                }
            });
            
            geos.push( this.elements.input.val().trim() );
            
            var geos = geos.join(' ');*/
            this.geocoder(geos,this.setMapGeocoderObjects.bind(this));
            
        },
        /*
         * Устанавливаем карту в соответствии с результатом поиска обьектов
         */
        setMapGeocoderObjects:function(geoObjects){
            this.circle = null;
            this.point = null;           

            if(geoObjects !== undefined && geoObjects.getLength()){
                var oGeo = geoObjects.get(0);
                
                //this.geocoderGeo = oGeo;
                var state = this.option('map.state');
                
                var CenterZoom = this.getCenterAndZoom(oGeo.properties.get('boundedBy'));
                
                this.updateForm({
                    lat:CenterZoom.center[1],
                    long:CenterZoom.center[0],
                    zoom:CenterZoom.zoom
                })
                this.elements.staticMap.click()
                /*this.createOnCompleteMap();
                
                this.map.options.set('restrictMapArea',false);
                this.map.setBounds(oGeo.properties.get('boundedBy'));
                this.map.options.set('restrictMapArea',true);*/
            }     
            
        },
        afterShowMap:function(){
            if(this.geocoderGeo === null){
                return false;
            }
            /*this.map.options.set('restrictMapArea',false);
            this.map.setBounds(oGeo.properties.get('boundedBy'));
            this.map.options.set('restrictMapArea',true);*/
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
            
            if(radius === undefined){
                radius = this.getRadiusByBounds(aBounds)
                if(radius < 0){
                    radius = this.option('circle.radius');
                }
            }            
            
            this.createMarkers(aCenter, radius);
            
            this.addMarkers();
            
        },
        addMarkers:function(){
            this.clearMap();
            this.map.geoObjects.add(this.circle).add(this.point);
            this.map.setBounds(this.circle.geometry.getBounds());

            this.map.events.add('click', this.mapClickHandler.bind(this));
            this.circle.events.add('click', this.mapClickHandler.bind(this));
        },
        /*
         * Создаем круг и метку
         */
        createMarkers:function(aCenter, radius){
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
        },
        /*
         * Методы работы с данными формы
         */
        addToForm:function(data){
            $.each(data, function(key, val){
                this.elements.fields.append('<input type="hidden" name="'
                        +this.option('field_name')+'['+key+']" value="'+val+'" class="field-location">');
            }.bind(this));            
        },
        updateForm(data){
            $.each(data, function(key, val){
                this.elements.fields.find('input[name="'
                        +this.option('field_name')+'['+key+']"]').remove();
                this.elements.fields.append('<input type="hidden" name="'
                        +this.option('field_name')+'['+key+']" value="'+val+'" class="field-location">');
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
            this.updateStaticMap();
        }
    });
})(jQuery);

