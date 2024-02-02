import { btnSeeMedias } from "./utilsForm.js";
import { componentEmptyMediaVideo } from "./utilsForm.js";
import { componentEmptyMediaPicture} from "./utilsForm.js";
import { createBtnDelete } from "./utilsForm.js";
import { processMedia } from "./utilsForm.js";
import { openComponentMedia } from "./utilsForm.js";
import { closeComponentMedia } from "./utilsForm.js";

// Media box to add a video field or a picture field
var modal = document.getElementById("boxMedia");
// Media element for append
var medias = document.getElementById("medias");
// Button for submit a form edition
var btnEditFigure = document.getElementById("btnEditFigure");
//Button for edit media
var btnEditMedia = document.getElementById("btnEditMedia");
//Counter medias
var countMedia = 0;

/**
 * Creates an empty message content.
 *
 * @returns {HTMLElement} The empty message element.
 */
function messageEmptyContent(idElement, messageError)
{
    var ulElement = document.createElement("ul");
    ulElement.id = idElement;
    ulElement.className = "alert alert-danger";
    ulElement.style.listStyle = "none";
    ulElement.innerHTML = "<li>" + messageError + "</li>";
    return ulElement;
}

/**
 * Several eventListeners to dynamically add media to the form
 */
document.getElementById("addMedia").addEventListener("click", function (){
    openComponentMedia(modal);
});
document.getElementById(("closeMedia")).addEventListener("click", function (){
    closeComponentMedia(modal);
});

/**
 * Method add  for a bloc image
 */
document.getElementById("addImage").addEventListener("click", function() {
    var medImageDiv = processMedia("picture");

    var labelElemPicture = medImageDiv.querySelector("label");
    labelElemPicture.className = "w-25";

    medias.appendChild(medImageDiv);

    var btnDelete = createBtnDelete();
    medImageDiv.append(btnDelete);

    btnDelete.addEventListener("click", function(){
        this.previousElementSibling.parentElement.remove();
        countMedia--;

        if (countMedia === 0) {
            openComponentMedia(btnEditFigure);
            closeComponentMedia(btnEditMedia);
        }
    });

    if (document.getElementById("emptyMediaPicture")){
        document.getElementById("emptyMediaPicture").remove();
    }

    countMedia++;
    openComponentMedia(btnEditMedia);
    closeComponentMedia(btnEditFigure);
    closeComponentMedia(modal);
});

/**
 * Method add for a bloc video
 */
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addVideo").addEventListener("click", function() {
        var medVideo = processMedia("video");
        medias.appendChild(medVideo.parentNode);

        var labelElemVideo = medVideo.parentNode.querySelector("label");
        labelElemVideo.className = "w-25";

        var btnDelete = createBtnDelete();
        medVideo.parentNode.append(btnDelete);

        btnDelete.addEventListener("click", function(){
            countMedia--;

            if (countMedia === 0) {
                openComponentMedia(btnEditFigure);
                closeComponentMedia(btnEditMedia);
            }

            this.previousElementSibling.parentElement.remove();
        });

        if (document.getElementById("emptyMediaVideo")){
            document.getElementById("emptyMediaVideo").remove();
        }

        countMedia++;
        openComponentMedia(btnEditMedia);
        closeComponentMedia(btnEditFigure);
        closeComponentMedia(modal);
    });

    if (document.getElementById("seeMediasMobileUpdate")){
        var seeMediasMobile = document.getElementById("seeMediasMobileUpdate");
        var hideMediasMobile =  document.getElementById("hideMediasMobileUpdate");

        seeMediasMobile.appendChild(btnSeeMedias("See medias"));
        hideMediasMobile.appendChild(btnSeeMedias("Hide medias"));

        seeMediasMobile.addEventListener("click", function () {
            document.getElementById("blocAllMediasUpdate").classList.remove("d-none");
            hideMediasMobile.classList.remove("d-none");
            seeMediasMobile.classList.add("d-none");
        });

        hideMediasMobile.addEventListener("click", function () {
            document.getElementById("blocAllMediasUpdate").classList.add("d-none");
            hideMediasMobile.classList.add("d-none");
            seeMediasMobile.classList.remove("d-none");
        });
    }
});

/**
 * Button submit form figure
 */
btnEditFigure.addEventListener("click", function () {
    let newFigureTitle = document.getElementById("new_figure_title");
    let newFigureContentFigure = document.getElementById("new_figure_contentFigure");

    if (newFigureTitle.value.trim() === "") {
        if (!document.getElementById("flagTitleForm")){
            newFigureTitle.parentNode.appendChild(messageEmptyContent("flagTitleForm", "Title is required"));
        }
        return;
    }

    if (newFigureContentFigure.value.trim() === ""){
        if (!document.getElementById("flagTitleForm")){
            newFigureContentFigure.parentNode.appendChild(messageEmptyContent("flagContentFigure","Content is required"));
        }
        return;
    }

    document.getElementById(("editionFormFigure")).submit();
})

/**
 * Button submit form media
 */
btnEditMedia.addEventListener("click", function (){
    document.getElementById(("editionFormMedia")).submit();
})

/**
 * Added a component to indicate to the user if there are no associate videos
 */
if (document.getElementById("listMediaVideo").children.length === 0){
    componentEmptyMediaVideo();
}

/**
 * Added a component to indicate to the user if there are no associate picture
 */
if (document.getElementById("listMediaPicture").children.length === 0){
    componentEmptyMediaPicture();
}
