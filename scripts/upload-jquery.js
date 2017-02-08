$(document).ready(function() {
	//Display the msg after upload
	$(".display-msg").fadeIn().delay(5000).fadeOut();
	//Progress bar
	$('#upload-form').submit(function() {

		$("#upload-button").click(function(e) {
			e.preventDefault();
			$('#upload-button').replaceWith('<input id="upload-button" type="submit" value="Upload">');
		});
		$('#progress').show();
	});

	//Choose-file size output
	$("#choose-file").change(function () {
		var iSize = ($("#choose-file")[0].files[0].size / 1024);
		if (iSize / 1024 > 1) {
			if (((iSize / 1024) / 1024) > 1) {
				iSize = (Math.round(((iSize / 1024) / 1024) * 100) / 100);
				$("#lblSize").html( iSize + "Gb");
			} else {
				iSize = (Math.round((iSize / 1024) * 100) / 100)
				$("#lblSize").html( iSize + "Mb");
			}
		} else {
			iSize = (Math.round(iSize * 100) / 100)
			$("#lblSize").html( iSize  + "kb");
		}
		/*if(iSize != false){$('#fsize').text('File Size is : ');}*/
	});
});
