<?php

namespace Charcoal\Tests\Ui\Form;

use \Charcoal\Ui\Form\GenericForm;
use \Charcoal\Ui\FormGroup\FormGroupBuilder;

/**
 *
 */
class AbstractFormGroupTest extends \PHPUnit_Framework_TestCase
{
    public $container;

    /**
     * @var AbstractViewClass $obj
     */
    public $obj;

    /**
     *
     */
    public function setUp()
    {
        $container = new \Pimple\Container();
        $container->register(new \Charcoal\Ui\ServiceProvider\FormServiceProvider());
        $container->register(new \Charcoal\Ui\ServiceProvider\LayoutServiceProvider());

        $container['logger'] = new \Psr\Log\NullLogger();
        $container['view'] = null;

        $this->container = $container;

        $form = $container['form/builder']->build([
            'type'=>null
        ]);

        $this->obj = $this->getMockForAbstractClass('\Charcoal\Ui\FormGroup\AbstractFormGroup', [[
            'form'               => $form,
            'logger'             => $container['logger'],
            'view'               => $container['view'],
            'layout_builder'     => $container['layout/builder'],
            'form_input_builder' => $container['form/input/builder']
        ]]);
    }

    public function testSetInputCallback()
    {
        $obj = $this->obj;
        $cb = function($o) {
            return 'foo';

        };
        $ret = $obj->setInputCallback($cb);
        $this->assertSame($ret, $obj);
    }

    public function testSetInputs()
    {
        $obj = $this->obj;
        $ret = $obj->setInputs([
            'test'=>[]
        ]);
        $this->assertSame($ret, $obj);
    }

    public function testHasInputs()
    {
        $obj = $this->obj;
        $this->assertFalse($obj->hasInputs());

        $ret = $obj->setInputs([
            'test'=>[]
        ]);

        $this->assertTrue($obj->hasInputs());
    }

    public function testNumInput()
    {
        $obj = $this->obj;
        $this->assertEquals(0, $obj->numInputs());

        $ret = $obj->setInputs([
            'test'=>[],
            'foobar'=>[]
        ]);

         $this->assertEquals(2, $obj->numInputs());
    }
}
