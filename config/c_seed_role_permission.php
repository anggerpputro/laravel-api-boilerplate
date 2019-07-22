<?php

return [
    "developer" => "all",

    "admin" => [
        "/register" => "all",
        "/login" => "all",
        "/logout" => "all",
        "/auth/refresh" => "all",
        "/auth/me" => "all",

        "/users" => "all",
        "/roles" => [
            "permissions" => "a,l,r",
        ],
        "/opds" => "all",

        "/kotas" => "all",
        "/kecamatans" => "all",
        "/kelurahans" => "all",

        "/status-laporans" => [
            "permissions" => "a,l,r",
        ],
    ],

    "verificator" => [
        "/register" => "all",
        "/login" => "all",
        "/logout" => "all",
        "/auth/refresh" => "all",
        "/auth/me" => "all",
    ],

    "opd" => [
        "/register" => "all",
        "/login" => "all",
        "/logout" => "all",
        "/auth/refresh" => "all",
        "/auth/me" => "all",
    ],
];
