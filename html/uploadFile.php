 <script type="text/javascript">
    var formdata = false;
    var input = document.getElementById("input_files");
    if (window.FormData) {
        formdata = new FormData();
    }

	$(document).keydown(function(event) {
		if (event.keyCode == 27) { 
	        document.getElementById('closeModal').click();
	    }
	});

	$('.custom-file-input').on('change', function() { 
	   let fileName = $(this).val().split('\\').pop(); 
	   $(this).next('.custom-file-label').addClass("selected").html(fileName); 
	});
	
    $('#input_file').on('change',function(event){
        var i = 0, len = this.files.length, img, reader, file;
        //console.log('Number of files to upload: '+len);
        $('#result').html('');
        $('#input_file').prop('disabled',true);
             file = this.files[0];
            //console.log(file);
            if(!!file.name.match(/.*\.adi$/)){
                    if (formdata) {
                        formdata.append("files[]", file);
                    }
            } else {
                $('#input_file').val('').prop('disabled',false);
                alert(file.name+' is not an ADI');
            }
    });
    
    $('#btn_submit').on('click',function(event){
        if (formdata) {
               $("#loading_spinner").show();
            $.ajax({
                url: "/my/upload.php",
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false, // this is important!!!
                success: function (res) {
                    var result = JSON.parse(res);
                    $('#input_files').val('').prop('disabled',false);
                    if(result.res === true){
                        var buf=result.data[0];
                        $('#result').html('File uploaded: '+buf);
                    } else {
                        $('#result').html(result.data);
                    }
                    // reset formdata
                    formdata = false;
                    formdata = new FormData();
                }
            });
        }
        return false;
    });
</script>
			
