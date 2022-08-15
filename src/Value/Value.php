<?php

namespace Kirby\Value;

use Kirby\Cms\ModelWithContent;
use Kirby\Validation\Validations;

/**
 * Value
 *
 * @package   Kirby Value
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
abstract class Value
{
	public Validations $validations;

	public function __construct(
		public bool $required = false
	) {
		$this->validations = new Validations();
		$this->validations->add('required', $this->required);
	}

	public function __toString(): string
	{
		return (string)$this->data;
	}

	public function errors(): array
	{
		return $this->validations()->errors($this->data);
	}

	public static function factory($data = null): static
	{
		return (new static())->set($data);
	}

	public function isEmpty(): bool
	{
		return $this->data === null;
	}

	public function render(ModelWithContent $model): mixed
	{
		return $this->data;
	}

	abstract public function set(): static;

	public function submit(
		mixed $data = null,
		ModelWithContent|null $model = null
	): static {
		$clone = clone $this;
		$clone->set($data);
		$clone->validate($model);

		$this->data = $clone->data;

		return $this;
	}

	public function validate(ModelWithContent|null $model = null): bool
	{
		return $this->validations()->validate($this->data, $model);
	}

	public function validations(): Validations
	{
		$this->validations ??= new Validations();

		// only validate the required state when the value is empty
		if ($this->isEmpty() === true) {
			return $this->validations->only('required');
		}

		return $this->validations;
	}
}