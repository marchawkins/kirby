<?php

namespace Kirby\Field\Prop;

use Kirby\Foundation\Enumeration;

/**
 * 24 or 12 h format
 *
 * @package   Kirby Field
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class TimeNotation extends Enumeration
{
	public array $allowed = [
		12,
		24,
	];

	public mixed $default = 24;

	public function display(): string
	{
		return $this->value === '24' ? 'HH:mm' : 'hh:mm a';
	}
}
