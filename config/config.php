<?php

Config::Set('router.page.ymaps', 'PluginYmaps_ActionYmaps');

$config['load_ymaps_actions'] = [
    'settings'
];

$config['field_name'] = 'ymap'; // Имя поля в форме

$config['options'] = [
    
    
    /*
     * Настройки карты для настроек профиля
     */
    'profile_settings'=>[
        'enable' => true,
        'field_name'=>$config['field_name'],
        'map'=>[
            'center' => [37.620070,55.753630],
            'width' => 400,
            'height' => 300,
            'zoom' => 4,
            'controls'=>['zoomControl'],
            'restrictMapArea'=>true
        ],
        'staticMap' => [
            'pt' => [ //https://tech.yandex.ru/maps/doc/staticapi/1.x/dg/concepts/markers-docpage/
                'style' =>'round',
                'color' => '',
                'size' => '',
                'content' => ''
            ],
            'width' => 400,
            'height' => 300,
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
            'enable' => false,
            'code' => "KZ", // фильтр По принадлежности региону https://tech.yandex.ru/maps/jsbox/2.1/regions
            'results' => 10, // результаты после фильтра
        ],
        
        /*
         * Окружность радиус которой тоже сохраняется
         */
        'circle' => [
            'show' => false,
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
     * Настройки карты в поиске
     */
    'search' => [
        'default' => true, // Показывать карту по умолчанию
        'layoutResult' => 'Найдено пользователей ?',
        'map' => [
            'height' => 500,
            'center' => [37.620070,55.753630],
            'width' => 400,
            'zoom' => 4,
            'controls'=>['zoomControl'],
            'maxZoom' => 15
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
    ],
    /*
     * Имя полей с координатами
     */
    'field_name' => $config['field_name'],
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
    
    
    
    
    
];



return $config;