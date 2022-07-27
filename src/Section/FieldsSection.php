<?php

namespace Kirby\Section;

use Kirby\Field\Fields;

/**
 * Fields section
 *
 * @package   Kirby Section
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class FieldsSection extends Section
{
	public const TYPE = 'fields';

	public function __construct(
		public string $id,
		public Fields|null $fields = null,
	) {
	}
}