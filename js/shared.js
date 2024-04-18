$(".nav-button").click((value) => {
    const path = (value.target.id == "home") ? "index" : value.target.id;
    window.location.replace(((path == "") ? "index" : path) + ".php");
});

$(".email-link").click((value) => {
    var mailtoLink = 'mailto:' + value.target.innerText;
    window.open(mailtoLink, '_blank');
});