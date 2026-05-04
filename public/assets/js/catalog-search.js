(function () {
	const searchForm = document.querySelector('[data-catalog-search]');
	const searchInput = document.getElementById('catalog-search');
	const cards = Array.from(document.querySelectorAll('.catalog-card'));
	const emptyState = document.getElementById('catalog-search-empty');

	if (!searchForm || !searchInput || cards.length === 0 || !emptyState) {
		return;
	}

	const normalize = (value) => value.toLowerCase().trim();

	const filterCards = () => {
		const query = normalize(searchInput.value);
		let visibleCards = 0;

		cards.forEach((card) => {
			const text = normalize(card.textContent || '');
			const isVisible = query === '' || text.includes(query);

			card.style.display = isVisible ? '' : 'none';
			if (isVisible) {
				visibleCards += 1;
			}
		});

		emptyState.classList.toggle('catalog-page__empty--hidden', visibleCards > 0);
	};

	searchForm.addEventListener('submit', function (event) {
		event.preventDefault();
		filterCards();
	});
})();