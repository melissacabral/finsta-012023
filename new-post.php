<?php 
require_once('config.php');
require_once('includes/functions.php');
//this page is only accessible to logged in users. 
if( ! check_login() ){
	//not logged in! send them to 404
	header('Location:404.php');
}
require('includes/header.php');
require('includes/parse-upload.php');
?>
<main class="content">
	<h2>New Post</h2>

	<?php show_feedback( $feedback, $errors, $feedback_class ); ?>
	<form method="post" action="new-post.php" enctype="multipart/form-data">
		<label class="dropimage">Upload a .jpg, .gif or .png image
			<input type="file" name="uploadedfile" accept="image/*" required>	
		</label>

		<input type="submit" value="Upload Image">
		<input type="hidden" name="did_upload" value="1">
	</form>
	
</main>

<!-- script from picnic.css -->

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() {
  [].forEach.call(document.querySelectorAll('.dropimage'), function(img){
    img.onchange = function(e){
      var inputfile = this, reader = new FileReader();
      reader.onloadend = function(){
        inputfile.style['background-image'] = 'url('+reader.result+')';
      }
      reader.readAsDataURL(e.target.files[0]);
    }
  });
});
</script>
<?php 
include('includes/sidebar.php'); 
include('includes/footer.php');