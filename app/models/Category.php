<?php
require_once __DIR__ . '/../helpers/database.php';

class Category
{
	private PDO $pdo;

	public function __construct()
	{
		$this->pdo = getDatabaseConnection();
	}

	public function findAll(): array
	{
		$stmt = $this->pdo->query(
			'SELECT id, name, slug, description, created_at FROM categories ORDER BY id ASC'
		);

		return $stmt->fetchAll();
	}

	public function findById(int $id)
	{
		$stmt = $this->pdo->prepare(
			'SELECT id, name, slug, description, created_at FROM categories WHERE id = :id'
		);
		$stmt->execute(['id' => $id]);

		return $stmt->fetch();
	}

	public function findBySlug(string $slug)
	{
		$stmt = $this->pdo->prepare(
			'SELECT id, name, slug, description, created_at FROM categories WHERE slug = :slug'
		);
		$stmt->execute(['slug' => $slug]);

		return $stmt->fetch();
	}

	public function create(array $data)
	{
		$payload = $this->normalizePayload($data);

		$stmt = $this->pdo->prepare(
			'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'
		);
		$stmt->execute($payload);

		return $this->findById((int) $this->pdo->lastInsertId());
	}

	public function update(int $id, array $data)
	{
		$payload = $this->normalizePayload($data);
		$payload['id'] = $id;

		$stmt = $this->pdo->prepare(
			'UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id'
		);
		$stmt->execute($payload);

		return $this->findById($id);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
		$stmt->execute(['id' => $id]);

		return $stmt->rowCount() > 0;
	}

	public function slugExists(string $slug, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM categories WHERE slug = :slug';
		$params = ['slug' => $slug];

		if ($excludeId !== null) {
			$sql .= ' AND id != :exclude_id';
			$params['exclude_id'] = $excludeId;
		}

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);

		return (int) $stmt->fetchColumn() > 0;
	}

	private function normalizePayload(array $data): array
	{
		$name = trim((string) ($data['name'] ?? ''));
		$slug = trim((string) ($data['slug'] ?? ''));

		if ($slug === '') {
			$slug = $this->makeSlug($name);
		}

		return [
			'name' => $name,
			'slug' => $slug,
			'description' => trim((string) ($data['description'] ?? '')),
		];
	}

	private function makeSlug(string $value): string
	{
		$value = strtolower(trim($value));
		$value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
		$value = trim($value, '-');

		return $value !== '' ? $value : 'category';
	}
}