<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\EtherTimeHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * Class EtherTimeHelperTest
 * @package App\Test\TestCase\View\Helper
 * @property EtherTimeHelper $EtherTime
 */
class EtherTimeHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->EtherTime = new EtherTimeHelper($View);
    }

    public function testAbbreviatedTimeAgoInWords()
    {
        // Five minutes ago
        $expected = '5 minutes ago';
        $timestamp = time() - (60 * 5);
        $time = date('Y-m-d H:i:s', $timestamp);
        $result = $this->EtherTime->abbreviatedTimeAgoInWords($time);
        $this->assertEquals($expected, $result);
    }
}