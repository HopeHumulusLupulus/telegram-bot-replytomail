<?php
$dbopts = parse_url(getenv('DATABASE_URL'));
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => ltrim($dbopts["path"],'/'),
        'production' => [
            'adapter' => 'pgsql',
            'host' => $dbopts["host"],
            'name' => ltrim($dbopts["path"],'/'),
            'user' => $dbopts['user'],
            'pass' => $dbopts['pass'],
            'port' => $dbopts['port'],
            'charset' => 'UTF-8'
        ]
    ]
];
