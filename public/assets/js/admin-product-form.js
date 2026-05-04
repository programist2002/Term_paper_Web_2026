(function () {
	const imageInput = document.getElementById('product-image');
	const counter = document.getElementById('product-image-counter');
	const error = document.getElementById('product-image-error');

	if (!imageInput || !counter || !error) {
		return;
	}

	const maxLength = Number(imageInput.dataset.maxLength || '0');

	const updateImageLengthState = () => {
		const currentLength = imageInput.value.length;
		counter.textContent = currentLength + ' / ' + maxLength + ' символів';
		counter.classList.toggle('admin-product-form__counter--limit', currentLength >= maxLength);

		if (currentLength > maxLength) {
			error.textContent = 'Кількість символів URL адреси повинна бути не більше, ніж ' + maxLength + '.';
			imageInput.setCustomValidity(error.textContent);
		} else {
			error.textContent = '';
			imageInput.setCustomValidity('');
		}
	};

	imageInput.addEventListener('input', updateImageLengthState);
	updateImageLengthState();
})();
