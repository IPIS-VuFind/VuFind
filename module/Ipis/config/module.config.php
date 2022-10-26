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
        ]
    ]
];

return $config;
