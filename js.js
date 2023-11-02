function dropdownFunction(id) {
    document.getElementById(id).classList.toggle("show");

    if (id == "sectionDropdown") {
        var drop = document.getElementById("searchDropdown");
        if (drop.classList.contains('show')) {
            drop.classList.remove('show');
            var img = document.getElementById("searchArrow").src = "resources/arrow down.png";
        }
        drop = document.getElementById(id);
        if (drop.classList.contains('show')) {
            document.getElementById("sectionArrow").src = "resources/arrow up.png";
        }
        else {
            document.getElementById("sectionArrow").src = "resources/arrow down.png";
        }
    }
    else if (id == "searchDropdown") {
        var drop = document.getElementById("sectionDropdown");
        if (drop.classList.contains('show')) {
            drop.classList.remove('show');
            var img = document.getElementById("sectionArrow").src = "resources/arrow down.png";
        }
        drop = document.getElementById(id);
        if (drop.classList.contains('show')) {
            document.getElementById("searchArrow").src = "resources/arrow up.png";
        }
        else {
            document.getElementById("searchArrow").src = "resources/arrow down.png";
        }
    }
}