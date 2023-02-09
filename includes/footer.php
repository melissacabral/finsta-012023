		<footer class="footer">&copy; 2023 Finsta</footer>
	</div> 
<?php 
if( DEBUG_MODE ){
	include('includes/debug-output.php');
} ?>

<?php if($logged_in_user){ ?>
	<!-- Like Button Interaction -->
	<script type="text/javascript">
		//listen for clicks on any hearts
		document.body.addEventListener( 'click', function(e){
			if(e.target.className == 'heart-button'){
				
				likeUnlike( e.target );
			}
		} );

		async function likeUnlike( el ){
			let postId = el.dataset.postid;
			let userId = <?php echo $logged_in_user['user_id']; ?>;
			let container = el.closest('.likes');
			console.log(postId, userId);

			let formData = new FormData();
			formData.append('postId', postId);
			formData.append('userId', userId);

			let response = await fetch( "async-handlers/like-unlike.php", {
				method : 'POST',
				body : formData
			} );
			if(response.ok){
				let result = await response.text();
				container.innerHTML = result;
			}else{
				console.log(response.status);
			}
		}
	</script>
<?php } ?>
</body>
</html>