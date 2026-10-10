
document.addEventListener("DOMContentLoaded", () => {

    const rolePills = document.querySelectorAll(".role-filter-pill");
    const userRows = document.querySelectorAll(".user-table-row");

    rolePills.forEach(pill => {
        pill.addEventListener("click", () => {
            rolePills.forEach(p => p.classList.remove("active"));
            pill.classList.add("active");

            const selectedRole = pill.dataset.role.toLowerCase();
            userRows.forEach(row => {
                const userRole = row.dataset.role.toLowerCase();
                if (selectedRole === "all" || userRole === selectedRole) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });

    document.querySelectorAll(".user-action-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const row = btn.closest("tr");
            const userName = row.querySelector(".person-name").textContent.trim();
            const statusEl = row.querySelector(".user-status-dot");
            const action = btn.dataset.action;

            if (action === "verify") {
                statusEl.className = "user-status-dot active";
                statusEl.textContent = "Verified";
                btn.dataset.action = "suspend";
                btn.textContent = "Suspend";
                btn.style.color = "#d02020";
                alert(`Account for ${userName} has been officially verified!`);
            } else if (action === "suspend") {
                if (confirm(`Are you sure you want to suspend user ${userName}?`)) {
                    statusEl.className = "user-status-dot suspended";
                    statusEl.textContent = "Suspended";
                    btn.dataset.action = "activate";
                    btn.textContent = "Activate";
                    btn.style.color = "#00a66f";
                }
            } else if (action === "activate") {
                statusEl.className = "user-status-dot active";
                statusEl.textContent = "Active";
                btn.dataset.action = "suspend";
                btn.textContent = "Suspend";
                btn.style.color = "#d02020";
                alert(`Account for ${userName} has been reactivated.`);
            }
        });
    });

    const auditModal = document.getElementById("auditListingModal");
    const closeAuditModal = document.getElementById("closeAuditModal");

    document.querySelectorAll(".btn-audit").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".property-card");
            const propName = card ? card.querySelector(".property-name").textContent : "Property Listing";
            const propLocation = card ? card.querySelector(".property-location").textContent : "Dhaka";

            const titleEl = document.getElementById("auditPropertyTitle");
            const locEl = document.getElementById("auditPropertyLocation");
            if (titleEl) titleEl.textContent = propName;
            if (locEl) locEl.textContent = propLocation;

            if (auditModal) auditModal.classList.add("active");
        });
    });

    if (closeAuditModal && auditModal) {
        closeAuditModal.addEventListener("click", () => auditModal.classList.remove("active"));
        auditModal.addEventListener("click", (e) => {
            if (e.target === auditModal) auditModal.classList.remove("active");
        });
    }

    const approveAuditBtn = document.getElementById("btnApproveAudit");
    const flagAuditBtn = document.getElementById("btnFlagAudit");

    if (approveAuditBtn) {
        approveAuditBtn.addEventListener("click", () => {
            alert("Property Listing verified and approved for public discovery!");
            if (auditModal) auditModal.classList.remove("active");
        });
    }

    if (flagAuditBtn) {
        flagAuditBtn.addEventListener("click", () => {
            const reason = prompt("Enter reason for flagging (e.g. Incomplete title deed, price discrepancy):");
            if (reason) {
                alert(`Listing flagged for review with note: "${reason}". Landlord notified.`);
                if (auditModal) auditModal.classList.remove("active");
            }
        });
    }

    const payoutModal = document.getElementById("processPayoutModal");
    const closePayoutModal = document.getElementById("closePayoutModal");
    const payoutForm = document.getElementById("payoutForm");

    document.querySelectorAll(".open-payout-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (payoutModal) payoutModal.classList.add("active");
        });
    });

    if (closePayoutModal && payoutModal) {
        closePayoutModal.addEventListener("click", () => payoutModal.classList.remove("active"));
        payoutModal.addEventListener("click", (e) => {
            if (e.target === payoutModal) payoutModal.classList.remove("active");
        });
    }

    if (payoutForm) {
        payoutForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const amount = document.getElementById("payoutAmount").value;
            const recipient = document.getElementById("payoutRecipient").value;
            alert(`Payout of ${amount} BDT disbursed successfully to ${recipient}!\nAudit entry recorded.`);
            payoutForm.reset();
            if (payoutModal) payoutModal.classList.remove("active");
        });
    }

    document.querySelectorAll(".table-search, .global-search input").forEach(input => {
        input.addEventListener("input", () => {
            const query = input.value.toLowerCase().trim();
            const table = input.closest(".table-panel");
            if (table) {
                table.querySelectorAll("tbody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query) ? "" : "none";
                });
            }
        });
    });
});
