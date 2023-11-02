window.addEventListener('scroll', doTheViewThingy);

function doTheViewThingy() {
    var endOfArticle = document.getElementById("endOfArticle");
    if ((window.scrollY + window.innerHeight) >= (endOfArticle.offsetTop - 100)) {
        $.ajax(
            {
                url: "/viewCount.php?id=" + articleId,
            }
        );
        window.removeEventListener('scroll', doTheViewThingy);
    }
}