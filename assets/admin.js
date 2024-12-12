import "./styles/admin.css";

window.addEventListener("load", () => {
	const DIRECTION_UP = "up";
	const DIRECTION_DOWN = "down";
	const SORTABLE_ITEM_SELECTOR = "[data-sortable]";

	/**
	 * Move an element up or down by swapping it with a sibling.
	 */
	const moveElement = (el, direction) => {
		if (el) {
			const sibling =
				DIRECTION_UP === direction
					? el.previousElementSibling
					: el.nextElementSibling;
			if (sibling) {
				const container = sibling.parentNode;
				if (DIRECTION_UP === direction) {
					el.after(container.removeChild(sibling));
				} else {
					el.before(container.removeChild(sibling));
				}
				// route-point-position
				const siblings = [
					...container.querySelectorAll(SORTABLE_ITEM_SELECTOR),
				];
				for (const [index, el] of siblings.entries()) {
					const position = el.querySelector(".route-point-position input");
					if (position) {
						position.value = index + 1;
					}
				}
			}
		}
	};

	/*
	 * Find collection items marked as sortable (<div … data-sortable>) and wire
	 * up "Move up" and "Move down" elements inside to actually move the element.
	 *
	 * See the _Route_points_entry_row block in ../templates/admin/form.html.twig
	 * for details on the markup.
	 */
	for (const el of [
		...document.querySelectorAll(
			`.field-collection-item${SORTABLE_ITEM_SELECTOR}`,
		),
	]) {
		const [up, down] = [
			el.querySelector(".field-collection-move-up"),
			el.querySelector(".field-collection-move-down"),
		];
		if (up) {
			up.addEventListener("click", (event) => {
				moveElement(event.target.closest(SORTABLE_ITEM_SELECTOR), DIRECTION_UP);
			});
		}
		if (down) {
			down.addEventListener("click", (event) => {
				moveElement(
					event.target.closest(SORTABLE_ITEM_SELECTOR),
					DIRECTION_DOWN,
				);
			});
		}
	}

	// Hack/workaround to make file element required (cf. https://github.com/EasyCorp/EasyAdminBundle/issues/3424). See also PointOfInterestCrudController::configureFields().
	document.addEventListener("ea.collection.item-added", (event) => {
		const el = event.detail.newElement ?? null;
		if (el) {
			const file = el.querySelector('[type="file"]');
			if (file) {
				const group = file?.closest(".form-group");
				if (group) {
					const label = group?.querySelector("legend");
					label.classList.add("required");
					file.required = true;
				}
			}
		}
	});
});
