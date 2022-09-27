<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\searchToolKit;

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
        $this->tookKit = new searchToolKit();
    }

    public function test_sqlEscape()
    {
        $returnValue = $this->tookKit->sqlEscape("100% りんごジュース_山田林業");
        $this->assertEquals($returnValue,"100\% りんごジュース\_山田林業");
    }

    public function test_preparationToAndSearch()
    {
        $returnValue = $this->tookKit->preparationToAndSearch("林檎 いちご ブルーベリ");
        $this->assertContains("林檎", $returnValue);
        $this->assertContains("いちご", $returnValue);
        $this->assertContains("ブルーベリ", $returnValue);
    }
}
