<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
	private User $userModel;

	public function __construct()
	{
		$this->userModel = new User();
	}

	public function getUsers(): array
	{
		return $this->userModel->findAll();
	}

	public function getUserById(int $id)
	{
		return $this->userModel->findById($id);
	}

	public function createUser(array $data): array
	{
		$name = trim($data['name'] ?? '');
		$email = trim($data['email'] ?? '');
		$password = (string) ($data['password'] ?? '');
		$role = $this->normalizeRole($data['role'] ?? 'user');

		if ($name === '' || $email === '' || $password === '') {
			return ['success' => false, 'error' => 'Поля ім\'я, електронна пошта та пароль є обов\'язковими.'];
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['success' => false, 'error' => 'Невірний формат електронної пошти.'];
		}

		if ($this->userModel->emailExists($email)) {
			return ['success' => false, 'error' => 'Користувач з такою електронною поштою вже існує.'];
		}

		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		$user = $this->userModel->create($name, $email, $hashedPassword, $role);

		return ['success' => true, 'user' => $user, 'message' => 'Користувача успішно додано.'];
	}

	public function updateUser(int $id, array $data): array
	{
		$existingUser = $this->userModel->findById($id);

		if (!$existingUser) {
			return ['success' => false, 'error' => 'Користувача не знайдено.'];
		}

		$name = trim($data['name'] ?? (string) $existingUser['name']);
		$email = trim($data['email'] ?? (string) $existingUser['email']);
		$password = (string) ($data['password'] ?? '');
		$role = $this->normalizeRole($data['role'] ?? (string) $existingUser['role']);

		if ($name === '' || $email === '') {
			return ['success' => false, 'error' => 'Поля ім\'я та електронна пошта є обов\'язковими.'];
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['success' => false, 'error' => 'Невірний формат електронної пошти.'];
		}

		if ($this->userModel->emailExists($email, $id)) {
			return ['success' => false, 'error' => 'Користувач з такою електронною поштою вже існує.'];
		}

		$hashedPassword = $password !== ''
			? password_hash($password, PASSWORD_DEFAULT)
			: null;

		$user = $this->userModel->update($id, $name, $email, $role, $hashedPassword);

		return ['success' => true, 'user' => $user, 'message' => 'Дані користувача успішно оновлено.'];
	}

	public function deleteUser(int $id): array
	{
		$existingUser = $this->userModel->findById($id);

		if (!$existingUser) {
			return ['success' => false, 'error' => 'Користувача не знайдено.'];
		}

		$deleted = $this->userModel->delete($id);

        if (!$deleted) {
            return ['success' => false, 'error' => 'Не вдалося видалити користувача.'];
        } 

        return ['success' => true, 'message' => 'Користувача успішно видалено.'];
	}

	private function normalizeRole(string $role): string
	{
		$role = strtolower(trim($role));

		return in_array($role, ['user', 'admin'], true) ? $role : 'user';
	}
}