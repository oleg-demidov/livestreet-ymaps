<?php

$config['location'] = [ // настройки поля локация
    'field_name' => 'location',
    
        
    'actions' => [
        /*
        * Настройки карты для настроек профиля 'action' => [options]
        */
       'settings'=>[
            'field_name' => '___plugin.ymaps.location.field_name___',
            'map'=>[
                'state' => [
                    'center' => [48.688936,66.799797],
                    'width' => 400,
                    'height' => 420,
                    'zoom' => 4,
                    'controls'=>['zoomControl']
                ],
                'options' => [
                    'restrictMapArea'=>true
                ]
            ],
            'staticMap' => [
                'zoom' => 4,
                'll' => '66.799797,48.688936',
                'pt' => [ //https://tech.yandex.ru/maps/doc/staticapi/1.x/dg/concepts/markers-docpage/
                    'style' =>'round',
                    'color' => '',
                    'size' => '',
                    'content' => ''
                ],
                'width' => 400,
                'height' => 100,
            ],
            /*
            * Настройки приоритета поиска. Например можно указать boundedBy прямоугольник в котором искать в первую очередь
            */
            'geocoder' => [
                'results'=>5,
                'boundedBy'=>[[41.450983, 46.115502],[55.669800, 87.336205]], //https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode-docpage/#param-options.boundedBy
                'strictBounds'=> false //https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode-docpage/#param-options.strictBounds
            ],
            /*
             * Для того чтобы работал фильтр geocoder.results желательно выставить более 500, указать geocoder.boundedBy и geocoder.strictBounds => true
             */
            'filter' =>[
                'enable' => true,
                'code' => "KZ", // фильтр По принадлежности региону https://tech.yandex.ru/maps/jsbox/2.1/regions
                'results' => 10, // результаты после фильтра
            ],

            /*
             * Окружность радиус которой тоже сохраняется
             */
            'circle' => [
                'show' => true,
                'minRadius' => 500,
                'radius'=>300,
                'properties' => [
                    'hintContent' => 'Вы можете выбрать точное местонахождение'//map.you_can_choose_geo'
                ],
                'options' => [
                    'draggable' => false,
                    'fillOpacity' => 0.3,
                    'fillColor' => '#5cc7ff',
                    'strokeColor' => '#2f8bbc',
                    'strokeOpacity'  => 0.3,
                    'strokeWidth'  => 1
                ]
            ],
            /*
            * Вид метки в настройках профиля
            */
           'point' => [
               'properties' => [
                   'hintContent' =>'Вы здесь находитесь'//map.you_destination_here'
               ],
               'options' => [
                   'preset' => 'islands#circleDotIcon', //https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/
                   'iconColor' => '#2f8bbc'
               ]
           ]
       ],

       /*
        * Настройки регистрации
        */
       /*'user'=> [
           'map' => Config::Get('plugin.ymaps.location.actions.settings')
        ],*/
        
       /*
        * Настройки отображения карты в профиле
        */
       'profile' => [
           'staticMap' => [
               'll'=>'37.620070,55.753630',
               'width' => 400,
               'height' => 300,
               'zoom' => 3,
               'pt' => [ //https://tech.yandex.ru/maps/doc/staticapi/1.x/dg/concepts/markers-docpage/
                   'style' =>'round',
                   'color' => '',
                   'size' => '',
                   'content' => ''
               ],
           ]  
       ]
    ]
    
];

$config['location']['actions']['fauth'] = &$config['location']['actions']['settings'];

$config['search_map'] = [ // настройки карты в поиске
    'actions' => [
        /*
        * Настройки карты в поиске
        */
       'masters' => [
           'default' => false, // Показывать карту по умолчанию
           'layoutResult' => 'Найдено пользователей ?',
           'map' => [
               'state' => [
                   'height' => 500,
                   'center' => [48.688936,66.799797],
                   'width' => 400,
                   'zoom' => 4,
                   'controls'=>['zoomControl'],

               ],
               'options' => [
                   'maxZoom' => 17
               ]
           ],
           'point' => [
               'preset' => 'islands#blueStretchyIcon', //https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/
           ],
           'cluster' => [
               'preset' => 'islands#blueClusterIcons', //https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/
               'clusterDisableClickZoom' => true,
               'clusterHideIconOnBalloonOpen' => false,
               'geoObjectHideIconOnBalloonOpen' => false
           ]
       ]
    ]
];



return $config;