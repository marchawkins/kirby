<?php

namespace Kirby\Section\Prop;

use Kirby\Foundation\Enumeration;

/**
 * Page Status
 *
 * @package   Kirby Blueprint
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class PageStatus extends Enumeration
{
	public array $allowed = [
		'draft',
		'listed',
		'published',
		'unlisted',
		'unpublished'
	];

	public mixed $default = 'draft';

	public function hasAddButton(): bool
	{
		return in_array($this->value, ['draft', 'all']) === true;
	}

	public function isSortable(): bool
	{
		return in_array($this->value, ['listed', 'published', 'all']) === true;
	}
}