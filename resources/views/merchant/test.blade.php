<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
    <table border='1' cellspacing="0">
	    <tr>
	    	<input id='id' name='id'>
		    <td><input type='file' id='file' name='file'></td>
		    <td><button class='btn'>上传</button></td>
	    </tr>
	    <tr>
	    	<td colspan="2">
	    		<div class='preview'></div>
	    	</td>
    	</tr>
    </table>
    </body>
</html>
<script type='text/javascript' src='/merchant/js/jquery.min.js'></script>
<script type='text/javascript' src='/merchant/js/webuploader.min.js'></script>
<script type='text/javascript' src='/merchant/js/youyibao.js'></script>
<script type='text/javascript'>
	$(function(){
		youyibao.fileUpload($('file'), $('#id'), $('.preview'));




	});
</script>