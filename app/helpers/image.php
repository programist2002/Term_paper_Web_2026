<?php

function resolveImageSource(?string $path): string
{
	$value = trim((string) $path);

	if ($value === '') {
		return '';
	}

	if (preg_match('#^(https?:)?//#i', $value) === 1) {
		return str_starts_with($value, '//') ? 'https:' . $value : $value;
	}

	if (str_starts_with($value, '/')) {
		return $value;
	}

	if (preg_match('/^[a-z0-9.-]+\.[a-z]{2,}(?:\/.*)?$/i', $value) === 1) {
		return 'https://' . $value;
	}

	return $value;
}