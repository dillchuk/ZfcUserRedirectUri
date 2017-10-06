<?php

namespace ZfcUserRedirectUri;

return [
    'service_manager' => [
        'factories' => [
            RedirectUriCallback::class => RedirectUriCallbackFactory::class,
        ],
        'aliases' => [
            'zfcuser_redirect_callback' => RedirectUriCallback::class,
        ],
    ],
];
