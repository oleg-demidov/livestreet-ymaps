(function($) {
    "use strict";

    $.widget( "livestreet.ymapsJsMap", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            aftershowstatmap:null,
            aftershowjsmap:null,
            afterhidejsmap:null,
            afterinitjsmap:null,
            ymap:null,
            geocoder:null,
            filter:null,
            createAfterInit:true,
            values:{},
            selectors:{
                container:".container-ymap"
            },
            params: {}
        },
        map:null,
        clusterer:null,
        container:null,
        ymapsReady:false,
        jsMapReady:false,
        
        _create: function () {
            this._super();
            
            if(typeof(ymaps) !== "undefined"){
                ymaps.ready(this.ymapsOnReady.bind(this));
            }
            
        },
        /*
         * Вызывается после подгрузки всех обьектов ymaps
         */
        ymapsOnReady:function(){
            this._trigger( 'afterinitymaps', null, this );
            this.ymapsReady = true;
            
            if(this.option('createAfterInit')){
                this.createJsMap(this.element);       
            }
        },
        
        isMapReady:function(){
            return this.jsMapReady;
        },
        createJsMap:function(container, state, options){
            this.container = container;
            state = state || {};
            options = options || {};
            if(this.option('map') === null){
                console.log('Настройки карты не переданы');
                return;
            }
            state = Object.assign(this.option('map.state'), state );
            options = Object.assign(options, this.option('map.options'));
            
            let idMap = 'map' + Math.random();
            
            container.attr('id', idMap);
            
            container.css({width:state.width,height:state.height});

            this.map = new ymaps.Map(idMap, state, options); 
            
            this.jsMapReady = true;
            
            this.map.events.add('boundschange', this.controlBounds.bind(this));
            
            this._trigger( 'afterinitjsmap', null, this );
            
        },
        /*
         * Контролирует zoom. Чтобы не уходил за границы видимости
         */
        controlBounds:function(event){
            if(this.option('map.maxZoom') && event.get('newZoom') > this.option('map.maxZoom')){
                this.map.setZoom(this.option('map.maxZoom'));
            }
        },
        /*
         * Обратный вызов кнопки Ok на карте
         */
        mapButtonHandler:function(){
            this.hideJsMap();
        },
        /*
         * Показывает статичную карту
         */
        showStaticMap:function(container, options){
            var src;
            
            if(!(src = this.getStaticMapUrl(options))){
                return false;
            }
            
            this.addElement(container, {name:'staticMap', selector:".static-map"}, {tag:"img", src:src, class:"static-map"});
            
            this._trigger( 'afterShowStatMap', null, this );
        },
        
        getStaticMapUrl:function(options){
            options = options || {};
            
            if(this.option('staticMap') === null){
                console.log('Настройки static карты не переданы');
                return false;
            }
            var statOpt =  JSON.stringify(this.options.staticMap);
            statOpt =  JSON.parse(statOpt);
            
            var resOptions = Object.assign(statOpt, options ); 
            var pt = $.map(resOptions.pt, function(value, index) {
                return [value];
            });
            var size = [resOptions.width, resOptions.height];
            var queryData = {
                l:"map",
                size:size.join(','),
                z:Math.abs(resOptions.zoom),
                ll:resOptions.ll
            };   
            if(resOptions.lat !== undefined){
                queryData.pt = [Math.abs(resOptions.lat), Math.abs(resOptions.long), pt.join('')].join(',');
                delete queryData.ll;
            }
            
            var src = "https://static-maps.yandex.ru/1.x/?"+$.param(queryData);  
            
            return src;
        },
        
        hideStaticMap:function(){
            if(this.elements.staticMap === undefined){
                return;
            }
            this.elements.staticMap.remove();
            delete this.elements.staticMap;
        },
        
        clearMap:function(){
            this.map.geoObjects.removeAll();
        },
        hideJsMap:function(){
            this.container.css('display', 'none');
            this._trigger( 'afterHideJsMap', null, this );
        },
        showJsMap:function(){
            this.container.css('display', 'block')
                    .css({height: this.option('map.state.height'), width:this.option('map.state.width')});
            this.map.container.fitToViewport();
            this._trigger( 'afterShowJsMap', null, this );
        },
        /*
         * Получить радиус для окружности чтобы она входила в область bounds
         */
        getRadiusByBounds:function(aBounds){
            var dist = ymaps.coordSystem.geo.getDistance(aBounds[0],aBounds[1]);
            var radius = Math.ceil(dist/4);
            if(this.option('ymapsOptions.circle.minRadius') > radius){
                radius = this.option('ymapsOptions.circle.minRadius');
            }
            return radius;
        },
        getCenterAndZoom:function(Bounds){ 
            var state = this.option('map.state');
            return ymaps.util.bounds.getCenterAndZoom(Bounds,[state.width, state.height]);
        },
        /*
         * Получить набор обьектов по запросу
         */
        geocoder:function(mQuery, call){
            if(this.option('geocoder') === null || this.option('filter') === null){
                console.log('Настройки geocoder или filter не переданы');
                return;
            }
            
            var options = this.option('geocoder');
            //console.log('geocoder ',mQuery,options)
            var myGeocoder = ymaps.geocode(mQuery, options);
            myGeocoder.then(
                function (res) {
                    if(!this.option('filter.enable')){
                        call(res.geoObjects);
                        return;
                    }
                    var geoObjects = this.filterRes(res);
                    call(geoObjects);
                }.bind(this),
                function (err) {
                    console.log('Ошибка поиска гео обьекта',err);
                    call();
                }
            );
        },
        /*
         * Фильтр обьектов по запросу
         */
        filterRes:function(res){
            
            var newGeoObjects = new ymaps.GeoObjectCollection({}, {});

            res.geoObjects.each(function(oGeo){
                if(newGeoObjects.getLength() >= this.option('filter.results')){
                    return;
                }                
                if(!this.option('filter.code')){
                    results.push( oGeo );
                }
                if(oGeo.getCountryCode().localeCompare(this.option('filter.code')) == 0){
                    newGeoObjects.add(oGeo);
                }                                
            }.bind(this)); 
            
            return newGeoObjects;
        },
        /*
         * Добавить обьекты на карту используя кластеризацию(Можно добавить 1000 и больше не боясь торможения карты)
         */
        addObjectsClusterer:function(aObjects){
            if(this.clusterer !== null){
                this.clusterer.removeAll();
            }else{
                this.clusterer = new ymaps.Clusterer(this.option('cluster'));
            }
            this.clusterer.add(aObjects);
            this.map.geoObjects.add(this.clusterer);
        },
        /*
         * Получить регионы и их геометрию
         */
        regionsLoad:function(sCode, call){
            ymaps.regions.load(sCode, {
                lang: 'ru',
                quality: 1
            }).then(function (result) {
                this.regions = result.geoObjects;
                call(this.regions);
            }.bind(this),
            function (err) {
                console.log('Ошибка загрузки регионов');
            });
        },
        /*
         * Добавляет кнопку Ок на карту
         */
        _addButton:function(call){
            var _this = this;
            var layout = ymaps.templateLayoutFactory.createClass(
                    '<button type="button" id="butOk" value="" class="ls-button ls-button--primary map-ok ">{{ data.content }}</button>',
                {
                    build:function(){
                        layout.superclass.build.call(this);
                        $('#butOk').on('click', function(){
                            _this.buttonHandler();
                            call();
                        })
                    }
                }
            );
            
            var button = new ymaps.control.Button({
                data:{
                    content:'Ок',
                    float:"right"
                },
                options: {
                    layout: layout
                }
            });
            
            
            
            this.map.controls.add(button, {float: 'right'});
        },
        buttonHandler:function(){},
        
        /*
         * Принимаем массив обьектов и вставляем на карту
         */
        showObjectsOnMap:function(objects){
            
            if(!objects.length){
                return false;
            }
            
            var aObjects = $.map(objects, function(object, index){
                return new ymaps.Placemark(
                    [object.long, object.lat ], 
                    Object.assign(this.option('point.options'),{
                        clusterCaption: object.title || '',
                        iconContent: object.title || '',
                        balloonContent: '<a href="'+object.path+'"><img src="'+object.imagePath+'"/></a><br>' + 
                                '<a href="'+object.path+'">'+object.title+'</a>',
                        
                    }),{
                        preset:this.option('point.preset'),
                        
                    }
                );
            }.bind(this));
            this.addObjectsClusterer(aObjects);
            
            this.map.setBounds(this.clusterer.getBounds());
            this.map.setBounds(this.clusterer.getBounds());
            
        }
    });
})(jQuery);

