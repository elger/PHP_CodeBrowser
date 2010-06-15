$(function() {
    $("#tree").jstree({
        "plugins" : ["html_data", "themes"]
    });

    var w = $("#tree").width() + 40;
    var h = $("#tree").height() + 40;
});
