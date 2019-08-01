<?php

namespace Charcoal\Tests\Property;

use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\ImageProperty;
use Charcoal\Tests\AbstractTestCase;

/**
 * ## TODOs
 * - 2015-03-12:
 */
class ImagePropertyTest extends AbstractTestCase
{
    use \Charcoal\Tests\Property\ContainerIntegrationTrait;

    /**
     * @var ImageProperty
     */
    public $obj;

    /**
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new ImageProperty([
            'database'   => $container['database'],
            'logger'     => $container['logger'],
            'translator' => $container['translator'],
        ]);
    }


    public function testDefaults()
    {
        $this->assertEquals([], $this->obj['effects']);
        $this->assertEquals(ImageProperty::DEFAULT_DRIVER_TYPE, $this->obj['driverType']);
    }
    /**
     * @return void
     */
    public function testType()
    {
        $this->assertEquals('image', $this->obj->type());
    }

    /**
     * @return void
     */
    public function testSetEffects()
    {
        $this->assertEquals([], $this->obj['effects']);
        $ret = $this->obj->setEffects([['type'=>'blur', 'sigma'=>'1']]);
        $this->assertSame($ret, $this->obj);

        $this->obj['effects'] = [['type'=>'blur', 'sigma'=>'1'], ['type'=>'revert']];
        $this->assertEquals(2, count($this->obj['effects']));

        $this->obj->set('effects', [['type'=>'blur', 'sigma'=>'1']]);
        $this->assertEquals(1, count($this->obj['effects']));

        $this->assertEquals(1, count($this->obj['effects']));
    }

    /**
     * @return void
     */
    public function testAddEffect()
    {
        $this->assertEquals(0, count($this->obj['effects']));

        $ret = $this->obj->addEffect(['type'=>'grayscale']);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(1, count($this->obj['effects']));

        $this->obj->addEffect(['type'=>'blur', 'sigma'=>1]);
        $this->assertEquals(2, count($this->obj['effects']));
    }

    public function testSetApplyEffects()
    {
        $this->assertEquals('save', $this->obj['applyEffects']);
        $this->assertTrue($this->obj->canApplyEffects('save'));
        $this->assertFalse($this->obj->canApplyEffects('upload'));

        $ret = $this->obj->setApplyEffects('never');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('never', $this->obj['applyEffects']);
        $this->assertTrue($this->obj->canApplyEffects('never'));

        $this->obj->setApplyEffects('');
        $this->assertEquals('save', $this->obj['applyEffects']);

        $this->obj->setApplyEffects(null);
        $this->assertEquals('save', $this->obj['applyEffects']);

        $this->obj->setApplyEffects(false);
        $this->assertEquals('never', $this->obj['applyEffects']);

        $this->obj->setApplyEffects('upload');
        $this->assertEquals('upload', $this->obj['applyEffects']);
        $this->assertTrue($this->obj->canApplyEffects('upload'));


        $this->expectException(\OutOfBoundsException::class);
        $this->obj->setApplyEffects('foobar');
    }

    public function testDriverType()
    {
        $this->assertEquals(ImageProperty::DEFAULT_DRIVER_TYPE, $this->obj['driverType']);
        $ret = $this->obj->setDriverType('foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo', $this->obj['driverType']);

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setDriverType(false);
    }

    public function testProcessEffects()
    {
        $ret = $this->obj->processEffects(null, []);
        $this->assertNull($ret);
    }

    public function testAcceptedMimetypes()
    {
        $ret = $this->obj['acceptedMimetypes'];
        $this->assertContains('image/png', $ret);
        $this->assertContains('image/jpg', $ret);
    }
}
