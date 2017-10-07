<?php

namespace ZfcUserRedirectUriTest;

use ZfcUserRedirectUri\RedirectUriCallback;
use Zend\Mvc\Application;
use Zend\Router\RouteInterface;
use Zend\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use ZfcUser\Options\ModuleOptions;
use Zend\Http\Request;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\Stdlib\Parameters;

class RedirectUriTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider dataUri
     */
    public function testUri($expected, array $options) {
        $options = new Parameters($options);


        $mvcEvent = $this->getMock(MvcEvent::class);
        $mvcRouteMatch = $options->mvcRouteMatch? : new RouteMatch([]);
        $mvcEvent->expects($this->any())->method('getRouteMatch')->willReturn($mvcRouteMatch);

        $request = new Request;
        $request->setQuery(new Parameters(['redirect' => $options->uri . $options->query]));

        $headers = new Headers;

        $response = $this->getMock(Response::class, ['getHeaders']);
        $response->expects($this->any())->method('getHeaders')->willReturn($headers);

        $application = $this->getMock(Application::class, [], [], '', false);
        $application->expects($this->any())->method('getMvcEvent')->willReturn($mvcEvent);
        $application->expects($this->any())->method('getRequest')->willReturn($request);
        $application->expects($this->any())->method('getResponse')->willReturn($response);

        /**
         * Matched route may simply parrot back URI (without query string).
         */
        if ($options->routeMatch && !$options->routeMatch->getMatchedRouteName()) {
            $options->routeMatch->setMatchedRouteName($options->uri);
        }
        $router = $this->getMock(RouteInterface::class, [], [], '', false);
        $router->expects($this->any())
        ->method('match')->willReturn($options->routeMatch);

        $router->expects($this->any())
        ->method('assemble')->willReturnCallback(function() {
            $args = func_get_args();
            $route = $args[1]['name'];
            switch ($route) {
                case 'zfcuser': return '/post/default';
                case 'post/login': return '/post/login';
                case 'post/logout': return '/post/logout';
            }
            return $route;
        });

        $moduleOptions = $options->moduleOptions? : new ModuleOptions;
        $callback = new RedirectUriCallback(
        $application, $router, $moduleOptions
        );
        $callback();

        $this->assertCount(1, $response->getHeaders());
        $this->assertEquals(
        $expected, $response->getHeaders()->current()->getUri()
        );
        $this->assertEquals(302, $response->getStatusCode());
    }

    public static function dataUri() {

        $moduleOptions = new ModuleOptions;
        $moduleOptions->setLoginRedirectRoute('post/login');
        $moduleOptions->setLogoutRedirectRoute('post/logout');

        $moduleOptionsNoRedirect = clone $moduleOptions;
        $moduleOptionsNoRedirect->setUseRedirectParameterIfPresent(false);

        $routeMatch = new RouteMatch([]);

        $routeMatchLogin = clone $routeMatch;
        $routeMatchLogin->setMatchedRouteName('zfcuser/login');

        $routeMatchRegister = clone $routeMatch;
        $routeMatchRegister->setMatchedRouteName('zfcuser/register');

        $routeMatchLogout = clone $routeMatch;
        $routeMatchLogout->setMatchedRouteName('zfcuser/logout');

        return [
            ['/user/1',
                [
                    'uri' => '/user/1',
                    'routeMatch' => clone $routeMatch
                ]
            ],
            ['/user/2?test',
                [
                    'uri' => '/user/2',
                    'routeMatch' => clone $routeMatch,
                    'query' => '?test'
                ]
            ],
            [ '/user/3?test&field=yes',
                [
                    'uri' => '/user/3',
                    'routeMatch' => clone $routeMatch,
                    'query' => '?test&field=yes'
                ]
            ],
            ['/post/default',
                [
                    'uri' => '/user/1?test',
                    'routeMatch' => clone $routeMatch,
                    'moduleOptions' => $moduleOptionsNoRedirect
                ]
            ],
            ['/post/login',
                [
                    'uri' => '/junk/1',
                    'mvcRouteMatch' => $routeMatchLogin,
                    'moduleOptions' => $moduleOptions
                ]
            ],
            ['/post/logout',
                [
                    'uri' => '/junk/1',
                    'mvcRouteMatch' => $routeMatchLogout,
                    'moduleOptions' => $moduleOptions
                ]
            ],
        ];
    }

}
