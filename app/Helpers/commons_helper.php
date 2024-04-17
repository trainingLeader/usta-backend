<?php
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

function isProduction () {
    return getenv('CI_ENVIRONMENT') == 'production';
};
?>