function switchLine(lineId) {
    $('#' + lineId).effect("highlight", {color: ''}, 3000);
}

function initReview() {
    $('.hasIssues').cluetip({
        splitTitle: '|',
        activation: 'hover',
        dropShadow: false,
        tracking: true,
        cluetipClass: 'default'
    });
    $("div#sidebar").sidebar({
        width:600,
        height: 400,
        open : "click",
        close: "click",
        position: "right"
    });
}
