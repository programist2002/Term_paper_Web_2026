<?php
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
	private $categoryModel;

	public function __construct()
	{
		$this->categoryModel = new Category();
	}

	public function getCategories(): array
	{
		return $this->categoryModel->findAll();
	}

	public function getCategoryById(int $id)
	{
		return $this->categoryModel->findById($id);
	}

	public function getCategoryBySlug(string $slug)
	{
		return $this->categoryModel->findBySlug($slug);
	}

	public function createCategory(array $data): array
	{
		$validationError = $this->validateCategoryData($data);

		if ($validationError !== null) {
			return ['success' => false, 'error' => $validationError];
		}

		$slug = $this->resolveSlug($data);

		if ($this->categoryModel->slugExists($slug)) {
			return ['success' => false, 'error' => 'Категорія з таким slug уже існує.'];
		}

		$data['slug'] = $slug;
		$category = $this->categoryModel->create($data);

		return ['success' => true, 'category' => $category];
	}

	public function updateCategory(int $id, array $data): array
	{
		$existingCategory = $this->categoryModel->findById($id);

		if (!$existingCategory) {
			return ['success' => false, 'error' => 'Категорію не знайдено.'];
		}

		$validationError = $this->validateCategoryData($data);

		if ($validationError !== null) {
			return ['success' => false, 'error' => $validationError];
		}

		$slug = $this->resolveSlug($data, (string) $existingCategory['name']);

		if ($this->categoryModel->slugExists($slug, $id)) {
			return ['success' => false, 'error' => 'Категорія з таким slug уже існує.'];
		}

		$data['slug'] = $slug;
		$category = $this->categoryModel->update($id, $data);

		return ['success' => true, 'category' => $category];
	}

	public function deleteCategory(int $id): array
	{
		$existingCategory = $this->categoryModel->findById($id);

		if (!$existingCategory) {
			return ['success' => false, 'error' => 'Категорію не знайдено.'];
		}

		$deleted = $this->categoryModel->delete($id);

		if (!$deleted) {
			return ['success' => false, 'error' => 'Не вдалося видалити категорію.'];
		}

		return ['success' => true];
	}

	private function validateCategoryData(array $data): ?string
	{
		$name = trim((string) ($data['name'] ?? ''));

		if ($name === '') {
			return 'Назва категорії є обов’язковою.';
		}

		return null;
	}

	private function resolveSlug(array $data, string $fallbackName = ''): string
	{
		$slug = trim((string) ($data['slug'] ?? ''));

		if ($slug !== '') {
			return $this->slugify($slug);
		}

		$name = trim((string) ($data['name'] ?? ''));

		return $this->slugify($name !== '' ? $name : $fallbackName);
	}

	private function slugify(string $value): string
	{
		$value = strtolower(trim($value));
		$value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
		$value = trim($value, '-');

		return $value !== '' ? $value : 'category';
	}
}