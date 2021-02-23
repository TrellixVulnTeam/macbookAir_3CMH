Class 'Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory' not found

-> this problem ocurred when I tried to use guzzle

# How to solve:
    composer dump-autoload
    composer require symfony/psr-http-message-bridge    