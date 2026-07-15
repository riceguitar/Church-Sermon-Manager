function copyFunction(containerid) {
        /* Select the text */
        if (document.selection) { // IE
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }

      /* Copy the text */
      document.execCommand("copy");
    }

    function showPopup(twitterURL,facebookURL,googleURL,mgcount,urllink) {
        var btn = document.getElementById('share-button'+mgcount);
        document.getElementById("sm-facebook-url").href=facebookURL; 
        document.getElementById("sm-twitterURL-url").href=twitterURL; 
        document.getElementById("sm-googleURL-url").href=googleURL; 
        document.getElementById("text-url").innerHTML=urllink; 
        var offset = btn.getBoundingClientRect();
        // set position popup
        var popup = document.querySelector(".social-dropdown-menu");
        popup.style.top = ( offset.top - popup.offsetHeight + offset.height) + 'px';
        popup.style.left = ( offset.left - popup.offsetWidth + offset.width ) + 'px';
        // get element and add class
        var element = document.getElementById("social-dropdown");
        element.classList.add("active");

        // encode html code to copy
        /*var code = document.querySelector("#text-code");
        var encoded = encodeHTML(code.innerHTML);
        code.innerHTML = encoded;*/
    }

    function closePopup() {
        var element = document.getElementById("social-dropdown");
        element.classList.remove("active");
    }

    window.addEventListener("scroll", closePopup);

    var encodeHTML = (function() {
 
        var encodeHTMLmap = {
            "&" : "&amp;",
            "'" : "&#39;",
            '"' : "&quot;",
            "<" : "&lt;",
            ">" : "&gt;"
        };
     
        /**
        * encode character as HTML entity
        * @param {String} ch character to map to entity
        * @return {String}
        */
        function encodeHTMLmapper(ch) {
            return encodeHTMLmap[ch];
        }
     
        return function(text) {
            // search for HTML special characters, convert to HTML entities
            return text.replace(/[&"'<>]/g, encodeHTMLmapper);
        };
     
    })();