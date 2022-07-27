<?php

namespace Kirby\Field;

use Kirby\Field\Prop\Options;
use Kirby\Value\OptionValue;

/**
 * @covers \Kirby\Field\OptionField
 */
class OptionFieldTest extends TestCase
{
	/**
	 * @covers ::__construct
	 */
	public function testConstruct()
	{
		$field = new OptionField('test');

		$this->assertNull($field->default);
		$this->assertNull($field->options);
		$this->assertInstanceOf(Options::class, $field->options());
		$this->assertInstanceOf(OptionValue::class, $field->value);
	}

}