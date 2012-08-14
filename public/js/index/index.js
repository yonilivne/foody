function AJAXpageInit()
{
    var arrRecList = '$arrRecList';
    $('#recipesList a').each(function(){ $(this).attr('href', $(this).attr('href') + '&arrRecList=' + arrRecList + '&goBackTo=' + encodeURI(pgUrl)); });

    $('.more').unbind().die().live('click', function(e){
        $.getJSON($(this).attr('href') + '&pmode=empg&mode=json', function(msg){
            $('#recipesList').append(msg.recList);
            $('.more').attr('href', 'Recipes&pgnm=' + msg.nextPage);
            if (msg.hideNextPage == 'true') $('.more').hide();
        });
        e.preventDefault();
        e.stopPropagation();

    });
    if ('$hideNextPage' == 'true') $('.more').hide();
}
$(function(){
    $('.recipesListView').masonry({
        // options
        itemSelector : '.item',
        columnWidth : 0,
        isResizable : true,
        isFitWidth : true,
        isAnimated: false,
        animationOptions: {
            duration: 300,
            easing: 'linear',
            queue: false }
    });
});

