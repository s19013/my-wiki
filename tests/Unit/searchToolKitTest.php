<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Tools\searchToolKit;

class searchToolKitTest extends TestCase
{
    private $tookKit;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function setup():void
    {
        parent::setUp();
        $this->toolKit = new searchToolKit();
    }

    public function test_sqlEscape()
    {
        $returnValue = $this->toolKit->sqlEscape("100% りんごジュース_山田林業");
        $this->assertEquals($returnValue,"100\% りんごジュース\_山田林業");
    }

    public function test_separatedByWhiteSpace()
    {
        $returnValue = $this->toolKit->separatedByWhiteSpace("林檎 いちご ブルーベリ");
        $this->assertContains("林檎", $returnValue);
        $this->assertContains("いちご", $returnValue);
        $this->assertContains("ブルーベリ", $returnValue);
    }

    public function test_preparationToAndSearch()
    {
        $returnValue = $this->toolKit->preparationToAndSearch("100% りんごジュース_山田林業");
        $this->assertContains("100\%", $returnValue);
        $this->assertContains("りんごジュース\_山田林業", $returnValue);
    }
}
