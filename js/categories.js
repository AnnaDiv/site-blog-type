function display_values() {
    const catsList = document.getElementById("cats");
    const categoriesInput = document.getElementById("categories");
    const inputField = document.getElementById("category");

    // Clear the <ul>
    catsList.innerHTML = '';

    categories_values.forEach((cat, index) => {
        const li = document.createElement("li");
        li.textContent = cat;

        li.addEventListener("click", function () {
            categories_values.splice(index, 1);
            display_values(); // re-render
        });

        catsList.appendChild(li);
    });

    // Store the array as JSON for server-side parsing
    categoriesInput.value = JSON.stringify(categories_values);
    inputField.value = '';
}

function add_category_from_input() {
    const inputField = document.getElementById("category");
    const val = inputField.value.trim();

    if (val && !categories_values.includes(val)) {
        categories_values.push(val);
        display_values();
    }
}

// Allow click on Enter button
function transport_value(e) {
    e.preventDefault(); // Prevent form submission
    add_category_from_input();
}

// Allow pressing "Enter" key inside input
document.getElementById("category").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        e.preventDefault(); // prevent form submit
        add_category_from_input();
    }
});
