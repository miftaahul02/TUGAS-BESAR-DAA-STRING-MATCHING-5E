const input = document.getElementById("search");
const suggestion = document.getElementById("suggestion");

input.addEventListener("keyup", function(e) {
    const q = input.value.trim();

    if (q.length === 0) {
        suggestion.innerHTML = "";
        suggestion.style.display = "none";
        return;
    }

    if (e.key === "Enter") return;

    fetch("search.php?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            suggestion.innerHTML = "";
            
            if (data.length > 0) {
                suggestion.style.display = "block";
                
                const limitData = data.slice(0, 7);

                limitData.forEach(text => {
                    const li = document.createElement("li");
                    const safeQuery = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp("(" + safeQuery + ")", "gi");
                    
                    li.innerHTML = text.replace(regex, "<strong>$1</strong>");

                    li.onclick = () => {
                        input.value = text;
                        suggestion.innerHTML = "";
                        suggestion.style.display = "none";
                    };

                    suggestion.appendChild(li);
                });
            } else {
                suggestion.style.display = "none";
            }
        })
        .catch(err => console.error("Error:", err));
});

document.addEventListener("click", function(e) {
    if (!input.contains(e.target) && !suggestion.contains(e.target)) {
        suggestion.style.display = "none";
    }
});