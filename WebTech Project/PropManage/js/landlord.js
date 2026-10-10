
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".segment button").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.parentElement.querySelectorAll("button").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
        });
    });

    document.querySelectorAll(".update-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".maintenance-card");
            const badge = card.querySelector(".badge");
            const choice = prompt("Enter status: Pending, In Progress, or Resolved");
            if (!choice) return;
            const value = choice.trim().toLowerCase();
            badge.className = "badge";
            if (value === "pending") { badge.classList.add("pending"); badge.textContent = "◉ Pending"; }
            else if (value === "in progress") { badge.classList.add("progress"); badge.textContent = "🔧 In Progress"; }
            else if (value === "resolved") { badge.classList.add("resolved"); badge.textContent = "✓ Resolved"; }
            else { alert("Please enter Pending, In Progress, or Resolved."); }
        });
    });

    document.querySelectorAll("[data-alert]").forEach(btn => {
        btn.addEventListener("click", () => alert(btn.dataset.alert));
    });

    document.querySelectorAll(".global-search input, .table-search").forEach(input => {
        input.addEventListener("input", () => {
            const term = input.value.toLowerCase().trim();
            const table = input.closest(".table-panel");
            if (!table) return;
            table.querySelectorAll("tbody tr").forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? "" : "none";
            });
        });
    });
});
