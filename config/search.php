<?php


$config['search'] = [ // настройки карты в поиске
    'actions' => [
        'masters' => [
            'default' => false, // Показывать карту по умолчанию
            'layoutResult' => 'Найдено пользователей ?',
            'map' => [
                'state' => [
                    'height' => 500,
                    'center' => [48.688936,66.799797],
                    //'width' => 700,
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

$config['search']['actions']['people'] = $config['search']['actions']['masters'];
    

return $config;