var boxDelete = document.getElementById("boxDelete");

document.getElementById("btnDelete").addEventListener("click", function () {
    boxDelete.classList.remove("d-none");
    boxDelete.classList.add("d-block");
});

document.getElementById("closeBoxDelete").addEventListener("click", function () {
    boxDelete.classList.remove("d-block");
    boxDelete.classList.add("d-none");
});

document.getElementById("deleteFigure").addEventListener("click", function (){
    var slug = getSlug();
    deleteFigure(slug);
});

/**
 * Deletes a figure by making an HTTP DELETE request to the server.
 * Redirects to the specified URL if the server response contains a redirect parameter.
 * Logs an error message if there was a problem with the fetch operation.
 *
 * @return {void}
 */
 function deleteFigure(slug)
{
    fetch('/tricks/details/' + slug + '/delete', {
        method: 'DELETE',
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
            console.error('There was a problem with the fetch operation:', error);
        });
}

/**
 * Retrieves the slug from the current URL.
 *
 * @return {string} The slug extracted from the URL.
 */
function getSlug()
{
    // Obtenir l'URL courante
    var url = new URL(window.location.href);

    // Diviser le chemin en segments
    var segments = url.pathname.split('/');

    // Le slug est le dernier segment de l'URL
    return segments[segments.length - 1];
}
