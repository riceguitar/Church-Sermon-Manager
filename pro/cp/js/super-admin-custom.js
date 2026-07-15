window.onload = function () {
    var Anchors = document.querySelectorAll("a");
    for (let i = 0; i < Anchors.length; i++) {
        if (Anchors[i].href.search('sermonsoldoldold') !== -1) {
            Anchors[i].href = Anchors[i].href.replace("sermonsoldoldold", "sermon");
            continue;
        }
        if (Anchors[i].href.search("/series/") !== -1) {
            Anchors[i].href = Anchors[i].href.replace("/series/","/sermon-series/");
        }
    }
};