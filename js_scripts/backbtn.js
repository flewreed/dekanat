(function(window, location) {
    history.replaceState(null, document.title, location.pathname+"#!/stealingyourhistory");
    history.pushState(null, document.title, location.pathname);

    window.addEventListener("popstate", function() {
        if(location.hash === "#!/stealingyourhistory") {
            history.replaceState(null, document.title, location.pathname);
            setTimeout(function(){
                location.replace("index.php");
            },0);
        }
    }, false);
}(window, location));