// Number of pages of the list of comments
var page = 1;
// Status of loading comments
var loading = false;
// Load comments button
var loadMoreButton = document.getElementById("loadComment");
// Counter comments
var countComment = 0;

/**
 * Loads more comments for a given slug and page number.
 *
 * @return {void}
 */
function loadMoreComments()
{
    if (loading) {
        return;
    }

    loading = true;

    // Get slug from URL
    var slug = window.location.pathname.split("/").pop();

    fetch(`/load-more-comments/${slug}?page=${page}`)
        .then(response => response.json())
        .then(data => {
            // Loop through the comments and create HTML elements for each
            data.comments.forEach(comment => {
                // Create new comment container
                var commentContainer = document.createElement("div");
                commentContainer.className = "comment p-3 d-flex";

                // Create profile image container
                var profileContainer = document.createElement("div");
                profileContainer.className = "profil d-flex align-items-center me-4";

                // Create profile image
                var profileImage = document.createElement("img");

                if (comment && comment.avatar){
                    profileImage.src = "/img/avatar/mini/300x300-" + comment.avatar;
                } else {
                    profileImage.src = "/img/avatar/mini/300x300-default_avatar.webp";
                }

                profileImage.alt = "Photo de profil";
                profileImage.className = "img-fluid rounded-circle";
                profileImage.style.width = "50px";

                profileContainer.appendChild(profileImage);
                commentContainer.appendChild(profileContainer);

                // Create comment content container
                var contentContainer = document.createElement("div");
                contentContainer.className = "bg-light rounded p-2 ml-3";
                contentContainer.style.width = "600px";

                // Create comment content
                var commentContent = document.createElement("div");
                commentContent.className = "comment-content d-flex align-center";

                var commentText = document.createElement("p");
                commentText.className = "text-light mt-3 bg-success bg-gradient rounded p-3";
                commentText.textContent = comment.content;

                commentContent.appendChild(commentText);
                contentContainer.appendChild(commentContent);

                // Create comment info container
                var infoContainer = document.createElement("div");
                infoContainer.className = "comment-info d-flex gap-2 flex-row-reverse";

                // Create date element
                var dateElement = document.createElement("p");
                dateElement.textContent = `The : ${comment.date}`;

                // Create user element
                var userElement = document.createElement("p");

                if (comment.lastname && comment.firstname){
                    userElement.innerHTML = "<strong>" + comment.firstname + " " + comment.lastname + "</strong>&nbsp;";
                } else{
                    userElement.innerHTML = "<strong>" + comment.user + "</strong>&nbsp;";
                }

                infoContainer.appendChild(dateElement);
                infoContainer.appendChild(userElement);
                contentContainer.appendChild(infoContainer);

                commentContainer.appendChild(contentContainer);

                // Add the new comment to the container
                document.querySelector("#listCommments").appendChild(commentContainer);
                countComment++
            });

            loading = false;

            if (countComment === 0 && data.comments.length === 0) {
                loadMoreButton.style.display = "none";
                var flagEmptyComment = document.getElementById("flagEmptyComment");
                flagEmptyComment.classList.remove("d-none");
                flagEmptyComment.classList.add("d-block");
            }

            if (data.comments.length === 0) {
                loadMoreButton.style.display = "none";
            }

            page++;
        })
        .catch(error => {
            console.error("Error:", error);
        })
        .finally(() => {
            loading = false;
        });
}

/**
 * When you click, we load the comments
 */
loadMoreButton.addEventListener("click", loadMoreComments);

/**
 * When loading, we display the first available comments
 */
document.addEventListener("DOMContentLoaded", loadMoreComments);
