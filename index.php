<?php 
require_once('config.php');
require_once('includes/functions.php');
require('includes/header.php');
?>
<main class="content">
	<div class="posts-container flex one two-600 three-900">
		<?php 
		//WRITE IT
		//get the 20 most recent published posts
		$result = $DB->prepare('SELECT posts.*, categories.*, users.profile_pic, 
									users.username, users.user_id
								FROM posts, users, categories
								WHERE posts.is_published = 1
								AND posts.user_id = users.user_id 
								AND posts.category_id = categories.category_id
								ORDER BY posts.date DESC
								LIMIT 20');
		//RUN IT
		$result->execute();
		//CHECK IT - are there any rows in the result
		if( $result->rowCount() > 0 ){
			//LOOP IT
			while( $post = $result->fetch() ){
				//print_r($post);
		?>
		<article class="post">
			<div class="card">
				<div class="post-image-header">
					<a href="single.php?post_id=<?php echo $post['post_id']; ?>">
						<?php show_post_image( $post['image'], 'medium', $post['title'] ); ?>
					</a>
				</div>
				<footer>
					<div class="post-header flex two">
						<div class="user four-fifth flex">
							<img src="<?php echo $post['profile_pic']; ?>">
							<span><?php echo $post['username']; ?></span>
						</div>
						<div class="likes fifth">
							<?php 
							if($logged_in_user){
								$user_id = $logged_in_user['user_id'];
							}else{
								$user_id = 0;
							}
							like_interface( $post['post_id'], $user_id ); ?>
						</div>
					</div><!-- .post-header -->

					<h3 class="post-title clamp"><?php echo $post['title']; ?></h3>
					<p class="post-excerpt clamp"><?php echo $post['body']; ?></p>
					<div class="flex post-info">	
						<span class="category"><?php echo $post['name']; ?></span>
						<span class="comment-count">
							<?php count_comments( $post['post_id'] ); ?></span>				
						<span class="date"><?php echo time_ago( $post['date'] ); ?></span>			
					</div>
				</footer>
			</div><!-- .card -->
		</article> <!-- .post -->
		<?php 
			} // end while
		}else{
			//empty state
			echo '<h2>No Posts Found.</h2>';
		} //end of posts query
		?>

	</div><!-- .posts-container -->
</main>
<?php 
include('includes/sidebar.php'); 
include('includes/footer.php');