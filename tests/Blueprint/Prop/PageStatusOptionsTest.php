<?php

namespace Kirby\Blueprint\Prop;

use Kirby\Blueprint\TestCase;

/**
 * @covers \Kirby\Blueprint\PageStatusOptions
 */
class PageStatusOptionsTest extends TestCase
{
	/**
	 * @covers ::__construct
	 */
	public function testConstruct()
	{
		$status = new PageStatusOptions();

		$this->assertInstanceOf(PageStatusOption::class, $status->draft);
		$this->assertInstanceOf(PageStatusOption::class, $status->unlisted);
		$this->assertInstanceOf(PageStatusOption::class, $status->listed);
	}
}