import { createBtnDelete } from "./utilsForm.js";
import { processMedia } from "./utilsForm.js";
import { closeComponentMedia } from "./utilsForm.js";
import { openComponentMedia } from "./utilsForm.js";

// Media box to add a video field or a picture field
var modal = document.getElementById("boxMedia");
//Media element for append
var medias = document.getElementById("medias");
// Video counter
var countVideos = 0;
// Picture counter
var countPictures = 0;

/**
 * Updates the message display based on the countVideos and countPictures variables.
 * If there are no videos or pictures associated, it will display a message saying "No media is associated".
 *
 * @return {void}
 */
function updateMessageDisplay()
{
    var messageDiv = document.getElementById("messageEmptyData");

    if (!messageDiv) {
        messageDiv = document.createElement("div");
        messageDiv.id = "messageEmptyData";
        messageDiv.className = "text-center";
        medias.appendChild(divMessage);
    }

    if (countVideos === 0 && countPictures === 0) {
        messageDiv.textContent = "No media is associated";
        messageDiv.style.display = "block";
    }
}

/**
 * If no media is associated with the form, a message is displayed
 */
if (countVideos === 0 && countPictures === 0) {
    var divMessage = document.createElement("div");
    divMessage.id = "messageEmptyData";
    divMessage.className = "text-center";
    divMessage.textContent = "No media is associated";
    medias.appendChild(divMessage);
}

/**
 * Several eventListeners to dynamically add media to the form
 */
document.getElementById("addMedia").addEventListener("click",function () {
    openComponentMedia(modal)
});

document.getElementById(("closeMedia")).addEventListener("click", function (){
    closeComponentMedia(modal);
});

document.getElementById("new_figure_medias").appendChild(medias);

/**
 * Method add for a bloc image
 */
document.getElementById("addImage").addEventListener("click", function() {
    var medImageDiv = processMedia("picture");

    countPictures++

    var labelElemPicture = medImageDiv.querySelector("label");
    labelElemPicture.className = "w-25";
    labelElemPicture.textContent += " " + countPictures;

    medias.appendChild(medImageDiv);

    var btnDelete = createBtnDelete();
    medImageDiv.append(btnDelete);

    btnDelete.addEventListener("click", function(){
        this.previousElementSibling.parentElement.remove();
        countPictures--;
        updateMessageDisplay();
    });

    document.getElementById("messageEmptyData").style.display = "none";

    closeComponentMedia(modal);
});

/**
 * Method add for a bloc video
 */
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addVideo").addEventListener("click", function() {
        var medVideo = processMedia("video");
        medias.appendChild(medVideo.parentNode);

        countVideos++;

        var labelElemVideo = medVideo.parentNode.querySelector("label");
        labelElemVideo.className = "w-25";
        labelElemVideo.textContent += " " + countVideos;

        var btnDelete = createBtnDelete();
        medVideo.parentNode.append(btnDelete);

        btnDelete.addEventListener("click", function(){
            this.previousElementSibling.parentElement.remove();
            countVideos--;
            updateMessageDisplay();
        });

        document.getElementById("messageEmptyData").style.display = "none";
        closeComponentMedia(modal);
    });
});

/**
 * Submit Form for a created a new figure
 */
if (document.getElementById("createFigureBtn")){
    document.getElementById("createFigureBtn").addEventListener("click", function() {
        document.getElementById("formFigure").submit();
    });
}
