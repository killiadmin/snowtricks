import { componentEmptyMediaVideo } from "./utilsForm.js";
import { componentEmptyMediaPicture} from "./utilsForm.js";

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
 * Several eventListeners to dynamically add media to the form
 */
document.getElementById("addMedia").addEventListener("click", addComponentMedia);
document.getElementById(("closeMedia")).addEventListener("click", closeComponentMedia);

/**
 * Method add  for a bloc image
 */
document.getElementById("addImage").addEventListener("click", function() {
    var prototypeMedias = document.getElementById("medias").getAttribute("data-prototype");
    var newIndex = medias.children.length;
    var newForm = prototypeMedias.replace(/__media__/g, newIndex);

    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = newForm;

    var medImageDiv = tempDiv.querySelector('input[type="file"]').parentNode;

    medImageDiv.className = "d-flex align-items-center justify-content-between gap-3 mb-3";

    var labelElemPicture = medImageDiv.querySelector("label");
    labelElemPicture.className = "w-25";;

    document.getElementById("medias").appendChild(medImageDiv);

    var btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger";
    btnDelete.innerText = "X";
    btnDelete.style.float = "right";

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
        var prototype = medias.getAttribute("data-prototype");
        var newIndex = medias.children.length;
        var newForm = prototype.replace(/__media__/g, newIndex);

        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = newForm;

        var medVideo = tempDiv.querySelector(".form-control");

        medVideo.parentNode.className = "d-flex align-items-center justify-content-between gap-3 mb-3";

        medias.appendChild(medVideo.parentNode);

        var labelElemVideo = medVideo.parentNode.querySelector("label");
        labelElemVideo.className = "w-25";

        var btnDelete = document.createElement("button");
        btnDelete.type = "button";
        btnDelete.className = "btn btn-danger";
        btnDelete.innerText = "X";
        btnDelete.style.float = "right";

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
 * Button subtmit form figure
 */
btnEditFigure.addEventListener("click", function (){
    document.getElementById(("editionFormFigure")).submit();
})

/**
 * Button subtmit form media
 */
document.getElementById("btnEditMedia").addEventListener("click", function (){
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
