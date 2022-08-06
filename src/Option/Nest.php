<?php

namespace Kirby\Option;

use Kirby\Cms\Field;

/**
 * The Nest class is used to convert any array type
 * into a Kirby style collection/object. This
 * can be used make any type of array compatible
 * with Kirby queries used by the Option package.
 *
 * @package   Kirby Option
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class Nest
{
	public static function create(mixed $data, mixed $parent = null): NestCollection|NestObject|Field
	{
		if (is_scalar($data) === true) {
			return new Field($parent, $data, $data);
		}

		$result = [];

		foreach ($data as $key => $value) {
			if (is_array($value) === true) {
				$result[$key] = static::create($value, $parent);
			} elseif (is_scalar($value) === true) {
				$result[$key] = new Field($parent, $key, $value);
			}
		}

		if (is_int(key($data)) === true) {
			return new NestCollection($result);
		}

		return new NestObject($result);
	}
}