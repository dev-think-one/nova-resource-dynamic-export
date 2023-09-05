<?php

return [
    'tables' => [
        'export_stored_files' => 'export_stored_files',
    ],

    'defaults' => [
        'disk'               => 'exports',
        'queue'              => 'export',
        'download_route'     => 'download.exports',
    ],
];
