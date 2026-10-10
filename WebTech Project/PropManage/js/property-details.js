document.addEventListener("DOMContentLoaded", function () {

    const mainImage =
        document.getElementById(
            "mainPropertyImage"
        );

    const thumbnails =
        document.querySelectorAll(
            ".thumbnail"
        );

    thumbnails.forEach(function (thumbnail) {

        thumbnail.addEventListener(
            "click",
            function () {

                const image =
                    this.getAttribute(
                        "data-image"
                    );

                if (mainImage && image) {

                    mainImage.src = image;

                }

                thumbnails.forEach(
                    function (item) {

                        item.classList.remove(
                            "active"
                        );

                    }
                );

                this.classList.add(
                    "active"
                );

            }
        );

    });

    const contactBtn =
        document.getElementById(
            "contactBtn"
        );

    if (contactBtn) {

        contactBtn.addEventListener(
            "click",
            function () {

                alert(
                    "Please sign in to contact the landlord."
                );

            }
        );

    }

    const rentBtn = document.getElementById("rentBtn");

    if (rentBtn) {
        rentBtn.addEventListener("click", function () {
            if (confirm("To submit a formal rental request with move-in dates and lease term, sign in to your Tenant Portal. Proceed to Tenant Portal now?")) {
                window.location.href = "../tenant/explore.php";
            }
        });
    }

    const messageBtn = document.getElementById("messageBtn");

    if (messageBtn) {
        messageBtn.addEventListener("click", function () {
            if (confirm("Open messaging to chat directly with the property owner?")) {
                window.location.href = "../tenant/messages.php";
            }
        });
    }

});