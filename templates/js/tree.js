$(function() {
    $("#tree").bind("loaded.jstree", function(event, data) {
        $("#tree").animate({width: "show", opacity: "show"}, "slow");
    }).jstree({
        "plugins" : ["html_data", "themes"]
    });

    $(".treeDir").click(function() {
        $("#tree").jstree("toggle_node", this);
    });

    // When the user clicks on a leaf item in the tree (representing a file)
    // we want to hide the filelist/the currently shown review and display the
    // correct review.
    $("#tree li.jstree-leaf a").click(function(event) {
        event.preventDefault();
        target = event.originalTarget.href;
        otherDone = false;
        $('.sidebar-container-right').remove();
        $('#cluetip').remove();
        $('#cluetip-waitimage').remove();

        $('#fileList').animate({opacity: 'hide'}, 'fast', function() {
        $('#reviewContainer').animate({opacity: 'hide'}, 'fast', function() {
            $('#loading').animate({opacity: 'show'}, 'slow');
            $('#reviewContainer').empty().load(target + ' #review', function() {
                $('#loading').animate({opacity: 'hide'}, 'fast', function() {
                    initReview();
                    $('#reviewContainer').animate({opacity: 'show'}, 'slow');
                });
            });
        })});
    });

    $("#treeToggle").click().toggle(function() {
        $("#tree").animate({width: "hide", opacity: "hide"}, "slow");
    }, function() {
        $("#tree").animate({width: "show", opacity: "show"}, "slow");
    });
});
