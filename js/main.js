// Lostly — site-wide JavaScript
// Replaces the inline onclick="" handlers that used to live directly in the
// PHP templates. Behaviour is unchanged: same confirm dialog text, same
// show/hide logic for the contact info box.

// Shows a validation message right after the given form, reusing the same
// CSS classes the PHP side already renders its own messages with, so no
// new CSS is needed. Any previously injected message is replaced.
function showFormMessage(form, text, className) {
    var existing = document.getElementById("js-form-message");
    if (existing) {
        existing.remove();
    }
    var p = document.createElement("p");
    p.id = "js-form-message";
    p.className = className;
    p.textContent = text;
    form.parentNode.insertBefore(p, form.nextSibling);
}

function clearFormMessage() {
    var existing = document.getElementById("js-form-message");
    if (existing) {
        existing.remove();
    }
}

// Runs validation rules in order (mirrors the matching PHP if/else-if
// chain exactly, message-for-message) and stops at the first failure.
// Returns true if every rule passed.
function runValidation(form, rules, messageClass) {
    clearFormMessage();
    for (var i = 0; i < rules.length; i++) {
        if (!rules[i].test()) {
            showFormMessage(form, rules[i].message, messageClass);
            return false;
        }
    }
    return true;
}

document.addEventListener("DOMContentLoaded", function () {

    // "Delete this report? This cannot be undone." confirmation on delete links
    document.querySelectorAll(".js-confirm-delete").forEach(function (link) {
        link.addEventListener("click", function (e) {
            var message = link.getAttribute("data-confirm-message") || "Are you sure?";
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // "Show Contact Information" button on item_detail.php
    document.querySelectorAll(".js-show-contact").forEach(function (button) {
        button.addEventListener("click", function () {
            var targetId = button.getAttribute("data-target");
            var target = document.getElementById(targetId);
            if (target) {
                target.style.display = "block";
            }
            button.style.display = "none";
        });
    });

    var page = document.body.getAttribute("data-page");

    // ---- registration.php ----------------------------------------------
    if (page === "registration") {
        var form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", function (e) {
                var username = form.elements["username"].value.trim();
                var email = form.elements["email"].value.trim();
                var phone = form.elements["phone"].value.trim();
                var division = form.elements["division"].value;
                var district = form.elements["district"].value.trim();
                var password = form.elements["password"].value;
                var confirmPassword = form.elements["confirm_password"].value;

                var ok = runValidation(form, [
                    { test: function () { return username !== ""; }, message: "Enter Username" },
                    { test: function () { return email !== ""; }, message: "Enter Email" },
                    { test: function () { return phone !== ""; }, message: "Enter Phone Number" },
                    { test: function () { return division !== ""; }, message: "Select Division" },
                    { test: function () { return district !== ""; }, message: "Enter District" },
                    { test: function () { return password !== ""; }, message: "Enter Password" },
                    { test: function () { return password.length >= 6; }, message: "Password must be at least 6 characters" },
                    { test: function () { return confirmPassword !== ""; }, message: "Confirm Your Password" },
                    { test: function () { return password === confirmPassword; }, message: "Passwords do not match" }
                ], "error-message");

                if (!ok) {
                    e.preventDefault();
                }
            });
        }
    }

    // ---- login.php --------------------------------------------------------
    if (page === "login") {
        var form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", function (e) {
                var username = form.elements["username"].value.trim();
                var password = form.elements["password"].value;

                var ok = runValidation(form, [
                    { test: function () { return username !== ""; }, message: "Enter Username" },
                    { test: function () { return password !== ""; }, message: "Enter Password" }
                ], "error-message");

                if (!ok) {
                    e.preventDefault();
                }
            });
        }
    }

    // ---- edit_profile.php ---------------------------------------------
    if (page === "edit-profile") {
        var form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", function (e) {
                var username = form.elements["username"].value.trim();
                var email = form.elements["email"].value.trim();
                var phone = form.elements["phone"].value.trim();
                var division = form.elements["division"].value;
                var district = form.elements["district"].value.trim();
                var newPassword = form.elements["new_password"].value;
                var confirmPassword = form.elements["confirm_password"].value;

                var ok = runValidation(form, [
                    { test: function () { return username !== ""; }, message: "Enter Username" },
                    { test: function () { return email !== ""; }, message: "Enter Email" },
                    { test: function () { return phone !== ""; }, message: "Enter Phone Number" },
                    { test: function () { return division !== ""; }, message: "Select a valid Division" },
                    { test: function () { return district !== ""; }, message: "Enter District" },
                    { test: function () { return !(newPassword !== "" && newPassword.length < 6); }, message: "New password must be at least 6 characters" },
                    { test: function () { return newPassword === confirmPassword; }, message: "New passwords do not match" }
                ], "profile-message error");

                if (!ok) {
                    e.preventDefault();
                }
            });
        }
    }

    // ---- report_item.php -----------------------------------------------
    if (page === "report-item") {
        var form = document.querySelector("form");
        if (form) {
            form.addEventListener("submit", function (e) {
                var itemName = form.elements["item_name"].value.trim();
                var category = form.elements["category"].value;
                var division = form.elements["division"].value;
                var district = form.elements["district"].value.trim();
                var eventDate = form.elements["event_date"].value;

                var ok = runValidation(form, [
                    { test: function () { return itemName !== ""; }, message: "Enter the item name" },
                    { test: function () { return category !== ""; }, message: "Select a valid category" },
                    { test: function () { return division !== ""; }, message: "Select a valid division" },
                    { test: function () { return district !== ""; }, message: "Enter a district" },
                    { test: function () { return eventDate !== ""; }, message: "Select a date" }
                ], "error-message");

                if (!ok) {
                    e.preventDefault();
                }
            });

            // Live photo preview when a new file is chosen, reusing the same
            // .current-photo-preview class the PHP side uses to show the
            // existing photo in edit mode.
            var fileInput = form.elements["item_image"];
            if (fileInput) {
                fileInput.addEventListener("change", function () {
                    if (!fileInput.files || !fileInput.files[0]) {
                        return;
                    }
                    var file = fileInput.files[0];
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = form.querySelector("img.current-photo-preview");
                        if (!img) {
                            img = document.createElement("img");
                            img.className = "current-photo-preview";
                            img.alt = "Selected photo";
                            fileInput.parentNode.insertBefore(img, fileInput.nextSibling);
                        }
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }

});
