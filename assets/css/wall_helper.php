<?php

if( ! function_exists('post_paint')){
	function post_paint($query){
		$ci = & get_instance();
		$ci->load->helper('wallutilerias');
			foreach ($query->result() as $row) {

				if( $row->userid != $row->posted_by ){
					$ci->db->select("Nombre, Appaterno, Apmaterno, idC006Alumno, Sexo");
					$result = $ci->db->get_where('C006Alumnos', array('idC006Alumno' => $row->posted_by), 1);
					if($result->num_rows() > 0){
						$row2 = $result->result();
						//print_r($row2);
						$row->idC006Alumno = $row2[0]->idC006Alumno;
						$row->sexo = $row2[0]->Sexo;
						//$nombre = ucwords(strtolower($row2[0]->Nombre) .' '.strtolower($row2[0]->Appaterno).' '.strtolower($row2[0]->Apmaterno));
						$nombre = nombreUsuario($row2[0]->idC006Alumno);
					}
				}else{
					$nombre = nombreUsuario($row->idC006Alumno);
					//ucwords(strtolower($row->Nombre) .' '.strtolower($row->Appaterno).' '.strtolower($row->Apmaterno));
				}
				$hace = time_elapsed_time($row->TimeSpent, $row->date_created);



				switch ($row->post_type) {
					case '0':
					//url
					//case '3':
						# code...
					$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
						a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo, a.c_id
					 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
						ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
					$comments = $query->result();
					post_single($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
						break;
					case '2':

					$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
						a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo, a.c_id
					 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
						ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id);
					$comments = $query->result();

						$arrayImagenes = explode('|', $row->cur_image) ;
						post_image($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $arrayImagenes, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
						break;
					case '4':	

						$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
						a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo, a.c_id
					 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
						ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id);
					$comments = $query->result();

					//$repost = $ci->db->get_where('Facebook_Posts', array('post_source' => $row->post_source), 1);
					
								$repost = $ci->db->query("SELECT post.p_id, alum.Nombre, alum.Appaterno, alum.Apmaterno, post.post, post.url,
				UNIX_TIMESTAMP() - post.date_created AS TimeSpent, post.date_created, alum.Foto, post.post_type, post.cur_image, post.title, post.description,
				post.favorite, alum.idC006Alumno, alum.sexo, post.post_source
				FROM facebook_posts AS post
			 INNER JOIN C006Alumnos AS alum ON post.userid= alum.idC006Alumno AND post.p_id = ". $row->post_source . " LIMIT 1");
			 

					
					//ya lo encontro solo falta pintarlo

						post_repost($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo, $repost);
					 break;

					 //url
					 case '3':

					 	$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
						a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo, a.c_id
					 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
						ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
					$comments = $query->result();
					post_link($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo,  $row->title, $row->description, $row->cur_image);
						

					 break;
					default:
						# code...
						break;
				}

				
			}
	}
}

if ( ! function_exists('post_all') ):
		function post_all($idAlumno){

			$ci =& get_instance();

			$query = $ci->db->query("SELECT post.p_id, alum.Nombre, alum.Appaterno, alum.Apmaterno, post.post, post.url,
				UNIX_TIMESTAMP() - post.date_created AS TimeSpent, post.date_created, alum.Foto, post.post_type, post.cur_image, 
				post.favorite, alum.idC006Alumno, alum.sexo, post.post_source, post.userid, post.posted_by, post.description, post.title
				FROM facebook_posts AS post
			 INNER JOIN C006Alumnos AS alum ON post.userid= alum.idC006Alumno AND post.userid = $idAlumno ORDER BY post.date_created DESC LIMIT 5");

			if($query->num_rows() > 0) :
				post_paint($query);

			// foreach ($query->result() as $row) {

			// 	if( $row->userid != $row->posted_by ){
			// 		$ci->db->select("Nombre, Appaterno, Apmaterno, idC006Alumno, Sexo");
			// 		$result = $ci->db->get_where('C006Alumnos', array('idC006Alumno' => $row->posted_by), 1);
			// 		if($result->num_rows() > 0){
			// 			$row2 = $result->result();
			// 			//print_r($row2);
			// 			$row->idC006Alumno = $row2[0]->idC006Alumno;
			// 			$row->sexo = $row2[0]->Sexo;
			// 			//$nombre = ucwords(strtolower($row2[0]->Nombre) .' '.strtolower($row2[0]->Appaterno).' '.strtolower($row2[0]->Apmaterno));
			// 			$nombre = nombreUsuario($row2[0]->idC006Alumno);
			// 		}
			// 	}else{
			// 		$nombre = nombreUsuario($row->idC006Alumno);
			// 		//ucwords(strtolower($row->Nombre) .' '.strtolower($row->Appaterno).' '.strtolower($row->Apmaterno));
			// 	}
			// 	$hace = time_elapsed_time($row->TimeSpent, $row->date_created);



			// 	switch ($row->post_type) {
			// 		case '0':
			// 		//url
			// 		//case '3':
			// 			# code...
			// 		$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
			// 		$comments = $query->result();
			// 		post_single($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
			// 			break;
			// 		case '2':

			// 		$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id);
			// 		$comments = $query->result();

			// 			$arrayImagenes = explode('|', $row->cur_image) ;
			// 			post_image($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $arrayImagenes, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
			// 			break;
			// 		case '4':	

			// 			$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id);
			// 		$comments = $query->result();

			// 		//$repost = $ci->db->get_where('Facebook_Posts', array('post_source' => $row->post_source), 1);
					
			// 					$repost = $ci->db->query("SELECT post.p_id, alum.Nombre, alum.Appaterno, alum.Apmaterno, post.post, post.url,
			// 	UNIX_TIMESTAMP() - post.date_created AS TimeSpent, post.date_created, alum.Foto, post.post_type, post.cur_image, post.title, post.description,
			// 	post.favorite, alum.idC006Alumno, alum.sexo, post.post_source
			// 	FROM facebook_posts AS post
			//  INNER JOIN C006Alumnos AS alum ON post.userid= alum.idC006Alumno AND post.p_id = ". $row->post_source . " LIMIT 1");
			 

					
			// 		//ya lo encontro solo falta pintarlo

			// 			post_repost($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo, $repost);
			// 		 break;

			// 		 //url
			// 		 case '3':

			// 		 	$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
			// 		$comments = $query->result();
			// 		post_link($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo,  $row->title, $row->description, $row->cur_image);
						

			// 		 break;
			// 		default:
			// 			# code...
			// 			break;
			// 	}

				
			// }

			else:
				?>
			<div class="post">
				<div class="span12">
					<div class="alert">
					Actualmente no ha realizado ningun publicación
					</div>
				</div>
			</div>
				<?php
			endif;
?>

<?php
	
}
endif;

if ( ! function_exists('post_scroll') ):
		function post_scroll($idAlumno, $idPost){

			$ci =& get_instance();

			$query = $ci->db->query("SELECT post.p_id, alum.Nombre, alum.Appaterno, alum.Apmaterno, post.post, post.url,
				UNIX_TIMESTAMP() - post.date_created AS TimeSpent, post.date_created, alum.Foto, post.post_type, 
				post.cur_image, post.favorite, alum.idC006Alumno, alum.sexo, post.post_source, post.userid, post.posted_by,
				post.title, post.description
				FROM facebook_posts AS post
			 INNER JOIN C006Alumnos AS alum ON post.userid= alum.idC006Alumno AND post.userid = $idAlumno 
			 AND  post.p_id < $idPost
			 ORDER BY post.date_created DESC LIMIT 5");

			if($query->num_rows() > 0){
				post_paint($query);
			// foreach ($query->result() as $row) {
			// 	//$nombre = ucwords(strtolower($row->Nombre) .' '.strtolower($row->Appaterno).' '.strtolower($row->Apmaterno));

			// 	if( $row->userid != $row->posted_by ){
			// 		$ci->db->select("Nombre, Appaterno, Apmaterno, idC006Alumno, Sexo");
			// 		$result = $ci->db->get_where('C006Alumnos', array('idC006Alumno' => $row->posted_by), 1);
			// 		if($result->num_rows() > 0){
			// 			$row2 = $result->result();
			// 			//print_r($row2);
			// 			$row->idC006Alumno = $row2[0]->idC006Alumno;
			// 			$row->sexo = $row2[0]->Sexo;
			// 			$nombre = ucwords(strtolower($row2[0]->Nombre) .' '.strtolower($row2[0]->Appaterno).' '.strtolower($row2[0]->Apmaterno));
			// 		}
			// 	}else{
			// 		$nombre = ucwords(strtolower($row->Nombre) .' '.strtolower($row->Appaterno).' '.strtolower($row->Apmaterno));
			// 	}

			// 	$hace = time_elapsed_time($row->TimeSpent, $row->date_created);



			// 	switch ($row->post_type) {
			// 		case '0':
			// 		//url
	
						
			// 		$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );

			// 		$comments = $query->result();
			// 		post_single($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
			// 			break;
			// 		case '2':

			// 		$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		 FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
			// 		$comments = $query->result();

			// 			$arrayImagenes = explode('|', $row->cur_image) ;
			// 			post_image($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $arrayImagenes, $row->favorite, $comments, $row->idC006Alumno, $row->sexo);
			// 			break;
			// 		case '3':

			// 			$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
			// 			a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo
			// 		    FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
			// 			ON a.userid = b.idC006Alumno WHERE  a.post_id =". $row->p_id );
			// 		    $comments = $query->result();
			// 		    post_link($nombre, $row->post, $row->url, $hace, $row->Foto, $row->p_id, $row->favorite, $comments, $row->idC006Alumno, $row->sexo,  $row->title, $row->description, $row->cur_image);


			// 		break;
			// 		default:
			// 			# code...
			// 			break;
			// 	}

				
			// } // fin del for
    }
?>

<?php
	
}
endif;

if ( ! function_exists('post_single'))
{
	
	function post_single($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo){
?>
	<div class="post">
					
					<div class="span12">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" !-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo);?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_text.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>

										<?php if(!empty($url)): ?>
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<span class="tiempo_wall">  <?php echo $hace; ?> |

										<strong class="reply_wall"><a href="reply" id="<?php echo $idPost; ?>" class="link_muro">Responder</a></strong> | 
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										<strong class="reply_wall"><a href="favorite" id="<?php echo $idPost; ?>" class="link_muro">Favorito</a></strong> 
										</span> 
							 </div>
							 <?php if($favorite == 1): ?>	
							 <div id="div_favorite">
								 <div style="float:left;">
								 	<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/favorito.png"/>
								 </div>
							 </div>
							<?php endif; ?>

					</div>
					<?php pinta_comentario($comments); ?>
					
					
</div> <!-- fin de post !-->

<div style="clear:both;" > </div>

<hr />

<?
	}

}

if( ! function_exists('pinta_comentario')){
	function pinta_comentario($comments){
	?>
		<?php if( count($comments) > 0 ): ?>
					<?php foreach($comments as $row): ?>
					<?php $nombre = ucwords(strtolower($row->Nombre) .' '.strtolower($row->Appaterno).' '.strtolower($row->Apmaterno)); ?>
					<?php  $hace = time_elapsed_time($row->TimeSpent, $row->date_created); ?>
					<!--div class="span12"> <!-- post reply !-->
					<div class="span12"> <!-- post reply !-->
						<ul class="thumbnails">
								<li class="span2">
						 		</li>
						 		<li class="span1" style="margin-top:8px;margin-right:0px;">
						 			<div >
						 				<a href="#">
						 					<!--img src="<?php //echo base_url()?>theme/comunidad/bootstrap/img/post_comment_reply.png" alt="" !-->
						 					<img src="<?php echo imagen_perfil($row->idC006Alumno, $row->Sexo); ?>" width="27" height="27" />
						 				</a>
						 			</div>
						 		</li>
								<li class="span7" style="margin-top:5px;margin-left:0px;">
									<h3 class="titulo-post"> <?php echo $nombre ?></h3>
									<p> <?php echo $row->comments; ?></p>
									<span class="tiempo_wall"> <?php echo $hace; ?> | 
									<strong class="reply_wall"><a href="reply" id="<?php echo $row->c_id; ?>" data-type="comment" class="link_muro">Responder</a></strong> | 
									<strong class="reply_wall"> <a href="share" class="link_muro">Compartir</a></strong> </span> 
								</li>

								<?php $ci = & get_instance(); ?>
								<?php 
								//$comments_comments = $ci->db->get_where('facebook_posts_comments', array('comment_id' => $row->c_id)); 


								$query = $ci->db->query("SELECT UNIX_TIMESTAMP() - a.date_created AS TimeSpent, 
								a.date_created, a.comments, b.Nombre, b.Appaterno, b.Apmaterno, b.idC006Alumno, b.Sexo, a.c_id
							    FROM facebook_posts_comments as a INNER JOIN C006Alumnos as b 
								ON a.userid = b.idC006Alumno WHERE  a.comment_id =". $row->c_id );
							    $comments_comments = $query->result();

								?>

								<?php foreach ($comments_comments as $row_c) {
									 $nombre2 = ucwords(strtolower($row_c->Nombre) .' '.strtolower($row_c->Appaterno).' '.
									strtolower($row_c->Apmaterno));
									 $hace2 = time_elapsed_time($row_c->TimeSpent, $row_c->date_created); 
									?>
										<div class="span12"> <!-- post reply !-->
											<ul class="thumbnails">
													<li class="span3">
											 		</li>
											 		<li class="span1" style="margin-top:8px;margin-right:0px;">
											 			<div >
											 				<a href="#">
											 					<!--img src="<?php //echo base_url()?>theme/comunidad/bootstrap/img/post_comment_reply.png" alt="" !-->
											 					<img src="<?php echo imagen_perfil($row_c->idC006Alumno, $row_c->Sexo); ?>" width="27" height="27" />
											 				</a>
											 			</div>
											 		</li>
													<li class="span7" style="margin-top:5px;margin-left:0px;">
														<h3 class="titulo-post"> <?php echo $nombre2; ?></h3>
														<p> <?php echo $row_c->comments; ?></p>
														<span class="tiempo_wall"> <?php echo $hace2; ?> 
													</li>

			
											</ul>	
										</div>
									<?php
								} ?>
						</ul>	
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
<?php
	}
}

if ( ! function_exists('post_link'))
{
	
	function post_link($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo, $title, $description, $image){
?>
	<div class="post">
					
					<div class="span12">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" !-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo);?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_text.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>

										<?php if(!empty($url)): ?>
											
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<div class="video_share">
										    <div class="image_user_share">
										        <img src="<?php echo $image; ?>" width="100">
										    </div>
										    <div class="text_web_share">
										           <div class="title_url"><strong><?php echo $title; ?></strong></div>
										        <div class="description_url"><?php echo $description; ?></div>
										    </div>
										     
										</div>
										<div style="clear:both;"></div>	

										<span class="tiempo_wall">  <?php echo $hace; ?> |

										<strong class="reply_wall"><a href="reply" id="<?php echo $idPost; ?>" class="link_muro">Responder</a></strong> | 
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										<strong class="reply_wall"><a href="favorite" id="<?php echo $idPost; ?>" class="link_muro">Favorito</a></strong> 
										</span> 
							 </div>
							 <?php if($favorite == 1): ?>	
							 <div id="div_favorite">
								 <div style="float:left;">
								 	<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/favorito.png"/>
								 </div>
							 </div>
							<?php endif; ?>

					</div>
					<?php pinta_comentario($comments); ?>
					
					
</div> <!-- fin de post !-->

<div style="clear:both;" > </div>

<hr />

<?
	}

}

if ( ! function_exists('post_link_repost'))
{
	
	function post_link_repost($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo, $title, $description, $image){
?>
	<div class="post">
					
					<div class="span11 offset1">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" !-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo);?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_text.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content_repost">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>

										<?php if(!empty($url)): ?>
											
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<div class="video_share">
										    <div class="image_user_share">
										        <img src="<?php echo $image; ?>" width="100">
										    </div>
										    <div class="text_web_share_repost">
										           <div class="title_url"><strong><?php echo $title; ?></strong></div>
										        <div class="description_url"><?php echo $description; ?></div>
										    </div>
										     
										</div>
										<div style="clear:both;"></div>	

										<span class="tiempo_wall">  <?php echo $hace; ?> |

										
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										
										</span> 
							 </div>
							 <?php if($favorite == 1): ?>	
							 <div id="div_favorite">
								 <div style="float:left;">
								 	<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/favorito.png"/>
								 </div>
							 </div>
							<?php endif; ?>

					</div>
					
					
</div> <!-- fin de post !-->

<div style="clear:both;" > </div>

<hr />

<?
	}

}


if ( ! function_exists('post_single_repost'))
{
	
	function post_single_repost($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo){
?>
	<div class="post">
					
					<div class="span11 offset1">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php //echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" !-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo);?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_text.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content_repost">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>

										<?php if(!empty($url)): ?>
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<span class="tiempo_wall">  <?php echo $hace; ?> |

								
										<strong class="reply_wall">
										<a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
								
										</span> 
							 </div>
					</div>
					
					
</div> <!-- fin de post !-->

<div style="clear:both;" > </div>

<hr />

<?
	}

}


if ( ! function_exists('post_repost'))
{
	
	function post_repost($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo, $repost){
?>
	<div class="post">
					
					<div class="span12">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" !-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo);?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_text.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>

										<?php if(!empty($url)): ?>
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<span class="tiempo_wall">  <?php echo $hace; ?> |

										<strong class="reply_wall"><a href="reply" id="<?php echo $idPost; ?>" class="link_muro">Responder</a></strong> | 
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										<strong class="reply_wall"><a href="favorite" id="<?php echo $idPost; ?>" class="link_muro">Favorito</a></strong> 
										</span> 
							 </div>
							 <?php if($favorite == 1): ?>	
							 <div id="div_favorite">
								 <div style="float:left;">
								 	<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/favorito.png"/>
								 </div>
							 </div>
							<?php endif; ?>

					</div>
					<div style="clear:both;" > </div>
					<hr />
					<?php //print_r($repost); ?>
					<?php
					
					if($repost->num_rows() > 0){
						$row = $repost->result();
						//print_r($row);
						$hace = time_elapsed_time($row[0]->TimeSpent, $row[0]->date_created);
						switch($row[0]->post_type)
						 {
						 	case '5':

						 	$nombre = ucwords(strtolower($row[0]->Nombre) .' '.strtolower($row[0]->Appaterno).' '.strtolower($row[0]->Apmaterno));
						 	//echo "texto";
						 	//post_single_repost($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo)
						 	post_single_repost($nombre, $row[0]->post, $row[0]->url, $hace, $row[0]->Foto, $row[0]->p_id, $row[0]->favorite, array(), $row[0]->idC006Alumno, $row[0]->sexo);
						 	break;
						 	case '6':
						 	//echo "imagenes";
						 	$nombre = ucwords(strtolower($row[0]->Nombre) .' '.strtolower($row[0]->Appaterno).' '.strtolower($row[0]->Apmaterno));
						 	$arrayImagenes = explode('|', $row[0]->cur_image) ;
							post_image_repost($nombre, $row[0]->post, $row[0]->url, $hace, $row[0]->Foto, $row[0]->p_id, $arrayImagenes, $row[0]->favorite, array(), $row[0]->idC006Alumno, $row[0]->sexo);
						 	break;
						 	case '7':
						 	
						 	//echo "url";
						 	$nombre = ucwords(strtolower($row[0]->Nombre) .' '.strtolower($row[0]->Appaterno).' '.strtolower($row[0]->Apmaterno));
						 	//echo "texto";
						 	//post_single_repost($nombre, $post, $url, $hace, $foto, $idPost, $favorite, $comments, $idAlumno, $sexo)
						 	//post_single_repost($nombre, $row[0]->post, $row[0]->url, $hace, $row[0]->Foto, $row[0]->p_id, $row[0]->favorite, array(), $row[0]->idC006Alumno, $row[0]->sexo);
						 	post_link_repost($nombre, $row[0]->post, $row[0]->url, $hace, $row[0]->Foto, $row[0]->p_id, $row[0]->favorite, $comments, $row[0]->idC006Alumno, $row[0]->sexo,  $row[0]->title, $row[0]->description, $row[0]->cur_image);
	
						 	break;
						 }					
					} 
					
					 ?> 
					<?php pinta_comentario($comments); ?>
					
					
</div> <!-- fin de post !-->

<div style="clear:both;" > </div>



<?
	}

}


if ( ! function_exists('post_image'))
{
	
	function post_image($nombre, $post, $url, $hace, $foto, $idPost, $imagenes, $favorite, $comments, $idAlumno, $sexo){
?>
	<div class="post"> <!-- post de imagen -->
					<div class="span12">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" /!-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo); ?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_camera.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>


										<?php if(!empty($url)): ?>
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<?php
										pinta_imagenes($idPost,$imagenes);                                                                                
										?>
											
										<span class="tiempo_wall">  <?php echo $hace; ?> |

										<strong class="reply_wall"><a href="reply" id="<?php echo $idPost; ?>" class="link_muro">Responder</a></strong> | 
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										<strong class="reply_wall"><a href="favorite" id="<?php echo $idPost; ?>" class="link_muro">Favorito</a></strong>  
										</span> 
							 </div>

							 <?php if($favorite == 1): ?>	
							 <div id="div_favorite">
								 <div style="float:left;">
								 	<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/favorito.png"/>
								 </div>
							 </div>
							<?php endif; ?>

					</div>
					<?php pinta_comentario($comments); ?>
				</div> <!-- fin de post !-->



<div style="clear:both;" > </div>

<hr />

<?
	}

}

if ( ! function_exists('post_image_repost'))
{
	
	function post_image_repost($nombre, $post, $url, $hace, $foto, $idPost, $imagenes, $favorite, $comments, $idAlumno, $sexo){
?>
	<div class="post"> <!-- post de imagen -->
					<div class="span11 offset1">

						<input type="hidden" id="idPost" value="<?php echo $idPost; ?>" />

							<div style="float:left;">
								<!--img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_img.png" class="img-polaroid" /!-->
								<img src="<?php echo imagen_perfil($idAlumno, $sexo); ?>" width="48" height="48" />
								<img src="<?php echo base_url()?>theme/comunidad/bootstrap/img/post_camera.png" style="margin:-30px 2px 6px 5px;" />
							</div>

							<div class="post_content_repost">
										<h3 class="titulo-post"> <?php echo $nombre; ?> </h3>
										<p> <?php echo $post; ?> </p>


										<?php if(!empty($url)): ?>
											<a  href="<?php echo $url; ?>" class="link_muro"><?php echo $url; ?></a>
										<?php endif; ?>

										<?php
										
										pinta_imagenes($idPost,$imagenes);
										?>
											
										<span class="tiempo_wall">  <?php echo $hace; ?> |

										<strong class="reply_wall"><a href="reply" id="<?php echo $idPost; ?>" class="link_muro">Responder</a></strong> | 
										<strong class="reply_wall"><a href="share" id="<?php echo $idPost; ?>" class="link_muro">Compartir</a></strong> |
										<strong class="reply_wall"><a href="favorite" id="<?php echo $idPost; ?>" class="link_muro">Favorito</a></strong>  
										</span> 
							 </div>

			
					</div>
			
				</div> <!-- fin de post !-->



<div style="clear:both;" > </div>

<hr />

<?
	}

}


if ( ! function_exists('row_image'))
{
	function row_image($idPost,$rows, $pic1, $pic2, $pic3){
		$ruta = $ci =& get_instance();
		$path = $ruta->config->item('photos_base_wall_url');
	switch ($rows) {
		case '1':
			# code...
			?>
			<ul class="thumbnails">
				<li class="span12">
					<a href="<?php echo $path.$pic1; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic1; ?>" alt="">
					</a>
				</li>	
			 </ul>
			<?php
			break;
		case '2':
			?>
			<ul class="thumbnails">
				<li class="span6">
					<!--<a id="lightbox" class="thumbnail" data-toggle="lightbox" href="#demoLightbox"!-->
					<a href="<?php echo $path.$pic1; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic1; ?>" alt="">
					</a>
				</li>	
				<li class="span6">
					<a href="<?php echo $path.$pic2; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic2; ?>" alt="">
					</a>
				</li>	
			 </ul>
			<?php
			break;
		case '3':
		?>
			
			<ul class="thumbnails">
				<li class="span4">
					<a href="<?php echo $path.$pic1; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic1; ?>" alt="">
					</a>
					</li>	
					<li class="span4">
						<a href="<?php echo $path.$pic2; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic2; ?>" alt="">
					</a>
					</li>	

					<li class="span4">
						<a href="<?php echo $path.$pic3; ?>" class="thumbnail fresco" data-fresco-group="post<?php echo $idPost;?>" >
						<img src="<?php echo $path.$pic3; ?>" alt="">
						</a>
				</li>
			 </ul>

		<?
		break;
		
		default:
			# code...
			break;
	}
?>



<?php
}

}

if ( ! function_exists('deleteFirst')){

		function deleteFirst($num, $array){
			$i = 0;
			while($i < $num){
				array_shift($array);
				$i++;
			}	
			return $array;
		}
}

if ( ! function_exists('publish_post'))
{
	
	function publish_post($type, $texto, $userid, $posted_on, $title = "", $description = "", $image = ""){
$ci =& get_instance();
$ci->load->helper('avatar');

$id = 0;
//print_r($ci);
		switch ($type) {
			//texto
			case '0':

				$user_id = $userid; //$_REQUEST['x']; 
				$posted_on = $posted_on;
				//$val = $ci->avatar_helper->checkValues($texto);
				$val = sanear_string($texto);
				$ptype = 0;
				
				$ci->db->query("INSERT INTO facebook_posts (post,userid,date_created,posted_by,post_type) 
					VALUES('".$val."','".$userid."','".strtotime(date("Y-m-d H:i:s"))."','".$posted_on."',
						'".$ptype."')");

				$id = $ci->db->insert_id();
				//$id = 123;	
				# code...
				break;
				//image
			case '2':
				   // $user_id = $idAlumnoLogin; //$_REQUEST['x']; 
					//$posted_on = $idAlumnoPost;
				$user_id = $userid; //$_REQUEST['x']; 
				$posted_on = $posted_on;

					$val = sanear_string($texto);
					$ptype = 2;

					$array = $ci->session->userdata('imagenes');
					$stringImages = null;
					foreach ($array as $key) {
					 	$stringImages .= $key['file'] . '|';
					}
					$stringImages2 = substr($stringImages, 0, strlen($stringImages)-1);
					//echo "<h1>:stringImages".$stringImages .":". $stringImages2 . "</h1>";
					
					$ci->db->query("INSERT INTO facebook_posts (post,userid,date_created,posted_by, cur_image, post_type) 
						VALUES('".$val."',
							'".$user_id."',
							'".strtotime(date("Y-m-d H:i:s"))."',
							'".$posted_on."',
							'".$stringImages2."',
							'".$ptype."')");

					$id = $ci->db->insert_id();
				break;
				//url
				case '3':

					//$user_id = $idAlumnoLogin; //$_REQUEST['x']; 
					//$posted_on = $idAlumnoPost;
				$user_id = $userid; //$_REQUEST['x']; 
				$posted_on = $posted_on;

					$val = sanear_string($texto);
					$ptype = 3;
					//extraigo el link
					 $link = extract_link($val);
					 $val = str_replace($link, "", $val);
					$ci->db->query("INSERT INTO facebook_posts (post,userid,date_created,posted_by, post_type, url, title, description, cur_image) 
						VALUES('".$val."',
							'".$user_id."',
							'".strtotime(date("Y-m-d H:i:s"))."',
							'".$posted_on."',
							'".$ptype."',
							'".$link."',
							'".$title."',
							'".$description."',
							'".$image."')");

					$id = $ci->db->insert_id();

				break;			
				default:
				# code...
				break;
		}

		return $id;

	}

}

if( ! function_exists('publish_post_shared')){
	function publish_post_shared($idPost, $content, $user_id){
		$ci = & get_instance();

		$result = $ci->db->get_where('facebook_posts', array('p_id' => $idPost), 1);

		if($result->num_rows() > 0){
			$result->row(1)->p_id;


			$tipo_post_repost = 0;
			switch ($result->row(1)->post_type) {
				//texto y url
				case '0':
				//texto(deberia ser 1) = 5 repost single
					$tipo_post_repost = 5;
				case '3':
				//equivalente de texto o url en re_post
					//$tipo_post_repost = 5;
				//link = link repost
				$tipo_post_repost = 7;
					# code...
					break;
				//imagen
				case '2':
				//imagen = imagen repost
				$tipo_post_repost = 6;

				default:
					# code...
					break;
			}
			

			$ci->db->query("INSERT INTO facebook_posts (post, type, date_created, userid, posted_by, likes, 
				favorite, media, uip, title, description, url, cur_image, post_type) 
			  SELECT a.post, a.type, a.date_created, a.userid, a.posted_by, a.likes, a.favorite, a.media, a.uip, a.title, a.description, a.url, a.cur_image, $tipo_post_repost  
				 FROM facebook_posts as a WHERE a.p_id =" . $idPost);

			$id_repost = $ci->db->insert_id();
			$type_repost = 4;
				$ci->db->query("INSERT INTO facebook_posts (post,userid,date_created,posted_by,post_type, post_source) 
					VALUES('".$content."','".$user_id."','".strtotime(date("Y-m-d H:i:s"))."','".$user_id."',
						'".$type_repost."','". $id_repost ."')");

			return $ci->db->insert_id();


		}


		
	}
}

if (!function_exists("time_elapsed_time")){
	
	function time_elapsed_time($restante, $fecha){
		
				$hace = "desconocido";
				$days = floor($restante / (60 * 60 * 24)); 
				$remainder = $restante % (60 * 60 * 24);
				$hours = floor($remainder / (60 * 60));
				$remainder = $remainder % (60 * 60);
				$minutes = floor($remainder / 60);
				$seconds = $remainder % 60;
                if($days > 0) {
					//$oldLocale = setlocale(LC_TIME, 'pt_BR');
					$fecha = strftime('%b %e %Y', $fecha); 
					$hace = $fecha;
					// setlocale(LC_TIME, $oldLocale);
				}elseif($days == 0 && $hours == 0 && $minutes == 0)
					$hace = 'Hace unos momentos';
				elseif($hours==1)
					$hace = 'Hace'.' '.$hours.' '.'hora';
				elseif($hours>1)
					$hace = 'Hace'.' '.$hours.' '.'horas';
				elseif($days == 0 && $hours == 0 && $minutes ==1)
					$hace = 'Hace'.' '.$minutes.' '.'minuto';
				elseif($days == 0 && $hours == 0)
					$hace = 'Hace'.' '.$minutes.' '.'minutos';
				else
					$hace = 'Hace unos momentos';
					
				
				return $hace;
		
	}
}

if(!function_exists('extract_link')){

	function extract_link($text){
		$text = html_entity_decode($text);
	 	$text = " ".$text;
	    preg_match_all('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', $text, $coincidencias, PREG_SET_ORDER);
	
		 if(count($coincidencias) > 0){
			$primeraOcurrencia = $coincidencias[0][0];
		 }
		 //posiscion de la 1era ocurrencia
	 $pos = strpos($text, $primeraOcurrencia);
	 
	 return $primeraOcurrencia;
	}
}

if( (!function_exists('html_post_reply'))){

	function html_post_reply($nombre, $comments, $hace, $idAlumno, $sexo){
		?>
		<div class="span12"> <!-- post reply !-->
						<ul class="thumbnails">
								<li class="span3">
						 		</li>
						 		<li class="span1" style="margin-top:8px;margin-right:0px;">
						 			<div >
						 				<a href="#">
						 					<img src="<?php echo imagen_perfil($idAlumno, $sexo); ?>" width="27" height="27" />
						 				</a>
						 			</div>
						 		</li>
								<li class="span7" style="margin-top:5px;margin-left:0px;">
									<h3 class="titulo-post"> <?php echo $nombre ?></h3>
									<p> <?php echo $comments; ?></p>
									<span class="tiempo_wall"> <?php echo $hace; ?> | <strong class="reply_wall"><a href="#" class="link_muro">Responder</a></strong> | <strong class="reply_wall"> <a href="#" class="link_muro">Compartir</a></strong> </span> 
								</li>
						</ul>	
		</div>
		<?php	
	}

}

if( (!function_exists('sidebar_videos'))){

	function sidebar_videos($idAlumno){
		$ci = & get_instance();
		$query=$ci->db->get_where("Facebook_Videos",
									array("idC006Alumno"=> $idAlumno,"active"=>1 ) );

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/videos/ver/<?php echo $row[0]->id; ?>" data-toggle="tooltip" title="<?php echo $row[0]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_video.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/videos/ver/<?php echo $row[1]->id; ?>" data-toggle="tooltip" title="<?php echo $row[1]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_video.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/videos/ver/<?php echo $row[2]->id; ?>" data-toggle="tooltip" title="<?php echo $row[2]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_video.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/videos/ver/<?php echo $row[3]->id; ?>" data-toggle="tooltip" title="<?php echo $row[3]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_video.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span9 offset2"> 
			<div class="alert">
			No hay videos!
			</div>
		</div>
		
		<?php	
		endif;
	}

}

if( (!function_exists('sidebar_musica'))){

	function sidebar_musica($idAlumno){
		$ci = & get_instance();
		$query=$ci->db->get_where("Facebook_Music",
									array("idC006Alumno"=> $idAlumno,"active"=>1 ) );

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/musica" data-toggle="tooltip" title="<?php echo $row[0]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_music.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/musica" data-toggle="tooltip" title="<?php echo $row[1]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_music.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/musica" data-toggle="tooltip" title="<?php echo $row[2]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_music.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->name != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/musica" data-toggle="tooltip" title="<?php echo $row[3]->name; ?>">
						 				<img src="<?php echo base_url()?>/theme/comunidad/bootstrap/img/sidebar_music.png" >
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span9 offset2"> 
			<div class="alert">
			No hay canciones!
			</div>
		</div>
		
		<?php	
		endif;
	}

}

if ( ! function_exists('get_avatar_comments'))
{
	
	
	function get_avatar_comments($idAlumno){
		$CI =& get_instance();
		
		$photos_base = $CI->config->item('photos_base');
		$photos_base_url = $CI->config->item('photos_base_url');
		
		$idAlumno= $idAlumno;
		//echo $photos_base.$idAlumno.".jpg";
		
		//$url=$photos_base."thumb".$idAlumno.".jpg"
		$url="http://www.intrafermatta.com.mx/fotos/"."thumb".$idAlumno.".jpg";
		$curl = curl_init($url);

	      //don't fetch the actual page, you only want to check the connection is ok
	      curl_setopt($curl, CURLOPT_NOBODY, true);
	  
	      //do request
	      $result = curl_exec($curl);
	  
	      $ret = false;
	  
	      //if request did not fail
	      if ($result !== false) {
	          //if request was ok, check response code|
	          $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
	  
	          if ($statusCode == 200) {
	              $ret = true;   
	          }
	      }
	  
	      curl_close($curl);
		
		
		
//		if(file_exists($photos_base."thumb".$idAlumno.".jpg")){
		if($ret==true){
			return "http://www.intrafermatta.com.mx/fotos/thumb".$idAlumno.".jpg";
			//return $photos_base_url.$idAlumno.".jpg";
		}else{
			
			$url="http://www.intrafermatta.com.mx/fotos/".$idAlumno.".jpg";
	  		$curl = curl_init($url);
	  
	  	      //don't fetch the actual page, you only want to check the connection is ok
	  	      curl_setopt($curl, CURLOPT_NOBODY, true);
	  	  
	  	      //do request
	  	      $result = curl_exec($curl);
	  	  
	  	      $ret = false;
	  	  
	  	      //if request did not fail
	  	      if ($result !== false) {
	  	          //if request was ok, check response code
	  	          $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
	  	  
	  	          if ($statusCode == 200) {
	  	              $ret = true;   
	  	          }
	  	      }
	  	  
	  	      curl_close($curl);
			
			
			if($ret==true){
				return "http://www.intrafermatta.com.mx/fotos/".$idAlumno.".jpg";
			//return $photos_base_url.$idAlumno.".jpg";
			}else{
			
				if($CI->session->userdata('sexo')=="M"){
					return $CI->config->site_url()."theme/frontend/gravatar/male_silhouette.jpg";
				}else{
					return $CI->config->site_url()."theme/frontend/gravatar/female_silhouette.jpg";
				}
			}
			
		}
	}
	
	
}

if( ! function_exists('table_solicitudes')){

	function table_solicitudes($datosAlumno){
		?>
		<?php //if(count($datosAlumno) > 0): ?>
		<table id="tableSolicitudes" class="table-striped" width="100%">
    	<tr>
    		<th> Nombre </th>
    		<th> Nick  </th>
    		<th> Confirmar  </th>
    	</tr>    	
    	<?php foreach ($datosAlumno as $item): ?>
    		<tr>
    		<td> <?php echo ucwords(strtolower($item->Nombre) .' '.strtolower($item->Appaterno).' '.strtolower($item->Apmaterno));  ?></td>
    		<td> ... </td>
    		<td> <a class="btn btn-small btn-custom" data-alumno="<?php echo $item->idC006Alumno; ?>"> Aceptar </a> <button  class="btn"> Ignorar </button> </td>
    	</tr>
    	<?php endforeach; ?>

       </table>
 <?php

	}
}

if( ! function_exists('table_invite')){

	function table_invite($datosAlumno, $datosSolicitudes){
		//print_r($datosAlumno);
		if(count($datosAlumno) > 0):
		?>
		<table id="tablePeople" class="table-striped" width="100%">
		<?php //print_r($datosSolicitudes); ?>
    	<tr>
    		<th> Nombre </th>
    		<th> Nick  </th>
    		<th> Confirmar  </th>
    	</tr>
    	
    	
    	<?php foreach ($datosAlumno as $item): ?>
    		<tr>
    		<td> <?php echo ucwords(strtolower($item->Nombre) .' '.strtolower($item->Appaterno).' '.strtolower($item->Apmaterno));  ?></td>
    		<td> <?php echo $item->nickname; ?> </td>
    		<?php if(array_search($item->idC006Alumno, $datosSolicitudes) > 0): ?>
    			<td> <a class="btn btn-small btn-custom disabled" data-alumno="<?php echo $item->idC006Alumno; ?>" data-loading-text="Enviando..."> Solicitud enviada </a> <button  class="btn btn-small"> Cancelar </button>   </td>
    		<?php else: ?>
    		<td> <a class="btn btn-small btn-custom" data-alumno="<?php echo $item->idC006Alumno; ?>" data-loading-text="Enviando..."> Enviar Solicitud </a> </td>
    	<?php endif; ?>
    	</tr>
    	<?php endforeach; ?>
    

       </table>
   <?php else:?>
   <div class="alert">
   	<p> No se encontraron coincidencias </p>
   </div>    
   <?php endif; ?>

 <?php

	}
}
if( !function_exists('count_solicitudes')){
	function count_solicitudes($idAlumno){

		//$Alumno_bus    = $idAlumno;
    	 //$notifications = $this->db->query("SELECT * FROM F004Favorites WHERE idC006Alumno_fav=$Alumno_bus AND Accepted =0");
		/*
		$ci = & get_instance();
    	 $credalumn= $idAlumno;
      $queryalumno="SELECT C006.idC006Alumno as Alumno, CONCAT(C006.Appaterno , ' ', C006.Apmaterno , ' ', C006.Nombre) as NombreAlumno, T005.Descripcion as OpcionEducativa, T008.Descripcion as UltimoCIclo, C010.Descripcion as Semestre, C004.Descripcion as Instrumento, C003.Descripcion as CAMPUS, C002.Descripcion as TIPOED
      , T008.idC003Campus,T008.idC002TipoEd, T008.idT008Ciclo  
      FROM T007Alumnos_Ciclos AS T007
      INNER JOIN C006Alumnos AS C006 ON C006.idC006Alumno=T007.idC006Alumno
      LEFT JOIN T008Ciclos AS T008 ON T008.idC003Campus=T007.idC003Campus AND T008.idC002TipoEd=T007.idC002TipoEd AND T008.idT008Ciclo=T007.idT008Ciclo
      LEFT JOIN T005OpcionEducativa AS T005
      ON T005.idC002TipoEd=T007.idC002TipoEd
      AND T005.idT005OpcionEd=T007.idT005OpcionEd
      LEFT JOIN C010Periodo as C010
      on C010.idC010Periodo = T007.idC010Periodo
      LEFT JOIN T006Alumno_Instrumento as T006
      on T006.idC003Campus = T007.idC003Campus
      and T006.idC002TipoEd = T007.idC002TipoEd
      and T006.idT008Ciclo = T007.idT008Ciclo
      and T006.idC006Alumno = T007.idC006Alumno
      LEFT JOIN C004Instrumento as C004
      on T006.idC004Instrumento = C004.idC004Instrumento
      LEFT JOIN C003Campus as C003
      on C003.idC003Campus = T007.idC003Campus
      LEFT JOIN C002TipoEducacion as C002
      on T007.idC002TipoEd = C002.idC002TipoEd
      WHERE T007.idC006Alumno='$credalumn' AND T008.DiaFin=(SELECT MAX(T008V2.DiaFin) FROM T008Ciclos AS T008V2 LEFT JOIN T007Alumnos_Ciclos AS T007V2 ON T008V2.idC003Campus=T007V2.idC003Campus AND T008V2.idC002TipoEd=T007V2.idC002TipoEd AND T008V2.idT008Ciclo=T007V2.idT008Ciclo WHERE 
      T007V2.idC006Alumno='$credalumn')";

		 $ciclos=$ci->db->query($queryalumno);

		 if($ciclos->num_rows()>0)
      {
		 $rowciclo = $ciclos->row();
        $query="SELECT DISTINCT T020.idC006Alumno, C006.Nombre,C006.Appaterno,C006.Apmaterno FROM T020AlumnoHorario as T020
                INNER JOIN C006Alumnos AS C006 ON T020.idC006Alumno = C006.idC006Alumno 
              WHERE Exists
              (SELECT * FROM T020AlumnoHorario as T020V2
              where T020V2.idC003Campus = T020.idC003Campus
              and T020V2.idC002TipoEd = T020.idC002TipoEd
              and T020V2.idT008Ciclo = T020.idT008Ciclo
              and T020V2.idC006Alumno <> T020.idC006Alumno
              and T020V2.idC001Materia = T020.idC001Materia
              and T020V2.idT012Bloques = T020.idT012Bloques
              and T020V2.idT013Grupos = T020.idT013Grupos
              and T020V2.idC003Campus = '".$rowciclo->idC003Campus."' 
              and T020V2.idC002TipoEd = '".$rowciclo->idC002TipoEd."' 
              and T020V2.idT008Ciclo = '".$rowciclo->idT008Ciclo."' 
              and T020V2.idC006Alumno = '". $idAlumno."') 
              
              ORDER BY RAND() LIMIT 9 ";
        $amigos=$ci->db->query($query);

    }*/

    	$ci = & get_instance();
    
    	 $amigos = $ci->db->query("SELECT * FROM Facebook_solicitudes as a, C006Alumnos as b WHERE  a.idC006Alumno_request = b.idC006Alumno and a.idC006Alumno = ".  $idAlumno ." AND accepted =0");

      return $amigos->num_rows();
	}
}
if ( !function_exists('info_perfil')){

	function info_perfil($idAlumno, $type = ''){
		$ci = & get_instance();
		$datos = array();

		$result = $ci->db->get_where('Facebook_infocontact', array('idC006Alumno' => $idAlumno), 1);
		if($result->num_rows() > 0){
			
		}

		switch ($type) {
			case 'normal':
				# code...
				break;
			case 'externo':
				break;
			default:
				# code...
				break;
		}

		
		return $result;
	}
}

if( (!function_exists('sidebar_friends'))){

	function sidebar_friends($idAlumno){
		$ci = & get_instance();
		

		$query = $ci->db->query("SELECT * FROM Facebook_friends as a, C006Alumnos as b WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno);

		//debere preguntar si tiene una foto en el perfil

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ."comunidad/wall/". substr(md5($row[0]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[0]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[0]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url') . $row[0]->Foto;?>"  />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[0]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" >
						 			<?php endif; ?>
						 			</a>
						 		<?php endif; ?>
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[1]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[1]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[1]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[1]->Foto;?>" >
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[1]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" >
						 			<?php endif; ?>
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[2]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[2]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[2]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[2]->Foto;?>" >
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[2]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" >
						 			<?php endif; ?>
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[3]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[3]->Nombre; ?>">
						 						<?php if(file_exists( $ci->config->item('photos_college_base'). $row[3]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[3]->Foto;?>" >
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[3]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" >
						 			<?php endif; ?>
						 		
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span9 offset2"> 
			<div class="alert">
			No hay amigos!
			</div>
		</div>
		
		<?php	
		endif;
	}

}
if( (!function_exists('sidebar_fotos'))){

	function sidebar_fotos($idAlumno){
		$ci = & get_instance();
		

		//$query = $ci->db->query("SELECT * FROM Facebook_Friends as a, C006Alumnos as b WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno);
		$query = $ci->db->query('select a.name as "imagen", b.url as "album", b.idC006Alumno, b.id as "idAlbum"
			from Facebook_Photos as a, Facebook_Albums as b
		where a.album = b.id and b.idC006Alumno = ' . $idAlumno);


		//debere preguntar si tiene una foto en el perfil

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/fotos_admin/ver/<?php echo $row[0]->idAlbum;?>" data-toggle="tooltip" title="<?php echo $row[0]->imagen; ?>">
						 				<img src="<?php echo $ci->config->item('galleries_base_url'). $row[0]->album . '/thumbsSmall/' . $row[0]->imagen;?>"  />
						 			</a>
						 		<?php endif; ?>
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/fotos_admin/ver/<?php echo $row[1]->idAlbum;?>" data-toggle="tooltip" title="<?php echo $row[1]->imagen; ?>">
						 				<img src="<?php echo $ci->config->item('galleries_base_url'). $row[1]->album . '/thumbsSmall/' . $row[1]->imagen;?>"  />
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/fotos_admin/ver/<?php echo $row[2]->idAlbum; ?>" data-toggle="tooltip" title="<?php echo $row[2]->imagen; ?>">
						 				<img src="<?php echo $ci->config->item('galleries_base_url'). $row[2]->album . '/thumbsSmall/' . $row[2]->imagen;?>"  />
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ?>comunidad/fotos_admin/ver/<?php echo $row[3]->idAlbum; ?>" data-toggle="tooltip" title="<?php echo $row[3]->imagen; ?>">
						 				<img src="<?php echo $ci->config->item('galleries_base_url'). $row[3]->album . '/thumbsSmall/' . $row[3]->imagen;?>"  />
						 			</a>
						 		<?php endif; ?>
						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span9 offset2"> 
			<div class="alert">
			No hay fotos!
			</div>
		</div>
		
		<?php	
		endif;
	}

}
if( (!function_exists('panel_amigos_added'))){

	function panel_amigos_added($idAlumno){
		$ci = & get_instance();
		//$query = $ci->db->query("SELECT * FROM Facebook_Friends as a, C006Alumnos as b WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno);
		//$query = $ci->db->query("SELECT * FROM Facebook_friends as a, C006Alumnos as b, Facebook_infocontact as c
		//	WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = c.idC006Alumno and a.idC006Alumno = " . $idAlumno);

		$query = $ci->db->query("SELECT * FROM Facebook_friends as a, C006Alumnos as b WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno);

		//print_r($query);
		//debere preguntar si tiene una foto en el perfil

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral-friends">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ."comunidad/wall/". substr(md5($row[0]->idC006Alumno_friend), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[0]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[0]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url') . $row[0]->Foto;?>" width="75px" height="75px"  />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[0]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px"  />
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[0]->Nombre) .' '.strtolower($row[0]->Appaterno).' '.strtolower($row[0]->Apmaterno)); ?> </span><br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[0]->Delegacion . " " . $row[0]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 		
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[1]->idC006Alumno_friend), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[1]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[1]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[1]->Foto;?>" width="75px" height="75px" />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[1]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px" >
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[1]->Nombre) .' '.strtolower($row[1]->Appaterno).' '.strtolower($row[1]->Apmaterno)); ?> </span> <br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[1]->Delegacion . " " . $row[1]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 			
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[2]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[2]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[2]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[2]->Foto;?>" width="75px" height="75px" />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[2]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px" >
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[2]->Nombre) .' '.strtolower($row[2]->Appaterno).' '.strtolower($row[2]->Apmaterno)); ?> </span> <br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[2]->Delegacion . " " . $row[2]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[3]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[3]->Nombre; ?>">
						 						<?php if(file_exists( $ci->config->item('photos_college_base'). $row[3]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[3]->Foto;?>" width="75px" height="75px" />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[3]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>"  width="75px" height="75px">
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[3]->Nombre) .' '.strtolower($row[3]->Appaterno).' '.strtolower($row[3]->Apmaterno)); ?> </span><br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[3]->Delegacion . " " . $row[3]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>

						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span8 offset1"> 
			<br>
			<div class="alert">
			No hay concidencias!
			</div>
		</div>
		
		<?php	
		endif;
	}
}
if( (!function_exists('panel_amigos_search'))){

	function panel_amigos_search($idAlumno, $search){
		$ci = & get_instance();
		//$query = $ci->db->query("SELECT * FROM Facebook_Friends as a, C006Alumnos as b WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno);
		$query = $ci->db->query("SELECT * FROM Facebook_friends as a, C006Alumnos as b
			WHERE a.idC006Alumno_friend = b.idC006Alumno and a.idC006Alumno = " . $idAlumno . 
			" and (b.Nombre like '%$search%' OR b.Appaterno like '%$search%' OR b.Apmaterno like '%$search%') limit 10");

		//print_r($query);
		//debere preguntar si tiene una foto en el perfil

		$numVidios =  $query->num_rows();

		$filas = $numVidios / 4;

		$arrays = array_chunk($query->result(), 4);
		if( $numVidios > 0 ):
		?>
		<? foreach ($arrays as $row) :?>
		<ul class="lista-lateral-friends">
						 	<li class="span3">
						 		<?php if(count($row) >= 1 && $row[0]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url() ."comunidad/wall/". substr(md5($row[0]->idC006Alumno_friend), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[0]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[0]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url') . $row[0]->Foto;?>" width="75px" height="75px"  />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[0]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px"  />
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[0]->Nombre) .' '.strtolower($row[0]->Appaterno).' '.strtolower($row[0]->Apmaterno)); ?> </span><br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[0]->Delegacion . " " . $row[0]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 		
						 	</li>	
						 	<li class="span3">
						 		<?php if(count($row) >= 2 && $row[1]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[1]->idC006Alumno_friend), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[1]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[1]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[1]->Foto;?>" width="75px" height="75px" />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[1]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px" >
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[1]->Nombre) .' '.strtolower($row[1]->Appaterno).' '.strtolower($row[1]->Apmaterno)); ?> </span> <br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[1]->Delegacion . " " . $row[1]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 			
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 3 && $row[2]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[2]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[2]->Nombre; ?>">
						 				<?php if(file_exists( $ci->config->item('photos_college_base'). $row[2]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[2]->Foto;?>" width="75px" height="75px">
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[2]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>" width="75px" height="75px" >
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[2]->Nombre) .' '.strtolower($row[2]->Appaterno).' '.strtolower($row[2]->Apmaterno)); ?> </span> <br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[2]->Delegacion . " " . $row[2]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>
						 	</li>
						 	<li class="span3">
						 			<?php if(count($row) >= 4 && $row[3]->idC006Alumno != ""): ?>
						 			<a href="<?php echo base_url()."comunidad/wall/". substr(md5($row[3]->idC006Alumno), 0, 8);?>" data-toggle="tooltip" title="<?php echo $row[3]->Nombre; ?>">
						 						<?php if(file_exists( $ci->config->item('photos_college_base'). $row[3]->Foto)): ?>
						 				<img src="<?php echo $ci->config->item('photos_college_url'). $row[3]->Foto;?>" width="75px" height="75px" />
						 			<?php else: ?>
						 				<img src="<?php echo ( $row[3]->Sexo == 'M') ? base_url().'theme/frontend/gravatar/male_silhouette.jpg' : base_url().'theme/frontend/gravatar/female_silhouette.jpg'; ?>"  width="75px" height="75px">
						 			<?php endif; ?>
						 			</a>
						 			<div class="friend-info" style="float:right;">
						 			 <span class="autocomplete-nombre-override"><?php echo ucwords(strtolower($row[3]->Nombre) .' '.strtolower($row[3]->Appaterno).' '.strtolower($row[3]->Apmaterno)); ?> </span><br>
						 			 <span class="autocomplete-nombre-override"> <?php echo $row[3]->Delegacion . " " . $row[3]->Estado; ?> </span>
						 			</div>
						 		<?php endif; ?>

						 	</li>
						 	
				</ul>
		<?php endforeach; 
		else:
		?>
		<div class="span8 offset1"> 
			<br>
			<div class="alert">
			No hay concidencias!
			</div>
		</div>
		
		<?php	
		endif;
	}
}
if ( ! function_exists('solicitud_enviada')){

	function solicitud_enviada($idAmigoVisit, $idAmigoSession){
		$ci = & get_instance();

		//echo $idAmigoSession;

		//$query = $ci->db->get_where('Facebook_solicitudes', array('idC006Alumno_request' => $idAmigoVisit, 'idC006Alumno' => $idAmigoSession, 'accepted' => '0'));
		$query = $ci->db->get_where('Facebook_solicitudes', array('idC006Alumno_request' => $idAmigoSession, 'idC006Alumno' => $idAmigoVisit, 'accepted' => '0'));
		//print_r($query);
		return $query->num_rows();
	}
}

//si es amigo puede ver sus post y puede publicar, permiso por defecto
if( ! function_exists('es_amigo')){
	function es_amigo($idAmigoVisit, $idAmigoSession){
		$ci = & get_instance();

		//echo $idAmigoVisit;

		$query = $ci->db->get_where('Facebook_friends', array('idC006Alumno_friend' => $idAmigoVisit, 'idC006Alumno' => $idAmigoSession));
		//print_r($query);
		return ( $query->num_rows() > 0) ? true : false;
	}
}

if( ! function_exists('tiene_publicaciones')){
	function tiene_publicaciones($idAmigoView){
		$ci = & get_instance();
		$query = $ci->db->get_where('facebook_posts', array('posted_by' => $idAmigoView));
		return ( $query->num_rows() > 0) ? true : false;
	}
}

if( !function_exists('pinta_imagenes')){
    
    function pinta_imagenes($idPost, $imagenes){
        $num = count($imagenes);
                                                                                
           if($num >= 3){
               $fila = $num/3;
               $arrays = array_chunk($imagenes, 3);

            $i = 0;
            while ($i < $fila) {
//                                                                                   
                //print_r($arrays[$i]) ;
                if (count($arrays[$i]) == 3) {
                    row_image($idPost,3, $arrays[$i][0], $arrays[$i][1], $arrays[$i][2]);
                } else {
                    switch (count($arrays[$i])) {
                        case 1:
                            row_image($idPost,1, $arrays[$i][0], '', '');
                            break;
                        case 2:
                            row_image($idPost,2, $arrays[$i][0], $arrays[$i][1], '');
                            break;
                        default:
                            break;
                    }
                }
                $i++;
            }
        } elseif ($num == 2) {
            row_image($idPost,2, $imagenes[0], $imagenes[1], '');
        } else {
            row_image($idPost,1, $imagenes[0], '', '');
        }
    }
}

if( !function_exists('sanear_string')){

	function sanear_string($string){
		$string = trim($string);

	    $string = str_replace(
	        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
	        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
	        $string
	    );

	    $string = str_replace(
	        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
	        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
	        $string
	    );

	    $string = str_replace(
	        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
	        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
	        $string
	    );

	    $string = str_replace(
	        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
	        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
	        $string
	    );

	    $string = str_replace(
	        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
	        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
	        $string
	    );

	    $string = str_replace(
	        array('ñ', 'Ñ', 'ç', 'Ç'),
	        array('n', 'N', 'c', 'C',),
	        $string
	    );

	    //Esta parte se encarga de eliminar cualquier caracter extraño
	    $string = str_replace(
	        array("\\", "¨", "º", "-", "~",
	             "#", "@", "|", "!", "\"",
	             "·", "$", "%", "&", "/",
	             "(", ")", "?", "'", "¡",
	             "¿", "[", "^", "`", "]",
	             "+", "}", "{", "¨", "´",
	             ">", "< ", ";", ",", ":",
	             ".", " "),
	        '',
	        $string
	    );

	    return $string;
	}
}