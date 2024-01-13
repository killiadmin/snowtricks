/**
 * Deletes a media by sending an HTTP DELETE request to the server.
 *
 * @param {Event} event - The event object that triggered the delete operation.
 */
function deleteMedia(event) {
    event.preventDefault();

    let button = event.currentTarget;
    let mediaId = button.getAttribute("data-media-id");
    let mediaCard = button.parentElement;

    fetch("/media/" + mediaId + "/delete", {
        method: "DELETE",
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    }).then(data => {
        // remove media card from the DOM
        mediaCard.remove();
    }).catch(error => {
        console.error("There was a problem with the fetch operation:", error);
    });
}

document.querySelectorAll(".btnImageDelete").forEach((button) => {
    button.addEventListener("click", deleteMedia);
});

document.querySelectorAll(".btnVideoDelete").forEach((button) => {
    button.addEventListener("click", deleteMedia);
});