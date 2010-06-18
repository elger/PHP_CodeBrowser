$(function() {
    $("#tree").jstree({
        "plugins" : ["html_data", "themes"]
    });

    $(".treeDir").click(function() {
        $("#tree").jstree("toggle_node", this);
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
