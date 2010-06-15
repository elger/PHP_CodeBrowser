$(function() {
    $("#tree").jstree({
        "plugins" : ["html_data", "themes"]
    });

    $("#treeToggle").click().toggle(function() {
        $("#tree").animate({
            width: "hide",
            opacity: "hide"
        }, "slow");
    }, function() {
        $("#tree").animate({
            width: "show",
            opacity: "show"
        }, "slow");
    });
});
