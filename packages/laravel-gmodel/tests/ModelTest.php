<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testAttributeManipulation()
    {
        $model = new GenericModelStub;
        $model->name = 'foo';

        $this->assertEquals('foo', $model->name);
        $this->assertTrue(isset($model->name));
        unset($model->name);
        $this->assertEquals(null, $model->name);
        $this->assertFalse(isset($model->name));

        $model['name'] = 'foo';
        $this->assertTrue(isset($model['name']));
        unset($model['name']);
        $this->assertFalse(isset($model['name']));
    }

    public function testConstructor()
    {
        $model = new GenericModelStub(['name' => 'john']);
        $this->assertEquals('john', $model->name);
    }

    public function testNewInstanceWithAttributes()
    {
        $model = new GenericModelStub;
        $instance = $model->newInstance(['name' => 'john']);

        $this->assertInstanceOf('ModelStub', $instance);
        $this->assertEquals('john', $instance->name);
    }

    public function testHidden()
    {
        $model = new GenericModelStub;
        $model->password = 'secret';

        $attributes = $model->attributesToArray();
        $this->assertFalse(isset($attributes['password']));
        $this->assertEquals(['password'], $model->getHidden());
    }

    public function testVisible()
    {
        $model = new GenericModelStub;
        $model->setVisible(['name']);
        $model->name = 'John Doe';
        $model->city = 'Paris';

        $attributes = $model->attributesToArray();
        $this->assertEquals(['name' => 'John Doe'], $attributes);
    }

    public function testToArray()
    {
        $model = new GenericModelStub;
        $model->name = 'foo';
        $model->bar = null;
        $model->password = 'password1';
        $model->setHidden(['password']);
        $array = $model->toArray();

        $this->assertTrue(is_array($array));
        $this->assertEquals('foo', $array['name']);
        $this->assertFalse(isset($array['password']));
        $this->assertEquals($array, $model->jsonSerialize());

        $model->addHidden(['name']);
        $model->addVisible('password');
        $array = $model->toArray();
        $this->assertTrue(is_array($array));
        $this->assertFalse(isset($array['name']));
        $this->assertTrue(isset($array['password']));
    }

    public function testToJson()
    {
        $model = new GenericModelStub;
        $model->name = 'john';
        $model->foo = 10;

        $object = new stdClass;
        $object->name = 'john';
        $object->foo = 10;

        $this->assertEquals(json_encode($object), $model->toJson());
        $this->assertEquals(json_encode($object), (string) $model);
    }

    public function testMutator()
    {
        $model = new GenericModelStub;
        $model->list_items = ['name' => 'john'];
        $this->assertEquals(['name' => 'john'], $model->list_items);
        $attributes = $model->getAttributes();
        $this->assertEquals(json_encode(['name' => 'john']), $attributes['list_items']);

        $birthday = strtotime('245 months ago');

        $model = new GenericModelStub;
        $model->birthday = '245 months ago';

        $this->assertEquals(date('Y-m-d', $birthday), $model->birthday);
        $this->assertEquals(20, $model->age);
    }

    public function testToArrayUsesMutators()
    {
        $model = new GenericModelStub;
        $model->list_items = [1, 2, 3];
        $array = $model->toArray();

        $this->assertEquals([1, 2, 3], $array['list_items']);
    }

    public function testReplicate()
    {
        $model = new GenericModelStub;
        $model->name = 'John Doe';
        $model->city = 'Paris';

        $clone = $model->replicate();
        $this->assertEquals($model, $clone);
        $this->assertEquals($model->name, $clone->name);
    }

    public function testAppends()
    {
        $model = new GenericModelStub;
        $array = $model->toArray();
        $this->assertFalse(isset($array['test']));

        $model = new GenericModelStub;
        $model->setAppends(['test']);
        $array = $model->toArray();
        $this->assertTrue(isset($array['test']));
        $this->assertEquals('test', $array['test']);
    }

    public function testArrayAccess()
    {
        $model = new GenericModelStub;
        $model->name = 'John Doen';
        $model['city'] = 'Paris';

        $this->assertEquals($model->name, $model['name']);
        $this->assertEquals($model->city, $model['city']);
    }

    public function testSerialize()
    {
        $model = new GenericModelStub;
        $model->name = 'john';
        $model->foo = 10;

        $serialized = serialize($model);
        $this->assertEquals($model, unserialize($serialized));
    }

    public function testCasts()
    {
        $model = new GenericModelStub;
        $model->score = '0.34';
        $model->data = ['foo' => 'bar'];
        $model->count = 1;
        $model->object_data = ['foo' => 'bar'];
        $model->active = 'true';
        $model->default = 'bar';
        $model->collection_data = [['foo' => 'bar', 'baz' => 'bat']];

        $this->assertTrue(is_float($model->score));
        $this->assertTrue(is_array($model->data));
        $this->assertTrue(is_bool($model->active));
        $this->assertTrue(is_int($model->count));
        $this->assertEquals('bar', $model->default);
        $this->assertInstanceOf('\stdClass', $model->object_data);
        $this->assertInstanceOf('\Illuminate\Support\Collection', $model->collection_data);

        $attributes = $model->getAttributes();
        $this->assertTrue(is_string($attributes['score']));
        $this->assertTrue(is_string($attributes['data']));
        $this->assertTrue(is_string($attributes['active']));
        $this->assertTrue(is_int($attributes['count']));
        $this->assertTrue(is_string($attributes['default']));
        $this->assertTrue(is_string($attributes['object_data']));
        $this->assertTrue(is_string($attributes['collection_data']));

        $array = $model->toArray();
        $this->assertTrue(is_float($array['score']));
        $this->assertTrue(is_array($array['data']));
        $this->assertTrue(is_bool($array['active']));
        $this->assertTrue(is_int($array['count']));
        $this->assertEquals('bar', $array['default']);
        $this->assertInstanceOf('\stdClass', $array['object_data']);
        $this->assertInstanceOf('\Illuminate\Support\Collection', $array['collection_data']);
    }

    public function testGuarded()
    {
        $model = new GenericModelStub(['secret' => 'foo']);
        $this->assertTrue($model->isGuarded('secret'));
        $this->assertNull($model->secret);
        $this->assertContains('secret', $model->getGuarded());

        $model->secret = 'bar';
        $this->assertEquals('bar', $model->secret);

        GenericModelStub::unguard();

        $this->assertTrue(GenericModelStub::isUnguarded());
        $model = new GenericModelStub(['secret' => 'foo']);
        $this->assertEquals('foo', $model->secret);

        GenericModelStub::reguard();
    }

    public function testGuardedCallback()
    {
        GenericModelStub::unguard();
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['callback'])
            ->getMock();
        $mock->expects($this->once())
            ->method('callback')
            ->will($this->returnValue('foo'));
        $string = GenericModelStub::unguarded([$mock, 'callback']);
        $this->assertEquals('foo', $string);
        GenericModelStub::reguard();
    }

    public function testTotallyGuarded()
    {
        $this->expectException('ArtisanLabs\GModel\MassAssignmentException');

        $model = new GenericModelStub();
        $model->guard(['*']);
        $model->fillable([]);
        $model->fill(['name' => 'John Doe']);
    }

    public function testFillable()
    {
        $model = new GenericModelStub(['foo' => 'bar']);
        $this->assertFalse($model->isFillable('foo'));
        $this->assertNull($model->foo);
        $this->assertNotContains('foo', $model->getFillable());

        $model->foo = 'bar';
        $this->assertEquals('bar', $model->foo);

        $model = new GenericModelStub;
        $model->forceFill(['foo' => 'bar']);
        $this->assertEquals('bar', $model->foo);
    }

    public function testHydrate()
    {
        $models = GenericModelStub::hydrate([['name' => 'John Doe']]);
        $this->assertEquals('John Doe', $models[0]->name);
    }
}
