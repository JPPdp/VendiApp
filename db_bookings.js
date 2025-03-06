// Filter Bookings
const filterButtons = document.querySelectorAll(".FILTER_BUTTON");
const bookingRows = document.querySelectorAll(".BOOKING_TABLE tbody tr");

filterButtons.forEach(button => {
    button.addEventListener("click", () => {
        // Remove active class from all buttons
        filterButtons.forEach(btn => btn.classList.remove("ACTIVE"));
        // Add active class to the clicked button
        button.classList.add("ACTIVE");

        const filter = button.dataset.filter;

        // Show/hide rows based on filter
        bookingRows.forEach(row => {
            const status = row.dataset.status;
            if (filter === "ALL" || status === filter) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
});

// Handle Accept/Decline Actions
const acceptButtons = document.querySelectorAll(".ACCEPT_BUTTON");
const declineButtons = document.querySelectorAll(".DECLINE_BUTTON");

acceptButtons.forEach(button => {
    button.addEventListener("click", () => {
        const row = button.closest("tr");
        row.querySelector(".STATUS").textContent = "Approved";
        row.querySelector(".STATUS").id = "APPROVED";
        button.disabled = true;
        button.nextElementSibling.disabled = true; // Disable Decline button
    });
});

declineButtons.forEach(button => {
    button.addEventListener("click", () => {
        const row = button.closest("tr");
        row.querySelector(".STATUS").textContent = "Rejected";
        row.querySelector(".STATUS").id = "REJECTED";
        button.disabled = true;
        button.previousElementSibling.disabled = true; // Disable Accept button
    });
});