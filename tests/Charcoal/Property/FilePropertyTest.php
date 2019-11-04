<?php

namespace Charcoal\Tests\Property;

use PDO;
use InvalidArgumentException;
use ReflectionClass;

// From 'charcoal-property'
use Charcoal\Property\FileProperty;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\ReflectionsTrait;
use Charcoal\Tests\Property\ContainerIntegrationTrait;

/**
 *
 */
class FilePropertyTest extends AbstractTestCase
{
    use ReflectionsTrait;
    use ContainerIntegrationTrait;

    /**
     * @var FileProperty
     */
    public $obj;

    /**
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new FileProperty([
            'database'   => $container['database'],
            'logger'     => $container['logger'],
            'translator' => $container['translator'],
            'container'  => $container,
        ]);
    }

    /**
     * @return void
     */
    public function testConstructor()
    {
        $obj = $this->obj;
        $this->assertInstanceOf('\Charcoal\Property\FileProperty', $obj);
        $this->assertEquals('uploads/', $obj['uploadPath']);
        $this->assertFalse($obj['overwrite']);
        $this->assertEquals([], $obj['acceptedMimetypes']);
        $this->assertEquals($obj->maxFilesizeAllowedByPhp(), $obj['maxFilesize']);
    }

    /**
     * Asserts that the `type()` method is "file".
     *
     * @return void
     */
    public function testType()
    {
        $obj = $this->obj;
        $this->assertEquals('file', $obj->type());
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $obj = $this->obj;
        $ret = $obj->setData([
            'public_access'     => true,
            'uploadPath'        => 'uploads/foobar/',
            'overwrite'         => true,
            'acceptedMimetypes' => ['image/x-foobar'],
            'maxFilesize'       => (32 * 1024 * 1024),
        ]);
        $this->assertSame($ret, $obj);

        $this->assertTrue($this->obj['publicAccess']);
        $this->assertEquals('uploads/foobar/', $this->obj['uploadPath']);
        $this->assertTrue($this->obj['overwrite']);
        $this->assertEquals(['image/x-foobar'], $this->obj['acceptedMimetypes']);
        $this->assertEquals((32 * 1024 * 1024), $this->obj['maxFilesize']);
    }

    /**
     * Asserts that the uploadPath method
     * - defaults to 'uploads/'
     * - always append a "/"
     *
     * @return void
     */
    public function testSetUploadPath()
    {
        $obj = $this->obj;
        $this->assertEquals('uploads/', $this->obj['uploadPath']);

        $ret = $obj->setUploadPath('foobar');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foobar/', $obj['uploadPath']);

        $this->obj['upload_path'] = 'foo';
        $this->assertEquals('foo/', $obj['uploadPath']);

        $this->obj->set('upload_path', 'bar');
        $this->assertEquals('bar/', $obj['upload_path']);

        $this->expectException(\InvalidArgumentException::class);
        $obj->setUploadPath(42);
    }

    /**
     * @return void
     */
    public function testSetOverwrite()
    {
        $ret = $this->obj->setOverwrite(true);
        $this->assertSame($ret, $this->obj);
        $this->assertTrue($this->obj['overwrite']);

        $this->obj['overwrite'] = false;
        $this->assertFalse($this->obj['overwrite']);

        $this->obj->set('overwrite', true);
        $this->assertTrue($this->obj['overwrite']);
    }

    /**
     * @return void
     */
    public function testVaidationMethods()
    {
        $obj = $this->obj;
        $ret = $obj->validationMethods();
        $this->assertContains('acceptedMimetypes', $ret);
        $this->assertContains('maxFilesize', $ret);
    }

    /**
     * @return void
     */
    public function testValidateAcceptedMimetypes()
    {
        $obj = $this->obj;
        $obj->setMimetype('image/x-foobar');
        $this->assertTrue($obj->validateAcceptedMimetypes());

        $this->assertEmpty($obj['acceptedMimetypes']);
        $this->assertTrue($obj->validateAcceptedMimetypes());

        $obj->setAcceptedMimetypes(['image/x-barbaz']);
        $this->assertFalse($obj->validateAcceptedMimetypes());

        $obj->setAcceptedMimetypes(['image/x-foobar']);
        $this->assertTrue($obj->validateAcceptedMimetypes());
    }

