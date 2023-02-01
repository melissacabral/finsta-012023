<?php 
require_once('config.php');
require_once('includes/functions.php');

//SEARCH CONFIGURATION
//how many posts per page?
$per_page = 20;

$phrase = '';

//sanitize the search phrase
if( isset($_GET['phrase']) ){
	$phrase = clean_string( $_GET['phrase'] );
}
require('includes/header.php');
?>
<main class="content">
	<div class="posts-container flex three four-600 five-900">
		<?php 
		//WRITE IT
		//get 10 published posts that match the phrase
		$query = 'SELECT * FROM posts
				WHERE is_published = 1
				AND
				( title LIKE :phrase OR body LIKE :phrase )
				ORDER BY date DESC';
		$result = $DB->prepare($query);
		//RUN IT
		$result->execute( array( 'phrase' => "%$phrase%" ) );
		$total = $result->rowCount();

		
		//always round up so that leftover posts get their own page
		$total_pages = ceil( $total / $per_page );
		//what page are we on? 
		$current_page = 1;
		//if there is a page set in the URL, use it!
		if( isset($_GET['page']) ){
			$current_page = filter_var( $_GET['page'], FILTER_SANITIZE_NUMBER_INT );
		}
		//validate the page number (if out of bounds, go back to page 1)
		if( $current_page < 1 OR $current_page > $total_pages ){
			$current_page = 1;
		}

		//calculate the offset for the LIMIT
		$offset = ( $current_page - 1 ) * $per_page;

		//write the query again, with the limit applied
		$query .= ' LIMIT :offset, :per_page';
		
		$result = $DB->prepare($query);
		//bind the params because LIMIT requires integers, not string
		$wildcard_phrase = "%$phrase%";

		$result->bindParam( 'phrase', $wildcard_phrase, PDO::PARAM_STR );
		$result->bindParam( 'offset', $offset, 			PDO::PARAM_INT );
		$result->bindParam( 'per_page', $per_page, 		PDO::PARAM_INT );
		
		//run it again
		$result->execute();
		?>

		<section class="full">
			<h2>Search Results for <?php echo $phrase; ?></h2>
			<h3><?php echo $total == 1 ? '1 post found' : "$total posts found"; ?></h3>
			<h3>Showing page <?php echo $current_page; ?> of <?php echo $total_pages; ?>.</h3>
		</section>

		<?php
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
						<?php show_post_image( $post['image'], 'small', $post['title'] ); ?>
					</a>
				</div>
				<footer>
					<h3 class="post-title clamp"><?php echo $post['title']; ?></h3>
					<p class="post-excerpt clamp"><?php echo $post['body']; ?></p>
					<div class="flex post-info">	
						<span class="comment-count">
							<?php count_comments($post['post_id']); ?></span>				
						<span class="date"><?php echo time_ago( $post['date'] ); ?></span>			
					</div>
				</footer>
			</div><!-- .card -->
		</article> <!-- .post -->


			<?php 
			} // end while

		$prev = $current_page - 1;
		$next = $current_page + 1;
		?>
		<section class="pagination full">
			<?php if( $current_page != 1 ){ ?>
			<a class="button" href="search.php?phrase=<?php echo $phrase; ?>&amp;page=<?php echo $prev; ?>" >
				&larr; PREVIOUS</a>
			<?php } ?>

			<?php if($current_page != $total_pages){ ?>
			<a class="button" href="search.php?phrase=<?php echo $phrase; ?>&amp;page=<?php echo $next; ?>">NEXT &rarr;</a>
			<?php } ?>
		</section>

		<?php 
		} //end if
		?>
	</div><!-- .posts-container -->
</main>
<?php 
include('includes/sidebar.php'); 
include('includes/footer.php');