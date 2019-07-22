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

        "/users" => [
            "permissions" => "a,l,c,r,u,d",
        ],
        "/roles" => [
            "permissions" => "a,l,c,r,u,d",
        ],
        "/opds" => [
            "permissions" => "a,l,c,r,u,d",
        ],

        "/kotas" => [
            "permissions" => "a,l,c,r,u,d",
        ],
        "/kecamatans" => [
            "permissions" => "a,l,c,r,u,d",
        ],
        "/kelurahans" => [
            "permissions" => "a,l,c,r,u,d",
        ],

        "/status-laporans" => [
            "permissions" => "a,l,c,r,u,d",
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
