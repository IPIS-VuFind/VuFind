<?php
namespace Ipis\Module\Configuration;

$config = [
    'vufind' => [
        'plugin_managers' => [
            'contentblock' => [
                'factories' => [
                    'Ipis\ContentBlock\OtsFeed' => 'Ipis\ContentBlock\OtsFeedFactory'
                ],
                'aliases' => [
                    'otsfeed' => 'Ipis\ContentBlock\OtsFeed',
                ]
            ],
            'recorddriver' => [
                'factories' => [
                    'Ipis\RecordDriver\SolrDefault' => 'VuFind\RecordDriver\SolrDefaultFactory'
                ],
                'aliases' => [
                    'VuFind\RecordDriver\SolrDefault' => 'Ipis\RecordDriver\SolrDefault'
                ]
            ],
            'recordtab' => [
                'factories' => [
                    'Ipis\RecordTab\Opinion' => '\Laminas\ServiceManager\Factory\InvokableFactory'
                ],
                'aliases' => [
                    'Opinion' => 'Ipis\RecordTab\Opinion',
                    'opinion' => 'Ipis\RecordTab\Opinion'
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            'Ipis\Controller\LocalFileController' => 'VuFind\Controller\AbstractBaseFactory'
        ],
        'aliases' => [
            'LocalFile' => 'Ipis\Controller\LocalFileController',
            'localfile' => 'Ipis\Controller\LocalFileController'
        ]
    ],
    'router' => [
        'routes' => [
            'localfile-open' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route' => '/LocalFile/Open',
                      'defaults' => [
                        'controller' => 'LocalFile',
                        'action' => 'Open',
                      ]
                ]
            ]
        ]
    ]
];

return $config;
