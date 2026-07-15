window.onload = function () {
    var navTabs = document.getElementsByClassName("nav-tab");
    console.log("Not a Super Admin");
    for (let i = 0; i < navTabs.length; i++) {
        if (navTabs[i].innerHTML.toLowerCase() === 'podcast') {
            navTabs[i].className += " nav-tab-active is-super-admin-settings";
            break;
        }
    }
    var Anchors = document.querySelectorAll("a");
    for (let i = 0; i < Anchors.length; i++) {
        if (Anchors[i].href.search('sermonsoldoldold') !== -1) {
            Anchors[i].href = Anchors[i].href.replace("sermonsoldoldold", "sermon");
            console.log(i);
        }
    }
};