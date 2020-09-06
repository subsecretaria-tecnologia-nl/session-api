<?php 
    return [ 
			
		// 	'defaults' => [
    //     'guard' => 'api',
    //     'passwords' => 'users',
    // ],
    // 'guards' => [
    //     'api' => [
    //         'driver' => 'passport',
    //         'provider' => 'users',
    //     ],
    // ],
	
		// 	'api' => [
		// 			'driver' => 'passport',
		// 			'provider' => 'users',
		// 	],
	
		'defaults' => [
			'guard' => 'api',
			'passwords' => 'users',
	],
	
	
	'guards' => [
			'api' => [
					'driver' => 'jwt',
					'provider' => 'users',
			],
	],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ]
    ]
];