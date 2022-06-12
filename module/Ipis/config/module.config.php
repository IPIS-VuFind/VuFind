<?php
namespace Ipis\Module\Configuration;

$config = [
    'vufind' => [
        'plugin_managers' => [
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
