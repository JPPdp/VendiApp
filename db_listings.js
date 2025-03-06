// Thumbnail Preview
const thumbnailInput = document.getElementById("THUMBNAIL");
const thumbnailPreview = document.getElementById("THUMBNAIL_PREVIEW");

thumbnailInput.addEventListener("change", () => {
    const file = thumbnailInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.innerHTML = `<img src="${e.target.result}" alt="Thumbnail Preview">`;
        };
        reader.readAsDataURL(file);
    }
});

// Preview Images
const previewInput = document.getElementById("PREVIEW");
const previewImages = document.getElementById("PREVIEW_IMAGES");

previewInput.addEventListener("change", () => {
    previewImages.innerHTML = ""; // Clear existing images
    const files = previewInput.files;
    for (let i = 0; i < files.length && i < 5; i++) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImages.innerHTML += `<img src="${e.target.result}" alt="Preview Image ${i + 1}">`;
        };
        reader.readAsDataURL(files[i]);
    }
});

// Add Package
const packagesContainer = document.getElementById("PACKAGES");
const addPackageButton = document.getElementById("ADD_PACKAGE");

addPackageButton.addEventListener("click", () => {
    if (packagesContainer.children.length < 5) {
        const packageDiv = document.createElement("div");
        packageDiv.classList.add("PACKAGE");

        packageDiv.innerHTML = `
            <div class="PACKAGE_THUMBNAIL">
                <label for="PACKAGE_THUMBNAIL">Thumbnail</label>
                <input type="file" class="PACKAGE_THUMBNAIL_INPUT" accept="image/*">
                <div class="PACKAGE_THUMBNAIL_PREVIEW"></div>
            </div>
            <input type="text" class="PACKAGE_NAME" placeholder="Package Name" required>
            <input type="text" class="PACKAGE_PERSONS" placeholder="Good for [number] pax" required>
            <input type="number" class="PACKAGE_PRICE" placeholder="Price" required>
            <!--
            <textarea class="PACKAGE_DESCRIPTION" placeholder="Package Description"></textarea>
            -->
            <button type="button" class="DELETE_PACKAGE">Delete</button>
        `;

        packagesContainer.appendChild(packageDiv);

        // Add event listener to delete button
        packageDiv.querySelector(".DELETE_PACKAGE").addEventListener("click", () => {
            packageDiv.remove();
        });

        // Add event listener for thumbnail preview
        const thumbnailInput = packageDiv.querySelector(".PACKAGE_THUMBNAIL_INPUT");
        const thumbnailPreview = packageDiv.querySelector(".PACKAGE_THUMBNAIL_PREVIEW");

        thumbnailInput.addEventListener("change", () => {
            const file = thumbnailInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    thumbnailPreview.innerHTML = `<img src="${e.target.result}" alt="Package Thumbnail">`;
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        alert("Maximum of 5 packages allowed.");
    }
});