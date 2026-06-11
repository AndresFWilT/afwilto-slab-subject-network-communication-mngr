<?php

return [
    'smtp_host'  => env('MAIL_CLIENT_SMTP_HOST', 'localhost'),
    'smtp_port'  => (int) env('MAIL_CLIENT_SMTP_PORT', 25),
    'pop3_host'  => env('MAIL_CLIENT_POP3_HOST', 'localhost'),
    'pop3_port'  => (int) env('MAIL_CLIENT_POP3_PORT', 110),
    'tls'        => (bool) env('MAIL_CLIENT_TLS', false),
    'domain'     => env('MAIL_CLIENT_DOMAIN', 'redes3.udistrital.edu.co'),
    'jwt_ttl'    => (int) env('MAIL_CLIENT_JWT_TTL', 3600),
];
