<?php

namespace atoum\atoum\tests\units\cli\command;

require_once __DIR__ . '/../../../runner.php';

use atoum\atoum
;

class exception extends atoum
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\exceptions\runtime::class);
    }
}
