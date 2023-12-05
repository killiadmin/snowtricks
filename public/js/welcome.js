var page = 1;

// Variable to track loading status
var loading = false;


/**
 * Loading data asynchronous when user scrolls down
 */
function loadMore(node) {
    // Check if loading is in progress, if yes, return
    if (loading) {
        return;
    }

    //Mark loading as in progress
    loading = true;

    //We use fetch to make an HTTP GET request to the specified URL
    fetch(`/load-more?page=${page}`)
        .then(response => response.json())
        .then(data => {
            var loadSpinner = document.getElementById("load_spinner");

            if (loadSpinner) {
                loadSpinner.style.display = "flex";
            }

            // Utilisez votre logique pour ajouter les nouvelles cartes
            data.forEach((item) => {
                setTimeout(node => {
                    // Created new card
                    var newCard = document.createElement("div");
                    newCard.className = "card";
                    newCard.style.width = "17rem";
                    newCard.style.height = "11rem";

                    // Add the image part of the card
                    var imgCard = document.createElement("div");
                    imgCard.className = "img-card";
                    imgCard.style.textAlign = "center";
                    imgCard.style.width = "100%";
                    imgCard.style.height = "100%";
                    imgCard.style.overflow = "hidden";

                    var img = document.createElement("img");
                    img.src = item.picture;
                    img.className = "card-img-top";
                    img.alt = "figure snowboarding";
                    img.style.objectFit = "cover";
                    img.style.width = "100%";
                    img.style.height = "100%";

                    imgCard.appendChild(img);
                    newCard.appendChild(imgCard);

                    // Add the body part of the card
                    var cardBody = document.createElement("div");
                    cardBody.className = "card-body d-flex align-items-center justify-content-between p-2";
                    cardBody.style.borderTop = "outset";

                    var titleLink = document.createElement("a");
                    titleLink.href = "#";

                    var title = document.createElement("h5");
                    title.className = "card-title text-decoration-none text-black";
                    title.textContent = item.title.length > 18 ? item.title.substring(0, 15) + " ..." : item.title;
                    title.title = item.title;

                    titleLink.appendChild(title);

                    var buttonCard = document.createElement("div");
                    buttonCard.className = "bloc_button-card d-flex gap-3";

                    // Add buttons for each action (edit and delete)
                    var pencilIcon = document.createElement("a");
                    pencilIcon.href = "#";
                    pencilIcon.innerHTML = '<i class="fa-solid fa-pencil text-black"></i>';

                    var trashIcon = document.createElement("a");
                    trashIcon.href = "#";
                    trashIcon.innerHTML = '<i class="fa-solid fa-trash-can text-black"></i>'

                    buttonCard.appendChild(pencilIcon);
                    buttonCard.appendChild(trashIcon);

                    cardBody.appendChild(titleLink);
                    cardBody.appendChild(buttonCard);

                    newCard.appendChild(cardBody);

                    // Add the new card to the container
                    document.querySelector('.bloc_cards').appendChild(newCard);
                    loading = false;

                    setTimeout(() => {
                        if (loadSpinner) {
                            loadSpinner.style.display = "none";
                        }
                    }, 3000);

                }, 2000);
            });
            page++;
        })
        .catch(error => {
            console.error("Error:", error);
            loading = false;
        });
}

// On user scroll
window.addEventListener("scroll", function() {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
        loadMore();
    }
});

// When the Home page loads
document.addEventListener("DOMContentLoaded", function() {
    loadMore();
});