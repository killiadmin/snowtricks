var listBootmark = document.getElementById("listBookmark");
var bootMarkItems = document.querySelectorAll(".bootMarkItem");
var logoutBtnNav = document.getElementById("logoutBtnNav");

if (listBootmark) {
    listBootmark.addEventListener("click", function () {
        listBootmark.classList.remove("d-block");
        listBootmark.classList.add("d-none");


        logoutBtnNav.classList.remove("d-block");
        logoutBtnNav.classList.add("d-none");

        bootMarkItems.forEach(function (bootMarkItem) {
            bootMarkItem.classList.remove("d-none");
            bootMarkItem.classList.add("d-block");
        });
    });
}
