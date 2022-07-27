<?php

namespace Kirby\Section;

use Kirby\Blueprint\Prop\Help;
use Kirby\Blueprint\Prop\Image;
use Kirby\Blueprint\Prop\Label;
use Kirby\Blueprint\Prop\Text;
use Kirby\Cms\Collection as Models;
use Kirby\Cms\File;
use Kirby\Cms\ModelWithContent;
use Kirby\Cms\Page;
use Kirby\Cms\Pagination;
use Kirby\Cms\Site;
use Kirby\Cms\User;
use Kirby\Section\Prop\Layout;
use Kirby\Section\Prop\Related;
use Kirby\Section\Prop\Size;
use Kirby\Table\TableColumn;
use Kirby\Table\TableColumns;

class ModelsSection extends Section
{
	public const TYPE = 'models';

	public function __construct(
		public string $id,
		public TableColumns|null $columns = null,
		public Text|null $empty = null,
		public bool $flip = false,
		public Help|null $help = null,
		public Image|null $image = null,
		public Text|null $info = null,
		public Label|null $label = null,
		public Layout|null $layout = null,
		public int $limit = 20,
		public int|null $max = null,
		public int $min = 0,
		public int $page = 1,
		public Related|null $parent = null,
		public bool $search = false,
		public Size|null $size = null,
		public bool $sortable = true,
		public string|null $sortBy = null,
		public Text|null $text = null,
		...$args
	) {
		parent::__construct($id, ...$args);

		$this->label ??= Label::fallback($id);
	}

	public function add(ModelWithContent $model, Models $models): bool
	{
		if ($this->isFull($models) === true) {
			return false;
		}

		return true;
	}

	public function applyFlip(Models $models): Models
	{
		if ($this->flip === false) {
			return $models;
		}

		return $models->flip();
	}

	public function applyPagination(Models $models, array $query = []): Models
	{
		return $models->paginate([
			'page'   => $query['page'] ?? $this->page,
			'limit'  => $this->limit,
			'method' => 'none'
		]);
	}

	public function applySearch(Models $models, array $query = []): Models
	{
		if ($this->search === false) {
			return $models;
		}

		if (empty($query['searchterm']) === true) {
			return $models;
		}

		return $models->search($query['searchterm']);
	}

	public function applySort(Models $models): Models
	{
		if ($this->sortBy === null) {
			return $models;
		}

		return $models->sort(...$models::sortArgs($this->sortBy));
	}

	/**
	 * Creates the full columns collection for the
	 * table layout, including the default columns
	 */
	public function columns(): TableColumns
	{
		$columns = new TableColumns;

		if ($this->image) {
			$columns->add(new TableColumn(
				id: 'image',
				label: new Label(''),
				mobile: true,
				type: 'image',
				width: 'var(--table-row-height)'
			));
		}

		if ($this->text) {
			$columns->add(new TableColumn(
				id: 'title',
				label: new Label(['*' => 'title']),
				mobile: true,
				type: 'url',
			));
		}

		if ($this->info) {
			$columns->add(new TableColumn(
				id: 'info',
				label: new Label(['*' => 'info']),
				type: 'text',
			));
		}

		if ($this->columns) {
			$columns->add($this->columns);
		}

		return $columns;
	}

	/**
	 * Checks if the maximum number of models
	 * has already been added to the section
	 */
	public function isFull(Models $models): bool
	{
		if ($this->max === null) {
			return false;
		}

		return $models->pagination()->total() >= $this->max;
	}

	/**
	 * Renders the response for a single item.
	 * This will be handed over to the Vue components
	 * to render the item in the section
	 */
	public function item(ModelWithContent $model, Page|File $item): array
	{
		$panel = $item->panel();

		$render = [
			'dragText' => $panel->dragText(),
			'id'       => $item->id(),
			'image'    => $this->itemImage($item)?->render($item),
			'info'     => $this->info?->render($item),
			'link'     => $panel->url(true),
			'text'     => $this->text?->render($item),
		];

		if ($this->layout?->value === 'table') {
			$render += $this->itemCells($model, $item);
		}

		return $render;
	}

	public function itemCells(ModelWithContent $model, Page|File $item): array
	{
		// TODO: implement TableRows::render here
		return [];
	}

	public function itemImage(ModelWithContent $model): ?Image
	{
		return $model->blueprint()->image()?->merge($this->image);
	}

	public function items(ModelWithContent $model, Models $models, array $query = [])
	{
		return array_map(fn ($item) => $this->item($model, $item), $models->values());
	}

	/**
	 * Returns the correct link for the section label.
	 * If the model is the parent, the link will be empty,
	 * because the section is in the model panel view and
	 * the label should not be linked.
	 */
	public function link(ModelWithContent $model, File|Page|Site|User $parent): ?string
	{
		$modelLink  = $model->panel()->url(true);
		$parentLink = $parent->panel()->url(true);

		return $modelLink !== $parentLink ? $parentLink : null;
	}

	public function models(ModelWithContent $model, array $query = []): Models
	{
		return new Models;
	}

	public function options(ModelWithContent $model, Models $models, array $query): array
	{
		return [
			'add'      => $this->add($model, $models, $query),
			'layout'   => $this->layout?->value ?? 'list',
			'search'   => $this->search,
			'size'     => $this->size?->value,
			'sortable' => $this->sortable($model, $models, $query)
		];
	}

	public function pagination(ModelWithContent $model, Models $models, array $query = []): array
	{
		$pagination = $models->pagination();

		return [
			'limit'  => $pagination->limit(),
			'offset' => $pagination->offset(),
			'page'   => $pagination->page(),
			'total'  => $pagination->total()
		];
	}

	/**
	 * Get the parent model. If a parent query
	 * has been set, the model is queried. Otherwise
	 * the passed model is being used
	 */
	public function parent(ModelWithContent $model): File|Page|Site|User
	{
		if ($this->parent === null) {
			return $model;
		}

		// get the related model
		return $this->parent->model($model);
	}

	/**
	 * The basic section is only rendered with the label,
	 * a loading state for items and the help text.
	 * The rest will be loaded lazily.
	 */
	public function render(ModelWithContent $model): array
	{
		$parent = $this->parent($model);

		return [
			'help'  => $this->help?->render($model),
			'id'    => $this->id,
			'label' => $this->label->render($model),
			'link'  => $this->link($model, $parent),
			'type'  => static::TYPE
		];
	}

	public function routes(ModelWithContent $model): array
	{
		return [
			[
				'pattern' => '/',
				'action'  => function (array $query = []) use ($model) {
					$models = $this->models($model, $query);

					return [
						'data'       => $this->items($model, $models, $query),
						'options'    => $this->options($model, $models, $query),
						'pagination' => $this->pagination($model, $models, $query),
					];
				}
			]
		];
	}

	public function sortable(ModelWithContent $model, Models $models, array $query = []): bool
	{
		if ($this->sortable === false) {
			return false;
		}

		if ($this->sortBy !== null) {
			return false;
		}

		// don't allow sorting while search filter is active
		if ($this->search === true && empty($query['searchterm']) === false) {
			return false;
		}

		if ($this->flip === true) {
			return false;
		}

		return true;
	}

}