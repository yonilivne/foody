$(function() {
    var i=0;
    $('.edit').live('click', function() {
        $(this).parents('li').addClass('editMode');
    });
    $('.commit').live('click', function() {
        $(this).parents('li').removeClass('editMode');
    });
    $('.discard').live('click', function() {
        $(this).parents('li').removeClass('editMode');
    });

    $('h3 a').tooltip({delay: {show: 600, hide: 100}});

    $('a#addHeader').click(function() {
        $('<li class="postHeader postObject container editMode">'
            +'<h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3>'
            +'<input type="text" placeholder="enter header">'
            +'</li>').appendTo('#mainColSortable');
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });

    $('a#addText').click(function() {
        $('<li class="postText postObject container editMode"><h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3><textarea class="expanding" placeholder="Enter text here..."></textarea></li>').appendTo('#mainColSortable');
        $(".expanding").expandingTextarea();
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });

    $('a#addImage').click(function() {
        i++;
        var element = $('<li class="postImage image-upload postObject container editMode"><h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3><div class="uploadedPhoto"><img src="pics/thumbs/9.jpg" /></div><div class="uploadBox image'+i+'">Drop an image file here <br />- or -<br /> Click to browse from your device</div></li>');
        element.appendTo('#mainColSortable');
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});

        upload(i);

    });
    $('a#addVideo').click(function() {
        $('<li class="postVideo postObject container editMode">'
            +'<h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3>'
            +'</li>').appendTo('#mainColSortable');
    });
    $('a#addSteps').click(function() {
        $('<li class="postSteps postObject container editMode"><h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3><ol id="stepListSortable" class="stepList"><li><div class="stepMain"><div class="order"></div><div class="thumb"><img width="200px" height="100px" src="images/nopic_step.png"></div><div class="txt"><textarea class="expanding" placeholder="Enter text here..."></textarea></div></div><div class="stepSide reorderListBtns"><a title="Move" class="reorder"><div></div></a><a class="close"><div></div></a></div></li></ol><div style="clear:both;"><a id="addStepItem" class="button btn addListItem left"><div class="icn"></div><div class="txt">Add</div></a></div></li>').appendTo('#mainColSortable');

        $( '#stepListSortable' ).sortable({
            items: 'li',
            placeholder: 'sortPlaceholder container',
            handle: 'a.reorder',
            scroll: true,
            scrollSpeed: 10,
            tolerance: 'pointer',
            connectWith: '.stepList'
        });
        $( '#stepListSortable' ).disableSelection();

        $('a#addStepItem').click(function() {
            $('<li><div class="stepMain"><div class="order"></div><div class="thumb"><img width="200px" height="100px" src="images/nopic_step.png"></div><div class="txt"><textarea class="expanding" placeholder="Enter text here..."></textarea></div></div><div class="stepSide reorderListBtns"><a title="Move" class="reorder"><div></div></a><a class="close"><div></div></a></div></li>').appendTo('.stepList');
            $(".expanding").expandingTextarea();
        });
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });
    $('a#addLink').click(function() {
        $('<li class="postLink postObject container editMode">'
            +'<h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3>'
            +'<p>Go to my blog post - <a href="http://www.youfedababychili.com/2011/03/11/home/" target="_blank">www.youfedababychili.com</a></p>'
            +'</li>').appendTo('#mainColSortable');
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });
    $('a#addIngredients').click(function() {
        $('<li class="postIngredients postObject container editMode"><h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3><ul id="ingListSortable" class="ingList"><li><div class="ingMain"><span class="ingQuant left"><input type="text" placeholder="#"></input></span><span class="ingUnit left"><input type="text" placeholder="units"></input></span><span class="ingName left"><input type="text" placeholder="ingredient"></input></span><span class="ingNote left"><input type="text" placeholder="note"></input></span></div><div class="ingSide reorderListBtns"><a title="Move" class="reorder"><div></div></a><a class="close"><div></div></a></div></li></ul><a id="addIngItem" class="button btn addListItem left"><div class="icn"></div><div class="txt">Add</div></a></li>').appendTo('#sideColSortable');

        $( '#ingListSortable' ).sortable({
            items: 'li',
            placeholder: 'sortPlaceholder container',
            handle: 'a.reorder',
            scroll: true,
            scrollSpeed: 10,
            tolerance: 'pointer',
            connectWith: '.ingList'
        });
        $( '#ingListSortable' ).disableSelection();

        $('a#addIngItem').click(function() {
            $('<li><div class="ingMain"><span class="ingQuant left"><input type="text" placeholder="#"></input></span><span class="ingUnit left"><input type="text" placeholder="units"></input></span><span class="ingName left"><input type="text" placeholder="ingredient"></input></span><span class="ingNote left"><input type="text" placeholder="note"></input></span></div><div class="ingSide reorderListBtns"><a title="Move" class="reorder"><div></div></a><a class="close"><div></div></a></div></li>').appendTo('.ingList');
        });
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });
    $('a#addLocation').click(function() {
        $('<li class="postLocation postObject container editMode">'
            +'<h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3>'
            +'<iframe width="100%" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/?ie=UTF8&amp;hq=&amp;hnear=&amp;t=m&amp;ll=48.855736,2.34189&amp;spn=0.012424,0.023947&amp;z=14&amp;output=embed"></iframe>'
            +'</li>').appendTo('#sideColSortable');
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });
    $('a#addTimes').click(function() {
        $('<li class="postTimes postObject container editMode"><h3><a title="Delete" class="close"><div></div></a><a title="Move" class="reorder"><div></div></a><div class="commitDiscard"><a title="Done" class="commit"><div></div></a><a title="Discard" class="discard"><div></div></a><a title="Edit" class="edit"><div></div></a></div></h3><table class="timesTable" cellspacing="0px"><tr class="headers"><td class="prepTime">Prep time</td><td class="cookTime">Cook time</td><td class="totalTime">Total</td></tr><tr class="digits"><td class="prepTime" title="Preparation time"><div><input type="text"></input><span>hr</span></div><div class="colon">:</div><div><input type="text"></input><span>min</span></div></td><td  class="cookTime" title="Cooking time"><div><input type="text"></input><span>hr</span></div><div class="colon">:</div><div><input type="text"></input><span>min</span></div></td><td class="totalTime" title="Total time"><div><input type="text"></input><span> hr</span></div><div class="colon">:</div><div><input type="text"></input><span> min</span></div></td></tr></table></li>').appendTo('#sideColSortable');
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });

    $('a#addSeparator').click(function() {
        $('<div class="seperatorItem"></div><div class="sepSide reorderListBtns"><a class="close"><div></div></a></div>'
            +'<div class="mainCol"><ul id="mainColSortable" class="colSortable"><div style="height:20px;"></div></ul></div><div class="sideCol"><ul id="sideColSortable" class="colSortable"><div style="height:20px;"></div></ul></div>').appendTo('.postContent');
        $( '.colSortable' ).sortable({
            items: 'li',
            placeholder: 'sortPlaceholder container',
            handle: 'a.reorder',
            scroll: true,
            scrollSpeed: 10,
            tolerance: 'pointer',
            connectWith: '.colSortable'
        });
        $('h3 a').tooltip({delay: {show: 600, hide: 100}});
    });

    $('a.close').live('click', function() {
        $(this).parent().parent().remove();
    });

    $(".expanding").expandingTextarea();


    $( '.colSortable' ).sortable({
        items: 'li',
        placeholder: 'sortPlaceholder container',
        handle: 'a.reorder',
        scroll: true,
        scrollSpeed: 10,
        tolerance: 'pointer',
        connectWith: '.colSortable'
    });

    $( '#ingListSortable' ).sortable({
        items: 'li',
        placeholder: 'sortPlaceholder container',
        handle: 'a.reorder',
        scroll: true,
        scrollSpeed: 10,
        tolerance: 'pointer',
        connectWith: '.ingList'
    });
    $( '#stepListSortable' ).sortable({
        items: 'li',
        placeholder: 'sortPlaceholder container',
        handle: 'a.reorder',
        scroll: true,
        scrollSpeed: 10,
        tolerance: 'pointer',
        connectWith: '.stepList'
    });
    $( '.colSortable, #ingListSortable, #stepListSortable' ).disableSelection();
    function upload(i){
        var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: $(".uploadBox.image"+i)[0],
            // path to server-side upload script
            action: 'post/upload/',
            allowedExtensions: ['jpg','jpeg','png'],
            onSubmit: function(id, fileName){

                $(this.element).parent().find("h3").append('<div class="progress progress-striped active">'+
                    '<div class="bar" style="width: 0;"></div>' +
                    '</div>');
            },
            onProgress: function(id, fileName, loaded, total){
                var per = loaded * 100 / total;
                $(".bar").css("width",per+"%");
            },
            onComplete: function(id, fileName, responseJSON){
                $(".progress").remove();
                $(this.element).parent().find("img").attr("src",responseJSON['file']);
                $(this.element).parent().find("img").data("name",responseJSON['filename']);
                $(this.element).parent().removeClass("editMode");
            },
            template:'<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area qq-upload-button">Drop an image file here <br>- or -<br> Click to browse from your device</div>' +
                '<ul class="qq-upload-list"></ul>' +
                '</div>'
        });
        return uploader;
    }
    upload(i);
    $("#post-btn").click(function(e){
        e.preventDefault();
        var data = {};
        data.title = $("#title").val();
        data.elements = [];
        function Element(){
            this.type;
            this.value;
            this.column;
        }
        $("#mainColSortable li.postObject").each(function(index,el){
            e = new Element();
            e.column = 0;
            if($(el).hasClass("postText")){
                e.type = "text";
                e.value = $(el).find("textarea").val();

            }
            if($(el).hasClass("postImage")){
                e.type = "image";
                e.value= $(el).find("img").data("name");
            }
            data.elements.push(e);
        });
        $("#sideColSortable li.postObject").each(function(index,el){
            e = new Element();
            e.column = 1;
            if($(el).hasClass("postText")){
                e.type = "text";
                e.value = $(el).find("textarea").val();

            }
            if($(el).hasClass("postImage")){
                e.type = "image";
                e.value= $(el).find("img").data("name");
            }
            data.elements.push(e);
        });
        $.post("post/save/",{data:data},function(){
            window.location.href = "index";
        });
    });
});