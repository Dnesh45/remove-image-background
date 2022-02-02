<form method="post" enctype="multipart/form-data" action="{{url('upload-image')}}">
    @csrf
	<input type="file" name="file"/>
	<input type="submit" name="submit"/>
</form>