<?php
/*
 * By Pedram [ Pedroxam ]
*/

// Replace your server url and remote.php file
define('SERVER_API','http://example.com/remote.php');
?>

<!doctype html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	<title>Remote Upload File</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script src="./jquery.min.js"></script>
</head>
<body>
	<div id="upload">
		<h2>Upload Now !</h2>
		<form id="remote">
			<fieldset>
				<p><span class="fontawesome-link"></span><label for="link">Link</label></p>
				<p><input type="url" id="url" name="url" placeholder="http://" required autocomplete="off"></p>
				<p><input type="text" id="name" name="name" placeholder="filename" required autocomplete="off"></p>
				<p><input type="submit" value="Upload" name="send" class="Button"></p>
			</fieldset>
		</form>
		<div id="result">
			Progress: <span class="progress">--</span>
			<br/>
			Uploaded: <span class="uploaded">--</span>
			<br/>
			Filesize: <span class="filesize">--</span>
		</div>
	</div>
	
<script>

	// Global  Progress var
	var doProgress;
		
	function getProgress(){
		$.ajax({
			url: '<?php echo SERVER_API; ?>?progress=1',
			dataType: 'json',
			type: 'POST',
			data: {
				name: $('#name').val()
			},
			success:function(data){
				$('#result .progress').html(data.progress + ' %');
				$('#result .uploaded').html(data.uploaded);
				$('#result .filesize').html(data.size);
				
				if(data.progress >= 100){
				    clearInterval(doProgress); // Stop  Progress Var
				    $('#result').html('File Succesfully Uploaded!');
				}
			},
		})
	}

	$(document).ready(function(){
		$('#remote').submit(function(e){
			
		  	e.preventDefault();
			
			$.ajax({
				url: '<?php echo SERVER_API; ?>',
				dataType: 'json',
				type: 'POST',
				data: $(this).serialize(),
				beforeSend:function()
				{
					$('.Button').attr('disabled',true);
					doProgress = setInterval(getProgress, 2500);
				},
				success:function(){
					$('.Button').attr('disabled',false);
					
					if(data.status){
						$('#result').html('File Succesfully Uploaded.');
					}
					else {
						$('#result').html('Sorry, Please Check your server config.');
					}
				},
			})
		});
	});
	
</script>

</body>
</html>
