<?php

namespace Kirby\Blueprint;

/**
 * Image object for users
 *
 * @package   Kirby Blueprint
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class UserBlueprintImage extends BlueprintImage
{
	public function __construct(
		...$args
	) {
		parent::__construct(...$args);

		$this->back  ??= 'black';
		$this->color ??= 'gray-500';
		$this->cover ??= true;
		$this->icon  ??= 'user';
		$this->query ??= 'user.avatar';
		$this->ratio ??= '1/1';
	}
}