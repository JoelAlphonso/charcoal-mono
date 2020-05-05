<?php

namespace Charcoal\Tests\Email;

use ReflectionClass;

use Charcoal\Tests\AbstractTestCase;

/**
 * Class EmailAwareTraitTest
 */
class EmailAwareTraitTest extends AbstractTestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = $this->getMockForTrait('\Charcoal\Email\EmailAwareTrait');
    }

    public function getMethod($obj, $name)
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @dataProvider emailToArrayProvider
     */
    public function testEmailToArray($val, $exp)
    {
        $method = $this->getMethod($this->obj, 'emailToArray');
        $res = $method->invokeArgs($this->obj, [$val]);
        $this->assertEquals($res, $exp);
    }

    public function emailToArrayProvider()
    {
        return [
            ['mat@locomotive.ca', ['email'=>'mat@locomotive.ca', 'name'=>'']],
            ['Mathieu <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu']],
            ["'Mathieu' <mat@locomotive.ca>", ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu']],
            ['"Mathieu Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'Mathieu Mémo']],
            ['"M_athieu-Mémo" <mat@locomotive.ca>', ['email'=>'mat@locomotive.ca', 'name'=>'M_athieu-Mémo']],
            ['Alertes Mathieu-Loco <alertes@loco-motive_123.ca>', ['email'=>'alertes@loco-motive_123.ca', 'name'=>'Alertes Mathieu-Loco']],
            ['longtld@museum.com', ['email'=>'longtld@museum.com', 'name'=>'']],
            ['"Long TLD" <longtld@museum.com>', ['email'=>'longtld@museum.com', 'name'=>'Long TLD']],
            ['a.b-c-@d.e.f-g.com', ['email'=>'a.b-c-@d.e.f-g.com', 'name'=>'']],
            ['mat+1@locomotive.ca', ['email'=>'mat+1@locomotive.ca', 'name'=>'']],
            ['Mathieu <mat+1@locomotive.ca>', ['email'=>'mat+1@locomotive.ca', 'name'=>'Mathieu']],
            ['Name.with.dot <name-with-dot@test.justatest>', ['email'=>'name-with-dot@test.justatest', 'name'=>'Name.with.dot']]
        ];
    }
}
