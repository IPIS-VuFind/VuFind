<?php
return [
    'extends' => 'bootstrap3',
    'helpers' => [
        'factories' => [
            'VuFind\View\Helper\Root\RecordDataFormatter' => 'Ipis\View\Helper\Root\RecordDataFormatterFactory'
        ]
    ]
];
