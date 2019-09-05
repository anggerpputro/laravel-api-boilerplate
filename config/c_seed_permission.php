<?php

return [
    "routes" => [
        "/register" => [
            "permissions" => "a",
        ],
        "/login" => [
            "permissions" => "a",
        ],
        "/logout" => [
            "permissions" => "a",
        ],

        "/auth/refresh" => [
            "permissions" => "a",
        ],
        "/auth/me" => [
            "permissions" => "a",
        ],
    ],

    "permissions" => [
        "a" => "access",
        "l" => "list",
        "c" => "create",
        "r" => "read",
        "u" => "update",
        "d" => "delete",
    ]
];
