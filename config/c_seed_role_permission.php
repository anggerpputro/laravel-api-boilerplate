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
    ],
];
