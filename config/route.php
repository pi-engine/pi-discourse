<?php
/* << replace >>*/

return array(
    // route name
    'discourseRoute'  => array(
        'section'   => 'front',
        'priority'  => 100,
        //'type'      => 'Module\\Discourse\\Route\\Segment',
        'type'      => 'Module\\Discourse\\Route\\DiscourseRoute',
        'options'   => array(
            'prefix'              => '/discourse',
            'structure_delimiter'   => '/',
            'param_delimiter'     => '/',
            'key_value_delimiter' => '-',
            'defaults'            => array(
                'module'        => 'discourse',
                'controller'    => 'index',
                'action'        => 'index'
            ),
        ),
    ),
//    'user' => array(
//        'section'   => 'front',
//        'priority'  => 120,
////        'type'      => 'Module\\Discourse\\Route\\Segment',
//        'type'    => 'Zend\\Mvc\\Router\\Http\\Segment',
//        'options' => array(
//            'route'    => '/discourse/rest/user[/:id]',
//            'constraints' => array(
//                'id'     => '[0-9]+',
//            ),
//            'defaults' => array(
//                'controller' => 'discourse\index\index',
//            ),
//        ),
//    ),
//   
//    'default'   => array(
//        'section'   => 'front',
//        'priority'  => -999,
//
//        'type'      => 'Standard',
//        'options'   =>array(
//            'structure_delimiter'   => '/',
//            'param_delimiter'       => '/',
//            'key_value_delimiter'   => '-',
//            'defaults'              => array(
//                'module'        => 'discourse',
//                'controller'    => 'user',
//                'action'        => 'index',
//            )
//        )
//    )
);