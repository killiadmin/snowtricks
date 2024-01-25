import { createBtnDelete } from "./utilsForm.js";
import { processMedia } from "./utilsForm.js";
import { openComponentMedia } from "./utilsForm.js";
import { closeComponentMedia } from "./utilsForm.js";

// Media box to add a video field or a picture field
var modal = document.getElementById("boxMedia");
// Media element for append
var medias = document.getElementById("medias");
// Counter medias
var countMedia = 0;
// Bloc editing figure
var blocEditingFigure = document.getElementById("blocEditingFigure");
// Button Editing Media Trick
var btnEditMediaTrick = document.getElementById("btnEditMediaTrick");

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
if (document.getElementById("btnDelete")){
    document.getElementById("btnDelete").addEventListener("click", function () {
        openComponentMedia(document.getElementById("boxDelete"));
    });
}

document.getElementById("closeBoxDelete").addEventListener("click", function () {
    closeComponentMedia(document.getElementById("boxDelete"));
});

document.getElementById("deleteFigure").addEventListener("click", function () {
    deleteFigure(getSlug());
});

/**
 * Methods for UPDATE elements
 * Declarations EventListener for update a figures
 */
if (document.getElementById("editButton")){
    document.getElementById("editButton").addEventListener("click", function() {
        document.querySelectorAll(".read-mode, .edit-mode").forEach(function(element) {
            element.style.display = (element.style.display === "none") ? "block" : "none";
        });
    });
}

document.getElementById("closeMedia").addEventListener("click", function () {
    closeComponentMedia(modal);
});

document.getElementById("addMedia").addEventListener("click", function () {
    openComponentMedia(modal);
});

btnEditMediaTrick.addEventListener("click", function (){
    document.getElementById("editionFormMediaTrick").submit();
})

/**
 * Method add for a bloc video
 */
document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("addVideo")) {
        document.getElementById("addVideo").addEventListener("click", function () {
            var medVideo = processMedia("video");
            medias.appendChild(medVideo.parentNode);

            var labelElemVideo = medVideo.parentNode.querySelector("label");
            labelElemVideo.className = "w-25";

            var btnDelete = createBtnDelete();
            medVideo.parentNode.append(btnDelete);

            btnDelete.addEventListener("click", function () {
                countMedia--;
                this.previousElementSibling.parentElement.remove();

                if (countMedia === 0) {
                    blocEditingFigure.style.display = "block";
                    openComponentMedia(btnEditMediaTrick);
                }
            });

            blocEditingFigure.style.display = "none";

            countMedia++;
            closeComponentMedia(modal);
            openComponentMedia(btnEditMediaTrick);
        });
    }

    /**
     * Method add  for a bloc image
     */
    if (document.getElementById("addImage")){
        document.getElementById("addImage").addEventListener("click", function () {
            var medImageDiv = processMedia("picture");

            var labelElemPicture = medImageDiv.querySelector("label");
            labelElemPicture.className = "w-25";

            medias.appendChild(medImageDiv);

            var btnDelete = createBtnDelete();
            medImageDiv.append(btnDelete);

            btnDelete.addEventListener("click", function () {
                this.previousElementSibling.parentElement.remove();
                countMedia--;

                if (countMedia === 0) {
                    blocEditingFigure.style.display = "block";
                    openComponentMedia(btnEditMediaTrick);
                }
            });

            blocEditingFigure.style.display = "none";

            countMedia++;
            closeComponentMedia(modal);
            openComponentMedia(btnEditMediaTrick);
        });
    }
});
