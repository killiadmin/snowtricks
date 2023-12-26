var modal = document.getElementById("myModal");

function updateIds(element) {
    if (element.id) {
        element.id = element.id + '_' + Date.now();
    }

    var children = element.children;
    for (var i = 0; i < children.length; i++) {
        updateIds(children[i]);
    }
}

document.getElementById("addMedia").addEventListener("click", addComponentMedia);

document.getElementById("addImage").addEventListener("click", function() {
    var hiddenFormPicture = document.getElementById("hiddenFormPicture");
    var clonedFormPicture = hiddenFormPicture.cloneNode(true);

    updateIds(clonedFormPicture);
    document.getElementById("blocImages").appendChild(clonedFormPicture);

    modal.style.display = "none";
});

document.getElementById("addVideo").addEventListener("click", function() {
    var hiddenFormVideo = document.getElementById("hiddenFormVideo");
    var clonedFormVideo = hiddenFormVideo.cloneNode(true);

    clonedFormVideo.style.display = "block";
    updateIds(clonedFormVideo);

    document.getElementById("blocVideos").appendChild(clonedFormVideo);

    modal.style.display = "none";
});

function addComponentMedia() {
    modal.style.display = "block";
}
