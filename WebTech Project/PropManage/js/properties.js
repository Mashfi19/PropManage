document.addEventListener("DOMContentLoaded", function () {

    const searchSection =
        document.querySelector(".search-section");

    if (searchSection) {

        window.addEventListener("scroll", function () {

            if (
                searchSection.getBoundingClientRect().top
                <= 79
            ) {

                searchSection.classList.add(
                    "is-stuck"
                );

            } else {

                searchSection.classList.remove(
                    "is-stuck"
                );

            }

        });

    }

    const searchButton =
        document.getElementById("searchBtn");

    if (searchButton) {

        searchButton.addEventListener(
            "click",
            function () {

                const locationInput =
                    document.getElementById("location");

                const propertyTypeInput =
                    document.getElementById(
                        "propertyType"
                    );

                const budgetInput =
                    document.getElementById("budget");

                const location =
                    locationInput
                        ? locationInput.value
                            .trim()
                            .toLowerCase()
                        : "";

                const propertyType =
                    propertyTypeInput
                        ? propertyTypeInput.value
                        : "";

                const budget =
                    budgetInput
                        ? budgetInput.value
                        : "";

                const cards =
                    document.querySelectorAll(
                        ".property-card"
                    );

                let visibleCount = 0;

                cards.forEach(function (card) {

                    const cardLocation =
                        card.dataset.location
                            .toLowerCase();

                    const cardCategory =
                        card.dataset.category;

                    const cardPrice =
                        Number(
                            card.dataset.price
                        );

                    let matchesLocation = true;

                    let matchesType = true;

                    let matchesBudget = true;

                    if (location !== "") {

                        matchesLocation =
                            cardLocation.includes(
                                location
                            );

                    }

                    if (propertyType !== "") {

                        if (
                            propertyType ===
                            "Apartment"
                        ) {

                            matchesType =
                                cardCategory ===
                                "apartments";

                        }

                        else if (
                            propertyType ===
                            "House"
                        ) {

                            matchesType =
                                cardCategory ===
                                "houses";

                        }

                        else if (
                            propertyType ===
                            "Room"
                        ) {

                            matchesType =
                                cardCategory ===
                                "rooms";

                        }

                        else if (
                            propertyType ===
                            "Commercial"
                        ) {

                            matchesType =
                                cardCategory ===
                                "commercial";

                        }

                    }

                    if (budget !== "") {

                        if (budget === "50000") {

                            matchesBudget =
                                cardPrice >= 50000;

                        }

                        else {

                            matchesBudget =
                                cardPrice <=
                                Number(budget);

                        }

                    }

                    if (
                        matchesLocation &&
                        matchesType &&
                        matchesBudget
                    ) {

                        card.style.display =
                            "flex";

                        visibleCount++;

                    }

                    else {

                        card.style.display =
                            "none";

                    }

                });

                updateResultMessage(
                    visibleCount
                );

            }
        );

    }

    const pillButtons =
        document.querySelectorAll(
            ".filter-pills .pill"
        );

    pillButtons.forEach(function (pill) {

        pill.addEventListener(
            "click",
            function () {

                pillButtons.forEach(
                    function (button) {

                        button.classList.remove(
                            "active"
                        );

                    }
                );

                this.classList.add(
                    "active"
                );

                const selectedCategory =
                    this.getAttribute(
                        "data-category"
                    );

                const cards =
                    document.querySelectorAll(
                        ".property-card"
                    );

                let visibleCount = 0;

                cards.forEach(function (card) {

                    if (
                        selectedCategory ===
                        "all"
                    ) {

                        card.style.display =
                            "flex";

                        visibleCount++;

                    }

                    else if (
                        card.dataset.category &&
                        card.dataset.category.toLowerCase().includes(selectedCategory.toLowerCase())
                    ) {

                        card.style.display =
                            "flex";

                        visibleCount++;

                    }

                    else {

                        card.style.display =
                            "none";

                    }

                });

                updateResultMessage(
                    visibleCount
                );

                console.log(
                    "Selected Category:",
                    selectedCategory
                );

            }
        );

    });

    const sortSelect =
        document.getElementById("sortBy");

    if (sortSelect) {

        sortSelect.addEventListener(
            "change",
            function () {

                const propertyGrid =
                    document.getElementById(
                        "propertyGrid"
                    );

                const cards =
                    Array.from(
                        propertyGrid.querySelectorAll(
                            ".property-card"
                        )
                    );

                const selectedSort =
                    this.value;

                if (
                    selectedSort ===
                    "price-low"
                ) {

                    cards.sort(function (a, b) {

                        return (
                            Number(
                                a.dataset.price
                            )
                            -
                            Number(
                                b.dataset.price
                            )
                        );

                    });

                }

                else if (
                    selectedSort ===
                    "price-high"
                ) {

                    cards.sort(function (a, b) {

                        return (
                            Number(
                                b.dataset.price
                            )
                            -
                            Number(
                                a.dataset.price
                            )
                        );

                    });

                }

                else if (
                    selectedSort ===
                    "newest"
                ) {

                    cards.reverse();

                }

                cards.forEach(function (card) {

                    propertyGrid.appendChild(
                        card
                    );

                });

            }
        );

    }

    const cards =
        document.querySelectorAll(
            ".property-card"
        );

    updateResultMessage(
        cards.length
    );

});

function updateResultMessage(count) {

    const noResults =
        document.getElementById(
            "noResults"
        );

    if (!noResults) {
        return;
    }

    if (count === 0) {

        noResults.style.display =
            "block";

    }

    else {

        noResults.style.display =
            "none";

    }

}