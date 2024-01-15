/**
 * Deletes a media by sending an HTTP DELETE request to the server.
 *
 * @param {Event} event - The event object that triggered the delete operation.
 */
function deleteMedia(event)
{
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

/**
 * Creates and appends an empty video media component to the specified container.
 * @function componentEmptyMediaVideo
 * @returns {void}
 */
function componentEmptyMediaVideo()
{
    var divEmptyVideo = document.createElement("div");
    divEmptyVideo.id = "emptyMediaVideo";
    divEmptyVideo.className = "d-flex justify-content-center w-100";
    var pEmptyVideo = document.createElement("p");
    pEmptyVideo.innerText = "No video media available  ";
    pEmptyVideo.innerHTML += '<i class="fa-solid fa-person-snowboarding fa-rotate-90 fa-2xl"></i>';
    divEmptyVideo.appendChild(pEmptyVideo);
    document.getElementById("listMediaVideo").appendChild(divEmptyVideo);
}

/**
 * Creates and appends an empty media picture component to the DOM.
 * The component displays a message and an icon indicating that no picture media is available.
 *
 * @function componentEmptyMediaPicture
 * @returns {void}
 */
function componentEmptyMediaPicture()
{
    var divEmptyVideo = document.createElement("div");
    divEmptyVideo.id = "emptyMediaPicture";
    divEmptyVideo.className = "d-flex justify-content-center w-100";
    var pEmptyVideo = document.createElement("p");
    pEmptyVideo.innerText = "No picture media available  ";
    pEmptyVideo.innerHTML += '<i class="fa-solid fa-person-snowboarding fa-rotate-180 fa-2xl"></i>';
    divEmptyVideo.appendChild(pEmptyVideo);
    document.getElementById("listMediaPicture").appendChild(divEmptyVideo);
}

/*
 * We call the click method to delete an picture type media and if it is the last one we flag a message
 */
document.querySelectorAll(".btnImageDelete").forEach((button) => {
    button.addEventListener("click", function(event) {
        deleteMedia(event);
        if (document.getElementById("listMediaPicture").children.length === 1){
            componentEmptyMediaPicture();
        }
    });
});

/**
 * We call the click method to delete an video type media and if it is the last one we flag a message
 */
document.querySelectorAll(".btnVideoDelete").forEach((button) => {
    button.addEventListener("click", function(event) {
        deleteMedia(event);
        if (document.getElementById("listMediaVideo").children.length === 1){
            componentEmptyMediaVideo();
        }
    });
});

export { componentEmptyMediaVideo };
export { componentEmptyMediaPicture};