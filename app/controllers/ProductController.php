<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController
{
	private Product $productModel;

	public function __construct()
	{
		$this->productModel = new Product();
	}

	public function getProducts(): array
	{
		return $this->productModel->findAll();
	}

	public function getAvailableProducts(): array
	{
		return $this->productModel->findAvailable();
	}

	public function getProductsByCategory(int $categoryId): array
	{
		return $this->productModel->findByCategory($categoryId);
	}

	public function getProductById(int $id)
	{
		return $this->productModel->findById($id);
	}

	public function getProductBySlug(string $slug)
	{
		return $this->productModel->findBySlug($slug);
	}

	public function createProduct(array $data): array
	{
		$validationError = $this->validateProductData($data);

		if ($validationError !== null) {
			return ['success' => false, 'error' => $validationError];
		}

		$slug = $this->resolveSlug($data);

		if ($this->productModel->slugExists($slug)) {
			return ['success' => false, 'error' => 'Товар із таким slug уже існує.'];
		}

		$data['slug'] = $slug;

		try {
			$product = $this->productModel->create($data);
		} catch (PDOException $exception) {
			return $this->handlePersistenceException($exception);
		}

		return ['success' => true, 'product' => $product];
	}

	public function updateProduct(int $id, array $data): array
	{
		$existingProduct = $this->productModel->findById($id);

		if (!$existingProduct) {
			return ['success' => false, 'error' => 'Товар не знайдено.'];
		}

		$validationError = $this->validateProductData($data, true);

		if ($validationError !== null) {
			return ['success' => false, 'error' => $validationError];
		}

		$slug = $this->resolveSlug($data, (string) $existingProduct['name']);

		if ($this->productModel->slugExists($slug, $id)) {
			return ['success' => false, 'error' => 'Товар із таким slug уже існує.'];
		}

		$data['slug'] = $slug;

		try {
			$product = $this->productModel->update($id, $data);
		} catch (PDOException $exception) {
			return $this->handlePersistenceException($exception);
		}

		return ['success' => true, 'product' => $product];
	}

	public function deleteProduct(int $id): array
	{
		$existingProduct = $this->productModel->findById($id);

		if (!$existingProduct) {
			return ['success' => false, 'error' => 'Товар не знайдено.'];
		}

		$deleted = $this->productModel->delete($id);

		if (!$deleted) {
			return ['success' => false, 'error' => 'Не вдалося видалити товар.'];
		}

		return ['success' => true];
	}

	private function validateProductData(array $data, bool $isUpdate = false): ?string
	{
		$name = trim((string) ($data['name'] ?? ''));
		$manufacturer = trim((string) ($data['manufacturer'] ?? ''));
		$image = trim((string) ($data['image'] ?? ''));
		$price = $data['price'] ?? null;
		$quantity = $data['quantity'] ?? null;

		if ($name === '') {
			return 'Назва товару є обов’язковою.';
		}

		if ($manufacturer === '') {
			return 'Виробник товару є обов’язковим.';
		}

		if (!$isUpdate && trim((string) ($data['short_description'] ?? '')) === '' && trim((string) ($data['description'] ?? '')) === '') {
			return 'Додайте коротку або повну інформацію про товар.';
		}

		if ($image !== '' && strlen($image) > PRODUCT_IMAGE_MAX_LENGTH) {
			return 'URL зображення занадто довгий. Використайте адресу до ' . PRODUCT_IMAGE_MAX_LENGTH . ' символів.';
		}

		if ($price !== null && $price !== '' && (!is_numeric($price) || (float) $price < 0)) {
			return 'Ціна товару не може бути від’ємною.';
		}

		if ($quantity !== null && $quantity !== '' && (!is_numeric($quantity) || (int) $quantity < 0)) {
			return 'Кількість товару не може бути від’ємною.';
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

		return $value !== '' ? $value : 'product';
	}

	private function handlePersistenceException(PDOException $exception): array
	{
		$message = $exception->getMessage();

		if (str_contains($message, "Data too long for column 'image'")) {
			return ['success' => false, 'error' => 'URL зображення занадто довгий для поточної структури бази даних. Оновіть таблицю products або використайте коротший URL.'];
		}

		return ['success' => false, 'error' => 'Не вдалося зберегти товар через помилку бази даних.'];
	}
}
