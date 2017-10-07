<?php

namespace ZfcUserRedirectUriTest;

use ZfcUserRedirectUri\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase {

    public function testConfig() {
        $module = new Module;
        $config = $module->getConfig();
        $this->assertTrue(isset($config['service_manager']['factories']));
        $this->assertTrue(isset($config['service_manager']['aliases']));
    }

}
