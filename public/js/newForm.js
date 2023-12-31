var modal = document.getElementById("boxMedia");

document.getElementById("addMedia").addEventListener("click", addComponentMedia);
document.getElementById(("closeMedia")).addEventListener("click", closeComponentMedia);

//Method add a bloc image
document.getElementById("addImage").addEventListener("click", function() {
    var hiddenFormPicture = document.getElementById("hiddenFormPicture");
    var clonedFormPicture = hiddenFormPicture.cloneNode(true);

    updateIds(clonedFormPicture);
    document.getElementById("new_figure_medias").append(clonedFormPicture);

    var btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger";
    btnDelete.innerText = "X";
    btnDelete.style.float = "right";

    clonedFormPicture.append(btnDelete);

    btnDelete.addEventListener("click", function(){
        this.previousElementSibling.parentElement.remove();
    })

    modal.style.display = "none";
});

//Method add a bloc video
document.getElementById("addVideo").addEventListener("click", function() {
    var hiddenFormVideo = document.getElementById("hiddenFormVideo");
    var clonedFormVideo = hiddenFormVideo.cloneNode(true);
    clonedFormVideo.style.display = "block";
    updateIds(clonedFormVideo);

    document.getElementById("new_figure_medias").append(clonedFormVideo);

    var btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.className = "btn btn-danger";
    btnDelete.innerText = "X";
    btnDelete.style.float = "right";

    clonedFormVideo.append(btnDelete);

    btnDelete.addEventListener("click", function(){
        this.previousElementSibling.parentElement.remove();
    })

    modal.style.display = "none";
});

// Insert a button create after media element

document.getElementById("createFigureBtn").addEventListener("click", function() {
    document.getElementById("formFigure").submit();
    document.getElementById("formMedia").submit();
});


//****************************************FUNCTIONS*******************************************************************\\

function updateIds(element) {
    if (element.id) {
        element.id = element.id + '_' + Date.now();
    }

    var children = element.children;
    for (var i = 0; i < children.length; i++) {
        updateIds(children[i]);
    }
}

function addComponentMedia() {
    modal.style.display = "block";
}

function closeComponentMedia() {
    modal.style.display = "none";
}
