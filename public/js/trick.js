/**
 * Retrieves the slug from the current URL.
 *
 * @return {string} The slug extracted from the URL.
 */
function getSlug()
{
    // Url current
    var url = new URL(window.location.href);

    // Split the path in segments
    var segments = url.pathname.split("/");

    // The slug in the last segment in URL
    return segments[segments.length - 1];
}

/**
 * Deletes a figure by making an HTTP DELETE request to the server.
 * Redirects to the specified URL if the server response contains a redirect parameter.
 * Logs an error message if there was a problem with the fetch operation.
 *
 * @return {void}
 */
function deleteFigure(slug)
{
    fetch("/tricks/details/" + slug + "/delete", {
        method: "DELETE",
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            console.error("There was a problem with the fetch operation:", error);
        });
}

/**
 * Methods for DELETE elements
 * Declarations EventListener for delete a figures
 * @type {HTMLElement}
 */
var boxDelete = document.getElementById("boxDelete");

document.getElementById("btnDelete").addEventListener("click", function () {
    boxDelete.classList.remove("d-none");
    boxDelete.classList.add("d-block");
});

document.getElementById("closeBoxDelete").addEventListener("click", function () {
    boxDelete.classList.remove("d-block");
    boxDelete.classList.add("d-none");
});

document.getElementById("deleteFigure").addEventListener("click", function () {
    deleteFigure(getSlug());
});

/**
 * Methods for UPDATE elements
 * Declarations EventListener for update a figures
 */
document.getElementById("editButton").addEventListener("click", function() {
    document.querySelectorAll(".read-mode, .edit-mode").forEach(function(element) {
        element.style.display = (element.style.display === "none") ? "block" : "none";
    });
});
