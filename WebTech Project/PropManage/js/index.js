document.addEventListener("DOMContentLoaded", function () {

    const searchSection = document.querySelector(".search-section");

    if (searchSection) {
        window.addEventListener("scroll", function () {

            if (searchSection.getBoundingClientRect().top <= 79) {
                searchSection.classList.add("is-stuck");
            } else {
                searchSection.classList.remove("is-stuck");
            }
        });
    }

    const searchButton = document.getElementById("searchBtn");

    if (searchButton) {
        searchButton.addEventListener("click", function () {
            const locationInput = document.getElementById("location");
            const propertyTypeInput = document.getElementById("propertyType");
            const budgetInput = document.getElementById("budget");

            const location = locationInput ? locationInput.value.trim() : "";
            const propertyType = propertyTypeInput ? propertyTypeInput.value : "";
            const budget = budgetInput ? budgetInput.value : "";

            if (location === "" && propertyType === "" && budget === "") {
                alert("Please select at least one search option.");
                return;
            }

            console.log("Search information:");
            console.log("Location:", location);
            console.log("Property Type:", propertyType);
            console.log("Budget:", budget);
        });
    }
});

const pillButtons = document.querySelectorAll(".filter-pills .pill");

pillButtons.forEach((pill) => {
    pill.addEventListener("click", function () {
        pillButtons.forEach((btn) => btn.classList.remove("active"));
        this.classList.add("active");

        const selectedCategory = this.getAttribute("data-category");
        console.log("Selected Category:", selectedCategory);
    });
});