    /**
     * @return void
     */
    public function testFileExists()
    {
        $obj = $this->obj;
        $this->assertTrue($obj->fileExists(__FILE__));

        // $this->assertTrue($obj->fileExists(strtolower(__FILE__), true));
        // $this->assertTrue($obj->fileExists(strtoupper(__FILE__), true));

        $this->assertFalse($obj->fileExists('foobar/baz/42'));
    }

    /**
     * @dataProvider providePathsForIsAbsolutePath
     *
     * @param  string $path     A path to test.
     * @param  string $expected Whether the path is absolute (TRUE) or relative (FALSE).
     * @return void
     */
    public function testIsAbsolutePath($path, $expected)
    {
        $result = $this->callMethodWith($this->obj, 'isAbsolutePath', $path);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function providePathsForIsAbsolutePath()
    {
        return [
            [ '/var/lib',       true  ],
            [ 'c:\\\\var\\lib', true  ],
            [ '\\var\\lib',     true  ],
            [ 'var/lib',        false ],
            [ '../var/lib',     false ],
            [ '',               false ],
            [ null,             false ],
        ];
    }

    /**
     * @dataProvider filenameProvider
     *
     * @param  string $filename  A dirty filename.
     * @param  string $sanitized A clean version of $filename.
     * @return void
     */
    public function testSanitizeFilename($filename, $sanitized)
    {
        $obj = $this->obj;
        $this->assertEquals($sanitized, $obj->sanitizeFilename($filename));
    }

    /**
     * @return array
     */
    public function filenameProvider()
    {
        return [
            [ 'foobar',              'foobar'              ],
            [ '<foo/bar*baz?x:y|z>', '_foo_bar_baz_x_y_z_' ],
            [ '.htaccess',           'htaccess'            ],
            [ '../../etc/passwd',    '_.._etc_passwd'      ],
        ];
    }

    /**
     * @return void
     */
    // public function testGenerateFilenameWithoutIdentThrowsException()
    // {
    //     $obj = $this->obj;
    //     $this->expectException('\Exception');
    //     $obj->generateFilename();
    // }

    /**
     * @return void
     */
    public function testGenerateFilename()
    {
        $obj = $this->obj;
        $obj->setIdent('foo');
        $ret = $obj->generateFilename();
        $this->assertContains('Foo', $ret);
        $this->assertContains(date('Y-m-d'), $ret);

        //$obj->setLabel('foobar');
        //$ret = $obj->generateFilename();
        //$this->assertContains('foobar', $ret);
    }

    public function testGenerateUniqueFilename()
    {
        $ret = $this->obj->generateUniqueFilename('foo.png');
        $this->assertContains('foo', $ret);
        $this->assertStringEndsWith('.png', $ret);
        $this->assertNotEquals($ret, 'foo');
    }

    public function testFilesystem()
    {
        $this->assertEquals('public', $this->obj['filesystem']);

        $ret = $this->obj->setFilesystem('foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo', $this->obj['filesystem']);
    }

    public function testSetMimetype()
    {
        $ret = $this->obj->setMimetype('foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo', $this->obj->mimetype());

        $this->obj->setMimetype(null);
        $this->assertEquals('', $this->obj->mimetype());

        $this->obj->setMimetype(false);
        $this->assertEquals('', $this->obj->mimetype());

        $this->expectException(InvalidArgumentException::class);
        $this->obj->setMimetype([]);
    }

    /**
     * @return void
     */
    public function testSqlExtra()
    {
        $this->assertEquals('', $this->obj->sqlExtra());
    }

    /**
     * @return void
     */
    public function testSqlType()
    {
        $this->obj->setMultiple(false);
        $this->assertEquals('VARCHAR(255)', $this->obj->sqlType());

        $this->obj->setMultiple(true);
        $this->assertEquals('TEXT', $this->obj->sqlType());
    }

    /**
     * @return void
     */
    public function testSqlPdoType()
    {
        $this->assertEquals(PDO::PARAM_STR, $this->obj->sqlPdoType());
    }
}
