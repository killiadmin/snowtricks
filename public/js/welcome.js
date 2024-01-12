// Number of pages of the list of figures
var page = 1;

// Variable to track loading status
var loading = false;

// Figure card affection bloc
var blocCards = document.querySelector(".bloc_cards");

// Loading spinner
var loadSpinner = document.getElementById("load_spinner");

/**
 * Deletes a figure with the given slug by sending a DELETE request to the server.
 *
 * @param {string} slug - The slug of the figure to be deleted.
 * @return {Promise} - A promise that resolves to the JSON response from the server.
 *                    If there is an error, the promise is rejected with an error object.
 */
function deleteFigureWithTrash(slug)
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
        .then(() => {
            var card = document.getElementById("card-" + slug);
            if (card) {
                card.parentNode.removeChild(card);
            }
        })
        .catch(error => {
            console.error("There was a problem with the fetch operation:", error);
        });
}

/**
 * Loading more data asynchronously when the user scrolls down.
 * @return {void}
 */
function loadMore()
{
    var flagEmptyData = document.getElementById("flagEmptyData");

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
            if (loadSpinner) {
                loadSpinner.style.display = "flex";
            }

            if (data.length > 0){
                if(flagEmptyData) {
                    flagEmptyData.remove();
                }
            } else {
                if(flagEmptyData) {
                    flagEmptyData.style.display = "block";
                    loadSpinner.style.display = "none";
                }
            }

            //Creation of new figure cards
            data.forEach((item) => {
                setTimeout(node => {
                    // Created new card
                    var newCard = document.createElement("div");
                    newCard.className = "card";
                    newCard.style.width = "17rem";
                    newCard.style.height = "11rem";
                    newCard.id = "card-" + item.slug;

                    // Add the image part of the card
                    var imgCard = document.createElement("div");
                    imgCard.className = "img-card";
                    imgCard.style.textAlign = "center";
                    imgCard.style.width = "100%";
                    imgCard.style.height = "100%";
                    imgCard.style.overflow = "hidden";

                    var img = document.createElement("img");
                    let imgValid = "";

                    for (let picture of item.picture) {
                        if (picture) {
                            imgValid = picture;
                            break;
                        }
                    }

                    img.src = imgValid ? "/img/uploads/mini/300x300-" + imgValid : "/img/figure-0001.jpeg";
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
                    titleLink.href = "/tricks/details/" + item.slug;

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
                    trashIcon.innerHTML = '<i class="fa-solid fa-trash-can text-black"></i>'
                    trashIcon.style.cursor = "pointer";
                    /*trashIcon.addEventListener("click", function (event) {
                        deleteFigureWithTrash(item.slug);
                    });*/

                    var validateDelete = document.createElement("a");
                    validateDelete.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                    validateDelete.style.cursor = "pointer";
                    validateDelete.style.display = "none";

                    var cancelDelete = document.createElement("a");
                    cancelDelete.innerHTML = '<i class="fa-solid fa-xmark text-danger"></i>';
                    cancelDelete.style.cursor = "pointer";
                    cancelDelete.style.display = "none";

                    buttonCard.appendChild(validateDelete);
                    buttonCard.appendChild(cancelDelete);
                    buttonCard.appendChild(pencilIcon);
                    buttonCard.appendChild(trashIcon);

                    trashIcon.addEventListener("click", function () {
                        this.style.display = "none";
                        validateDelete.style.display = "inline";
                        cancelDelete.style.display = "inline";
                        pencilIcon.style.display = "none";
                    });

                    validateDelete.addEventListener("click", function () {
                        deleteFigureWithTrash(item.slug);
                    });

                    cancelDelete.addEventListener("click", function () {
                        this.style.display = "none";
                        validateDelete.style.display = "none";
                        trashIcon.style.display = "inline";
                        pencilIcon.style.display = "inline";
                    });

                    cardBody.appendChild(titleLink);
                    cardBody.appendChild(buttonCard);

                    newCard.appendChild(cardBody);

                    // Add the new card to the container
                    blocCards.appendChild(newCard);
                    loading = false;

                    setTimeout(() => {
                        if (loadSpinner) {
                            loadSpinner.style.display = "none";
                        }
                    }, 3000);
                }, 2000);
            });
            page++;
            loading = false;
        })
        .catch(error => {
            console.error("Error:", error);
            loading = false;
            if (loadSpinner) {
                loadSpinner.style.display = "none";
            }
        });
}

/**
 * When the user scrolls, the figure loading method applies
*/
window.addEventListener("scroll", function() {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
        loadMore();
    }
});
