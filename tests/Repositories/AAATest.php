<?php

namespace Starscy\Project\UnitTests\Repositories;

use PHPUnit\Framework\TestCase;


class MyClass {

    public function power($x, $y)
    {
        return pow($x, $y);
    }
}

class AAATest extends TestCase {
    public function testItWork()
    {
        
        $this->assertEquals(8, 8);
 }
}