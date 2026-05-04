<?php
require_once __DIR__ . '/../helpers/database.php';

class Product
{
	private PDO $pdo;

	public function __construct()
	{
		$this->pdo = getDatabaseConnection();
	}

	public function findAll(): array
	{
		$stmt = $this->pdo->query(
			'SELECT
				p.id,
				p.category_id,
				c.name AS category_name,
				p.name,
				p.slug,
				p.manufacturer,
				p.image,
				p.short_description,
				p.description,
				p.price,
				p.quantity,
				p.is_available,
				p.sku,
				p.created_at,
				p.updated_at
			FROM products p
			LEFT JOIN categories c ON c.id = p.category_id
			ORDER BY p.id DESC'
		);

		return $stmt->fetchAll();
	}

	public function findById(int $id)
	{
		$stmt = $this->pdo->prepare(
			'SELECT
				p.id,
				p.category_id,
				c.name AS category_name,
				p.name,
				p.slug,
				p.manufacturer,
				p.image,
				p.short_description,
				p.description,
				p.price,
				p.quantity,
				p.is_available,
				p.sku,
				p.created_at,
				p.updated_at
			FROM products p
			LEFT JOIN categories c ON c.id = p.category_id
			WHERE p.id = :id'
		);
		$stmt->execute(['id' => $id]);

		return $stmt->fetch();
	}

	public function findBySlug(string $slug)
	{
		$stmt = $this->pdo->prepare(
			'SELECT
				p.id,
				p.category_id,
				c.name AS category_name,
				p.name,
				p.slug,
				p.manufacturer,
				p.image,
				p.short_description,
				p.description,
				p.price,
				p.quantity,
				p.is_available,
				p.sku,
				p.created_at,
				p.updated_at
			FROM products p
			LEFT JOIN categories c ON c.id = p.category_id
			WHERE p.slug = :slug'
		);
		$stmt->execute(['slug' => $slug]);

		return $stmt->fetch();
	}

	public function findByCategory(int $categoryId): array
	{
		$stmt = $this->pdo->prepare(
			'SELECT
				p.id,
				p.category_id,
				c.name AS category_name,
				p.name,
				p.slug,
				p.manufacturer,
				p.image,
				p.short_description,
				p.description,
				p.price,
				p.quantity,
				p.is_available,
				p.sku,
				p.created_at,
				p.updated_at
			FROM products p
			LEFT JOIN categories c ON c.id = p.category_id
			WHERE p.category_id = :category_id
			  AND p.is_available = 1
			  AND p.quantity > 0
			ORDER BY p.name ASC'
		);
		$stmt->execute(['category_id' => $categoryId]);

		return $stmt->fetchAll();
	}

	public function create(array $data)
	{
		$payload = $this->normalizePayload($data);

		$stmt = $this->pdo->prepare(
			'INSERT INTO products (
				category_id,
				name,
				slug,
				manufacturer,
				image,
				short_description,
				description,
				price,
				quantity,
				is_available,
				sku
			) VALUES (
				:category_id,
				:name,
				:slug,
				:manufacturer,
				:image,
				:short_description,
				:description,
				:price,
				:quantity,
				:is_available,
				:sku
			)'
		);
		$stmt->execute($payload);

		return $this->findById((int) $this->pdo->lastInsertId());
	}

	public function update(int $id, array $data)
	{
		$payload = $this->normalizePayload($data);
		$payload['id'] = $id;

		$stmt = $this->pdo->prepare(
			'UPDATE products SET
				category_id = :category_id,
				name = :name,
				slug = :slug,
				manufacturer = :manufacturer,
				image = :image,
				short_description = :short_description,
				description = :description,
				price = :price,
				quantity = :quantity,
				is_available = :is_available,
				sku = :sku
			WHERE id = :id'
		);
		$stmt->execute($payload);

		return $this->findById($id);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
		$stmt->execute(['id' => $id]);

		return $stmt->rowCount() > 0;
	}

	public function slugExists(string $slug, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM products WHERE slug = :slug';
		$params = ['slug' => $slug];

		if ($excludeId !== null) {
			$sql .= ' AND id != :exclude_id';
			$params['exclude_id'] = $excludeId;
		}

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);

		return (int) $stmt->fetchColumn() > 0;
	}

	public function findAvailable(): array
	{
		$stmt = $this->pdo->query(
			'SELECT
				p.id,
				p.category_id,
				c.name AS category_name,
				p.name,
				p.slug,
				p.manufacturer,
				p.image,
				p.short_description,
				p.description,
				p.price,
				p.quantity,
				p.is_available,
				p.sku,
				p.created_at,
				p.updated_at
			FROM products p
			LEFT JOIN categories c ON c.id = p.category_id
			WHERE p.is_available = 1 AND p.quantity > 0
			ORDER BY p.name ASC'
		);

		return $stmt->fetchAll();
	}

	private function normalizePayload(array $data): array
	{
		$name = trim((string) ($data['name'] ?? ''));
		$slug = trim((string) ($data['slug'] ?? ''));

		if ($slug === '') {
			$slug = $this->makeSlug($name);
		}

		return [
			'category_id' => isset($data['category_id']) && $data['category_id'] !== ''
				? (int) $data['category_id']
				: null,
			'name' => $name,
			'slug' => $slug,
			'manufacturer' => trim((string) ($data['manufacturer'] ?? '')),
			'image' => trim((string) ($data['image'] ?? '')),
			'short_description' => trim((string) ($data['short_description'] ?? '')),
			'description' => trim((string) ($data['description'] ?? '')),
			'price' => isset($data['price']) && $data['price'] !== ''
				? (float) $data['price']
				: 0,
			'quantity' => isset($data['quantity'])
				? max(0, (int) $data['quantity'])
				: 0,
			'is_available' => !empty($data['is_available']) ? 1 : 0,
			'sku' => trim((string) ($data['sku'] ?? '')),
		];
	}

	private function makeSlug(string $value): string
	{
		$value = strtolower(trim($value));
		$value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
		$value = trim($value, '-');

		return $value !== '' ? $value : 'product';
	}
}
