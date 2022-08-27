window.onload = () => {
    document.getElementById("main").addEventListener("click", () => {
        chrome.tabs.create({
            url: "https://furkkinov.top/"
        });
    })
}