<! doctype html>
<html>
<head>
    <title>上传</title>
</head>
<body>
<div>
    <form method="post" action="/upload" enctype="multipart/form-data">
        <p>
            <input type="file" name="file[]" multiple>
        </p>
        <p><label for="dir">上传目录：</label><input id="dir" name="dir" placeholder="默认 images"></p>
        <p><input type="submit" value="上传"></p>
    </form>
</div>
</body>
</html>