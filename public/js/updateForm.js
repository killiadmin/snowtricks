import { componentEmptyMediaVideo } from "./utilsForm.js";
import { componentEmptyMediaPicture} from "./utilsForm.js";
import { createBtnDelete } from "./utilsForm.js";
import { processMedia } from "./utilsForm.js";

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
 * Displays the component media in a modal.
 *
 * @return {void}
 */
function addComponentMedia()
{
    modal.style.display = "block";
}

/**
 * Closes the component media.
 *
 * @return {void}
 */
function closeComponentMedia()
{
    modal.style.display = "none";
}

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
document.getElementById("addMedia").addEventListener("click", addComponentMedia);
document.getElementById(("closeMedia")).addEventListener("click", closeComponentMedia);

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
            btnEditFigure.style.display = "block";
            btnEditMedia.style.display = "none";
        }
    });

    if (document.getElementById("emptyMediaPicture")){
        document.getElementById("emptyMediaPicture").remove();
    }

    countMedia++;
    btnEditFigure.style.display = "none";
    btnEditMedia.style.display = "block";
    closeComponentMedia();
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
                btnEditFigure.style.display = "block";
                btnEditMedia.style.display = "none";
            }

            this.previousElementSibling.parentElement.remove();
        });

        if (document.getElementById("emptyMediaVideo")){
            document.getElementById("emptyMediaVideo").remove();
        }

        countMedia++;
        btnEditFigure.style.display = "none";
        btnEditMedia.style.display = "block";
        closeComponentMedia();
    });
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
