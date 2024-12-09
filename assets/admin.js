import "./styles/admin.css";

window.addEventListener("load", () => {
	const DIRECTION_UP = "up";
	const DIRECTION_DOWN = "down";

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
				const siblings = [...container.querySelectorAll("[data-sortable]")];
				for (const [index, el] of siblings.entries()) {
					const position = el.querySelector(".route-point-position input");
					if (position) {
						position.value = index + 1;
					}
				}
			}
		}
	};

	for (const el of [
		...document.querySelectorAll(".field-collection-item[data-sortable]"),
	]) {
		const [up, down] = [
			el.querySelector(".field-collection-move-up-button"),
			el.querySelector(".field-collection-move-down-button"),
		];
		up.addEventListener("click", (event) => {
			moveElement(event.target.closest("[data-sortable]"), DIRECTION_UP);
		});
		down.addEventListener("click", (event) => {
			moveElement(event.target.closest("[data-sortable]"), DIRECTION_DOWN);
		});
	}
});
