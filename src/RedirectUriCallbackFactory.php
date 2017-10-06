<?php

namespace ZfcUserRedirectUri;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RedirectUriCallbackFactory implements FactoryInterface {

    public function __invoke(
    ContainerInterface $container, $requestedName, array $options = null
    ) {
        return new RedirectUriCallback(
        $container->get('Application'), $container->get('Router'),
        $container->get('zfcuser_module_options')
        );
    }

}
