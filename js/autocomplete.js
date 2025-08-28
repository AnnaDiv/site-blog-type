function autocomplete(inp, arr) {
  var currentFocus;

  inp.addEventListener("input", function(e) {
    var a, b, i, val = this.value;
    closeAllLists();

    // trigger after 2 characters
    if (!val || val.length < 2) { 
      return false; 
    }

    if (!val) { return false; }
    currentFocus = -1;

    // Create container for autocomplete items
    a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");

    // Scroll only inside dropdown
    a.style.maxHeight = "150px";     // ~4 items tall
    a.style.overflowY = "auto";      // scroll only this
    a.style.border = "1px solid #ccc";
    a.style.position = "absolute";   // stays floating
    a.style.backgroundColor = "#fff";
    a.style.zIndex = "99";
    a.style.width = this.offsetWidth + "px"; // same width as input
    a.style.top = this.offsetTop + this.offsetHeight + "px";
    a.style.left = this.offsetLeft + "px";

    this.parentNode.appendChild(a);

    // Loop through array and match input
    for (i = 0; i < arr.length; i++) {
      if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
        b = document.createElement("DIV");
        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
        b.innerHTML += arr[i].substr(val.length);
        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";

        b.addEventListener("click", function(e) {
          inp.value = this.getElementsByTagName("input")[0].value;
          closeAllLists();
        });
        a.appendChild(b);
      }
    }
  });

  inp.addEventListener("keydown", function(e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) { // down
      currentFocus++;
      addActive(x);
      if (x && x[currentFocus]) {
        x[currentFocus].scrollIntoView({ block: "nearest" });
      }
    } else if (e.keyCode == 38) { // up
      currentFocus--;
      addActive(x);
      if (x && x[currentFocus]) {
        x[currentFocus].scrollIntoView({ block: "nearest" });
      }
    } else if (e.keyCode == 13) { // enter
      e.preventDefault();
      if (currentFocus > -1) {
        if (x) x[currentFocus].click();
      }
    }
  });

  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    x[currentFocus].classList.add("autocomplete-active");
    x[currentFocus].style.backgroundColor = "#e9e9e9";
  }

  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
      x[i].style.backgroundColor = "#fff";
    }
  }

  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        if (x[i].parentNode) {
          x[i].parentNode.removeChild(x[i]);
        }
      }
    }
  }

  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}