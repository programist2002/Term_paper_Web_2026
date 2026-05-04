<?php
require_once __DIR__ . '/../helpers/database.php';

class User
{
	private PDO $pdo;

	public function __construct()
	{
		$this->pdo = getDatabaseConnection();
	}

	public function findByEmail(string $email)
	{
		$stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
		$stmt->execute(['email' => $email]);

		return $stmt->fetch();
	}

	public function findAll(): array
	{
		$stmt = $this->pdo->query(
			'SELECT id, name, email, role, created_at FROM users ORDER BY id ASC'
		);

		return $stmt->fetchAll();
	}

	public function create(string $name, string $email, string $hashedPassword, string $role = 'user')
	{
		$stmt = $this->pdo->prepare(
			'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
		);
		$stmt->execute([
			'name' => $name,
			'email' => $email,
			'password' => $hashedPassword,
			'role' => $role,
		]);

		$userId = (int) $this->pdo->lastInsertId();

		return $this->findById($userId);
	}

	public function findById(int $id)
	{
		$stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
		$stmt->execute(['id' => $id]);

		return $stmt->fetch();
	}

	public function emailExists(string $email, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
		$params = ['email' => $email];

		if ($excludeId !== null) {
			$sql .= ' AND id != :exclude_id';
			$params['exclude_id'] = $excludeId;
		}

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);

		return (int) $stmt->fetchColumn() > 0;
	}

	public function update(int $id, string $name, string $email, string $role, ?string $hashedPassword = null)
	{
		$sql = 'UPDATE users SET name = :name, email = :email, role = :role';
		$params = [
			'id' => $id,
			'name' => $name,
			'email' => $email,
			'role' => $role,
		];

		if ($hashedPassword !== null) {
			$sql .= ', password = :password';
			$params['password'] = $hashedPassword;
		}

		$sql .= ' WHERE id = :id';

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);

		return $this->findById($id);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
		$stmt->execute(['id' => $id]);

		return $stmt->rowCount() > 0;
	}
}
