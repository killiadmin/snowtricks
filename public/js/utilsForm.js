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

/**
 * Creates a delete button.
 *
 * @returns {HTMLButtonElement} The delete button.
 */
function createBtnDelete()
{
    let btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger";
    btnDelete.innerText = "X";
    btnDelete.style.float = "right";

    return btnDelete
}

/**
 * Process media based on the given media type.
 *
 * @param {string} mediaType - The type of media (video or picture).
 * @return {object} - The processed media element.
 */
function processMedia(mediaType)
{
    var medias = document.getElementById("medias");
    var prototype = medias.getAttribute("data-prototype");
    var newIndex = medias.children.length;
    var newForm = prototype.replace(/__media__/g, newIndex);

    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = newForm;

    var mediaElement;

    if (mediaType === "video") {
        mediaElement = tempDiv.querySelector(".form-control");
        mediaElement.parentNode.className = "d-flex align-items-center justify-content-between gap-3 mb-3";

    } else if (mediaType === "picture") {
        mediaElement = tempDiv.querySelector('input[type="file"]').parentNode;
        mediaElement.className = "d-flex align-items-center justify-content-between gap-3 mb-3";
    }

    return mediaElement
}

/**
 * Opens component media by removing the "d-none" class and adding the "d-block" class to the element.
 *
 * @param {HTMLElement} element - The element to open the component media.
 *
 * @return {void}
 */
function openComponentMedia (element)
{
    element.classList.remove("d-none");
    element.classList.add("d-block");
}

/**
 * Hides a component media element by adding the "d-none" class and removing the "d-block" class.
 *
 * @param {HTMLElement} element - The component media element to be hidden.
 *
 * @return {void}
 */
function closeComponentMedia (element)
{
    element.classList.remove("d-block");
    element.classList.add("d-none");
}

/**
 * Creates a button that allows users to see medias.
 *
 * @returns {HTMLDivElement} - The div element containing the button.
 */
function btnSeeMedias (textBtn)
{
    let button = document.createElement("button");
    button.className = "btn btn-secondary";
    button.textContent = textBtn;

    let div = document.createElement("div");
    div.className = "d-flex justify-content-center d-lg-none mt-3";
    div.appendChild(button);

    return div;
}

/*
 * We call the click method to delete an picture type media and if it is the last one we flag a message
 */
document.querySelectorAll(".btnImageDelete").forEach((button) => {
    button.addEventListener("click", function(event) {
        deleteMedia(event);
        if (document.getElementById("listMediaPicture") && document.getElementById("listMediaPicture").children.length === 1){
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
        if (document.getElementById("listMediaVideo") && document.getElementById("listMediaVideo").children.length === 1){
            componentEmptyMediaVideo();
        }
    });
});

export { componentEmptyMediaVideo };
export { componentEmptyMediaPicture};
export { createBtnDelete };
export { processMedia };
export { closeComponentMedia };
export { openComponentMedia };
export { btnSeeMedias }
