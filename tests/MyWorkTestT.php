<?php

namespace Starscy\Project\UnitTests;

use PHPUnit\Framework\TestCase;


class MyClass {

    public function power($x, $y)
    {
        return pow($x, $y);
    }
}

class MyWorkTestT extends TestCase {
    public function testItWork()
    {
        
        $this->assertEquals(8, 8);
 }
}