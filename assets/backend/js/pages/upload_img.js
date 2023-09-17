function chooseFile(){
    $("#thumb-file").click();
}
function changePhoto() {
    $("#thumb-file").click();
}
function uploadPhoto(){
    var fileInput = $("#thumb-file")[0];
    if(fileInput.files && fileInput.files[0]) {
        var fileName = fileInput.files[0].name;
        var fileExtension = fileName.split('.').pop().toLowerCase();
    if(fileExtension == 'jpg' || fileExtension == 'png' || fileExtension == 'jpeg' || fileExtension == 'gif'){
        $('.vertical-center').hide()
        const previewImg    = $('#upload-img');
        const reader        = new FileReader()
        reader.onload       = function(e) {
            previewImg.attr('src', e.target.result);
        };
        reader.readAsDataURL(fileInput.files[0]);
            $('#preview-img').show()
    } else {
        alert("Extension accept only JPG, PNG, JPEG and GIF");
    }
    }
}