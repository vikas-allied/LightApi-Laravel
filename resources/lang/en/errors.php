<?php

/*return [
    'invalid_credentials' => 'Invalid Credentials',
    'user_not_found' => 'User not found',
    'unauthorized' => 'Unauthorized access',
    'validation_error' => 'There was an error with your data validation.',
    // Add other error messages here...
];*/


return [
    'invalid_credentials' => [
        'message' => 'Invalid Credentials',
        'status_code' => 401,
    ],
    'user_not_found' => [
        'message' => 'User not found',
        'status_code' => 404,
    ],
    'role_not_found' => [
        'message' => 'Role not found',
        'status_code' => 404,
    ],
    'unauthorized' => [
        'message' => 'Unauthorized access',
        'status_code' => 403,
    ],
    'validation_error' => [
        'message' => 'Validation error.',
        'status_code' => 422,
    ],
    'server_error' => [
        'message' => 'Internal Server error.',
        'status_code' => 500,
    ],

];

