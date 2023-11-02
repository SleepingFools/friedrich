var content;
if(typeof artContent !== 'undefined'){
    content = JSON.parse(artContent);
}

const editor = typeof artContent !== 'undefined' ? new EditorJS({
    holderId: 'editorjs',

    tools: {
        header: Header,
        delimiter: Delimiter,
        paragraph: {
            class: Paragraph,
            inlineToolbar: true,
        },
        embed: Embed,
        image: SimpleImage,
    },
    data: content
}) : new EditorJS({
    holderId: 'editorjs',

    tools: {
        header: Header,
        delimiter: Delimiter,
        paragraph: {
            class: Paragraph,
            inlineToolbar: true,
        },
        embed: Embed,
        image: SimpleImage,
    }
});


// https://stackoverflow.com/questions/5004233/jquery-ajax-post-example-with-php
var request;
$("#articleEditor").submit(function (event) {

    event.preventDefault();

    if (request) {
        request.abort();
    }

    editor.save().then((outputData) => {

        var $form = $(this);

        var $inputs = $form.find("input, select, button, textarea");
        
        var serializedData = $form.serialize();
        
        serializedData += "&content=" + encodeURIComponent(JSON.stringify(outputData));

        $inputs.prop("disabled", true);

        request = $.ajax({
            url: "edit.php",
            type: "post",
            data: serializedData
        });

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR) {
            // Log a message to the console
            $inputs.prop("disabled", false);
            if (response != "false") {
                var elementhtml = document.getElementById('IdOfArticle');
                elementhtml.value = response;
            }
        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    });


})