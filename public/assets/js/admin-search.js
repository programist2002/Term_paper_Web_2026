(function () {
	const searchBlocks = Array.from(document.querySelectorAll('[data-admin-search]'));

	if (searchBlocks.length === 0) {
		return;
	}

	const normalize = (value) => value.toLowerCase().trim();

	searchBlocks.forEach((block) => {
		const form = block.querySelector('[data-admin-search-form]');
		const input = block.querySelector('[data-admin-search-input]');
		const emptyState = block.querySelector('[data-admin-search-empty]');
		const section = block.closest('section');
		const items = section ? Array.from(section.querySelectorAll('[data-admin-search-item]')) : [];

		if (!form || !input || !emptyState || items.length === 0) {
			return;
		}

		const filterItems = () => {
			const query = normalize(input.value);
			let visibleItems = 0;

			items.forEach((item) => {
				const text = normalize(item.textContent || '');
				const isVisible = query === '' || text.includes(query);

				item.style.display = isVisible ? '' : 'none';
				if (isVisible) {
					visibleItems += 1;
				}
			});

			emptyState.classList.toggle('admin-page-search__empty--hidden', visibleItems > 0);
		};

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			filterItems();
		});
	});
})();
