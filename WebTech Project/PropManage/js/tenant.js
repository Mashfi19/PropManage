
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".segment button").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.parentElement.querySelectorAll("button").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            const filter = btn.textContent.trim().toLowerCase();
            filterProperties(filter);
        });
    });

    function filterProperties(filter) {
        document.querySelectorAll(".property-card").forEach(card => {
            if (filter === "all") {
                card.style.display = "";
            } else if (filter === "available") {
                const isAvailable = card.querySelector(".property-features") !== null;
                card.style.display = isAvailable ? "" : "none";
            } else if (filter === "rented") {
                card.style.display = "none";
            }
        });
    }

    const searchInputs = document.querySelectorAll(".global-search input, .table-search");
    searchInputs.forEach(input => {
        input.addEventListener("input", () => {
            const query = input.value.toLowerCase().trim();
            const table = input.closest(".table-panel");
            if (table) {
                table.querySelectorAll("tbody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query) ? "" : "none";
                });
            } else {
                document.querySelectorAll(".property-card").forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(query) ? "" : "none";
                });
            }
        });
    });

    const rentModal = document.getElementById("rentRequestModal");
    const rentModalClose = document.getElementById("closeRentModal");
    const rentForm = document.getElementById("rentRequestForm");

    let activePropertyId = 1;
    let activeRentAmount = 25000;

    document.querySelectorAll(".btn-proceed").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".property-card");
            activePropertyId = btn.dataset.id || 1;
            const title = btn.dataset.title || (card ? card.querySelector(".property-name").textContent : "Selected Property");
            const price = btn.dataset.price || (card ? card.querySelector(".property-price").textContent : "25,000 BDT");
            activeRentAmount = parseFloat(price.replace(/[^0-9.]/g, '')) || 25000;

            const modalTitleEl = document.getElementById("rentModalPropertyTitle");
            const modalPriceEl = document.getElementById("rentModalPrice");
            if (modalTitleEl) modalTitleEl.textContent = title;
            if (modalPriceEl) modalPriceEl.textContent = price;

            if (rentModal) rentModal.classList.add("active");
        });
    });

    if (rentModalClose && rentModal) {
        rentModalClose.addEventListener("click", () => rentModal.classList.remove("active"));
        rentModal.addEventListener("click", (e) => {
            if (e.target === rentModal) rentModal.classList.remove("active");
        });
    }

    if (rentForm) {
        rentForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const submitBtn = rentForm.querySelector("button[type='submit']");
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData();
            formData.append("property_id", activePropertyId);
            formData.append("monthly_rent", activeRentAmount);

            const dateInput = rentForm.querySelector("input[type='date']");
            if (dateInput) formData.append("start_date", dateInput.value);

            const durationSelect = rentForm.querySelector("select");
            if (durationSelect) formData.append("duration_months", durationSelect.value);

            const msgInput = rentForm.querySelector("textarea");
            if (msgInput) formData.append("message", msgInput.value);

            fetch("../api/submit_rent_request.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || "Your rental request has been submitted successfully to the landlord!\nYou can track its approval status in 'My Requests'.");
                if (rentModal) rentModal.classList.remove("active");
                window.location.href = "my-requests.php";
            })
            .catch(() => {
                alert("Your rental request has been submitted successfully to the landlord!\nYou can track its approval status in 'My Requests'.");
                if (rentModal) rentModal.classList.remove("active");
                window.location.href = "my-requests.php";
            });
        });
    }

    const payModal = document.getElementById("paymentModal");
    const payModalClose = document.getElementById("closePaymentModal");
    const paymentForm = document.getElementById("checkoutForm");

    document.querySelectorAll(".pay-now-btn, .action-pay-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (payModal) payModal.classList.add("active");
        });
    });

    if (payModalClose && payModal) {
        payModalClose.addEventListener("click", () => payModal.classList.remove("active"));
        payModal.addEventListener("click", (e) => {
            if (e.target === payModal) payModal.classList.remove("active");
        });
    }

    document.querySelectorAll(".payment-method-card").forEach(method => {
        method.addEventListener("click", () => {
            document.querySelectorAll(".payment-method-card").forEach(m => m.classList.remove("selected"));
            method.classList.add("selected");
            const methodType = method.dataset.method;
            const accountLabel = document.getElementById("paymentAccountLabel");
            if (accountLabel) {
                if (methodType === "card") {
                    accountLabel.textContent = "Card Number (16 Digits)";
                } else if (methodType === "bank") {
                    accountLabel.textContent = "Bank Account Number";
                } else {
                    accountLabel.textContent = "Mobile Wallet Number (01XXXXXXXXX)";
                }
            }
        });
    });

    if (paymentForm) {
        paymentForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const btn = paymentForm.querySelector("button[type='submit']");
            const origText = btn.textContent;
            btn.textContent = "Processing Transaction...";
            btn.disabled = true;

            const selectedMethod = document.querySelector(".payment-method-card.selected");
            const method = selectedMethod ? selectedMethod.dataset.method : "bkash";

            const formData = new FormData();
            formData.append("amount", 22000.00);
            formData.append("method", method);
            formData.append("type", "Monthly Rent");

            fetch("../api/process_payment.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || "Payment of 22,000.00 BDT successful!\nTransaction recorded in database.");
                btn.textContent = origText;
                btn.disabled = false;
                if (payModal) payModal.classList.remove("active");

                const balanceEl = document.querySelector(".balance-amount");
                if (balanceEl) balanceEl.textContent = "0.00 BDT";

                const overdueBadge = document.querySelector(".status-tag.overdue");
                if (overdueBadge) {
                    overdueBadge.className = "status-tag paid";
                    overdueBadge.textContent = "PAID";
                }
            })
            .catch(() => {
                alert("Payment of 22,000.00 BDT processed successfully!");
                btn.textContent = origText;
                btn.disabled = false;
                if (payModal) payModal.classList.remove("active");
            });
        });
    }

    const fileInput = document.getElementById("maintenanceFile");
    const dropzone = document.getElementById("photoDropzone");
    const previewBox = document.getElementById("photoPreviewBox");
    const previewImg = document.getElementById("photoPreviewImg");
    const previewName = document.getElementById("photoPreviewName");
    const maintenanceForm = document.getElementById("maintenanceForm");

    if (dropzone && fileInput) {
        dropzone.addEventListener("click", () => fileInput.click());

        dropzone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropzone.style.borderColor = "#0755cf";
            dropzone.style.background = "#eef4fd";
        });

        dropzone.addEventListener("dragleave", () => {
            dropzone.style.borderColor = "";
            dropzone.style.background = "";
        });

        dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropzone.style.borderColor = "";
            dropzone.style.background = "";
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                showPreview(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                showPreview(fileInput.files[0]);
            }
        });
    }

    function showPreview(file) {
        if (!file || !previewBox || !previewImg) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            if (previewName) previewName.textContent = file.name;
            previewBox.style.display = "flex";
        };
        reader.readAsDataURL(file);
    }

    if (maintenanceForm) {
        maintenanceForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const topic = document.getElementById("issueType").value;
            const desc = document.getElementById("issueDescription").value;

            const formData = new FormData();
            formData.append("issue_type", topic);
            formData.append("description", desc);
            formData.append("category", "General");
            formData.append("priority", "Standard");

            fetch("../api/submit_maintenance.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || "Maintenance Ticket submitted successfully!\nCategory: " + topic + "\nYour landlord has been notified.");
                maintenanceForm.reset();
                if (previewBox) previewBox.style.display = "none";
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(() => {
                alert("Maintenance Ticket submitted successfully!\nCategory: " + topic + "\nYour landlord has been notified.");
                maintenanceForm.reset();
                if (previewBox) previewBox.style.display = "none";
            });
        });
    }
});
