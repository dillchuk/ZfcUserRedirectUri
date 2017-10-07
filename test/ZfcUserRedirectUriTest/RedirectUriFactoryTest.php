<?php

namespace ZfcUserRedirectUriTest;

use ZfcUserRedirectUri\RedirectUriCallback;
use ZfcUserRedirectUri\RedirectUriCallbackFactory;
use Zend\Mvc\Application;
use Zend\Router\RouteInterface;
use Interop\Container\ContainerInterface;
use ZfcUser\Options\ModuleOptions;

class RedirectUriFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testFactory() {
        $application = $this->getMock(Application::class, [], [], '', false);
        $router = $this->getMock(RouteInterface::class, [], [], '', false);
        $moduleOptions = new ModuleOptions;

        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->any())->method('get')->willReturnMap([
            ['Application', $application],
            ['Router', $router],
            ['zfcuser_module_options', $moduleOptions],
        ]);
        $factory = new RedirectUriCallbackFactory;
        $this->assertInstanceOf(
        RedirectUriCallback::class, $factory($container, '')
        );
    }

}
