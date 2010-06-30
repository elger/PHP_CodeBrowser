$.History.bind(function (state) {
    $('.sidebar-container-right').remove();
    $('#cluetip').remove();
    $('#cluetip-waitimage').remove();

    if (state == '') {
        $('#reviewContainer').animate({opacity: 'hide'}, 'fast', function() {
            $('#fileList').animate({opacity: 'show'}, 'slow');
        });
    } else {
        // Go to specific review
        $('#fileList').animate({opacity: 'hide'}, 'fast', function() {
        $('#reviewContainer').animate({opacity: 'hide'}, 'fast', function() {
            $('#loading').animate({opacity: 'show'}, 'slow');
            $('#reviewContainer').empty().load(state + ' #review', function() {
                $('#loading').animate({opacity: 'hide'}, 'fast', function() {
                    initReview();
                    $('#reviewContainer').animate({opacity: 'show'}, 'slow');
                });
            });
        })});
    }
});

$(function() {
    $("#treeToggle").click().toggle(function() {
        $("#tree").animate({width: "hide", opacity: "hide"}, "slow");
        $("#treeToggle").css('background-image', "url('img/treeToggle-collapsed.png')");
    }, function() {
        $("#tree").animate({width: "show", opacity: "show"}, "slow");
        $("#treeToggle").css('background-image', "url('img/treeToggle-extended.png')");
    });

    $("#tree").bind("loaded.jstree", function(event, data) {
        $("#tree").animate({width: "show", opacity: "show"}, "slow");
    }).jstree({
        "plugins" : ["html_data", "themes"]
    });

    $(".treeDir").click(function() {
        $("#tree").jstree("toggle_node", this);
    });

    // When the user clicks on a leaf item in the tree (representing a file)
    // or an item in the fileList, want to hide the filelist/the currently
    // shown review and display the correct review.
    $(".fileLink").click(function(event) {
        event.preventDefault();
        target = event.originalTarget.href;
        $.History.go(target);
    });
});

