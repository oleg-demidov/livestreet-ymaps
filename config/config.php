<?php

Config::Set('router.page.ymaps', 'PluginYmaps_ActionYmaps');


$config['field_name'] = 'location';


$config['options'] = [
    
    
    
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