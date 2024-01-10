// Media box to add a video field or a picture field
var modal = document.getElementById("boxMedia");
//Media element for append
var medias = document.getElementById("medias");
// Video counter
var countVideos = 0;
// Picture counter
var countPictures = 0;

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
 * Updates the message display based on the countVideos and countPictures variables.
 * If there are no videos or pictures associated, it will display a message saying "No media is associated".
 *
 * @return {void}
 */
function updateMessageDisplay()
{
    var messageDiv = document.getElementById("messageEmptyData");

    if(!messageDiv) {
        messageDiv = document.createElement("div");
        messageDiv.id = "messageEmptyData";
        messageDiv.className = "text-center";
        medias.appendChild(divMessage);
    }

    if(countVideos === 0 && countPictures === 0){
        messageDiv.textContent = "No media is associated";
        messageDiv.style.display = "block";
    }
}

/**
 * If no media is associated with the form, a message is displayed
 */
if (countVideos === 0 && countPictures === 0){
    var divMessage = document.createElement("div");
    divMessage.id = "messageEmptyData";
    divMessage.className = "text-center";
    divMessage.textContent = "No media is associated";
    medias.appendChild(divMessage);
}

/**
 * Several eventListeners to dynamically add media to the form
 */
document.getElementById("addMedia").addEventListener("click", addComponentMedia);
document.getElementById(("closeMedia")).addEventListener("click", closeComponentMedia);
document.getElementById("new_figure_medias").appendChild(medias);

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

    countPictures++

    var labelElemPicture = medImageDiv.querySelector("label");
    labelElemPicture.className = "w-25";
    labelElemPicture.textContent += " " + countPictures;

    document.getElementById("medias").appendChild(medImageDiv);

    var btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger";
    btnDelete.innerText = "X";
    btnDelete.style.float = "right";

    medImageDiv.append(btnDelete);

    btnDelete.addEventListener("click", function(){
        this.previousElementSibling.parentElement.remove();
        countPictures--;
        updateMessageDisplay();
    });

    document.getElementById("messageEmptyData").style.display = "none";

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

        countVideos++;

        var labelElemVideo = medVideo.parentNode.querySelector("label");
        labelElemVideo.className = "w-25";

        labelElemVideo.textContent += " " + countVideos;

        var btnDelete = document.createElement("button");
        btnDelete.type = "button";
        btnDelete.className = "btn btn-danger";
        btnDelete.innerText = "X";
        btnDelete.style.float = "right";

        medVideo.parentNode.append(btnDelete);

        btnDelete.addEventListener("click", function(){
            this.previousElementSibling.parentElement.remove();
            countVideos--;
            updateMessageDisplay();
        });

        document.getElementById("messageEmptyData").style.display = "none";

        closeComponentMedia();
    });
});

/**
 * Submit Form for a created a new figure
 */
document.getElementById("createFigureBtn").addEventListener("click", function() {
    document.getElementById("formFigure").submit();
});
