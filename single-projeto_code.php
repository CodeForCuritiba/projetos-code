<?php
get_header();
?>
<div id="primary" class="content-area">
    <main id="main" class="content site-main" role="main">
        <?php

            while( have_posts() ) : the_post();

               $field_prefix = Projetos_code::FIELD_PREFIX;
               $projeto_inicio = post_custom($field_prefix.'projeto_inicio');
               $imagem       = get_the_post_thumbnail( get_the_ID(), 'large' );
               $projeto_situacao = post_custom($field_prefix.'projeto_situacao');
               $projeto_categorias = get_the_terms( get_the_ID(), 'tipos_projetos' );
               $projeto_link = post_custom($field_prefix.'projeto_link');
               $projeto_github = post_custom($field_prefix.'projeto_github');
               $projeto_equipe = preg_split('/\n|\r\n?/', post_custom($field_prefix.'projeto_equipe'));
               $projeto_novo_integrante = post_custom($field_prefix.'projeto_novo_integrante');
               
               if($projeto_inicio != ''){
                   $projeto_inicio = '('.$projeto_inicio.')';
               }

                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                    <header class="entry-header">
                            <?php the_title('<h1 class="entry-title">', ' '.$projeto_inicio.'</h1'); ?>
                    </header>
                <?php
                
                if( !empty($projeto_novo_integrante) ){
                    echo '<button style="background:#8dc550;">Colabore com este projeto!</button>';
                    echo '<br><br>';
                }

                if( !empty($imagem) ){
                    echo '<div class="poster">';
                    echo $imagem;
                    echo '</div><br>';
                }
                
                if( !empty($projeto_situacao) ){
                    switch ($projeto_situacao) {
                        case 'value1':
                            echo '<button style="background:#1714e0;padding:0.4em 0.4em 0.4em;">Finalizado</button>';
                            break;
                        case 'value2':
                            echo '<button style="background:#d33737;padding:0.4em 0.4em 0.4em;">Parado</button>';
                            break;
                        case 'value3':
                            echo '<button style="background:#14ad06;padding:0.4em 0.4em 0.4em;">Em desenvolvimento</button>';
                            break;
                        case 'value4':
                            echo '<button style="background:#cecb04;padding:0.4em 0.4em 0.4em;">Não iniciado</button>';
                            break;
                    }
                    echo '<br><br>';
                }

                ?>
                    <div class="right">
                        <div class="review-body">
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php


               if($projeto_categorias && !is_wp_error($projeto_categorias)){
                    $projeto_cat = array();

                    foreach($projeto_categorias as $cat){
                        $projeto_cat[] = $cat->name;
                    }

               }

               
               if( !empty($projeto_cat) ){
                    echo '<div class="tipo">';
                    foreach($projeto_cat as $cat){
                        echo ' <button style="background:#8dc550;padding:0.4em 0.4em 0.4em;">'.$cat.'</button> ';
                    }
                    echo '<br><br>';
               }


               if( !empty($projeto_novo_integrante) ){
                    echo '<h3>Participe deste projeto</h3>';
                    echo $projeto_novo_integrante;
                    echo '<br><br>';
               }

               if( !empty($projeto_link) ){
                    echo '<h3>Acesse o projeto</h3>';
                    echo '<a href="'.$projeto_link.'" target="_blank">'.$projeto_link.'</a>';
                    echo '<br><br>';
               }

               if( !empty($projeto_github) ){
                    echo '<h3>Código fonte do projeto</h3>';
                    echo '<a href="'.$projeto_github.'" target="_blank">'.$projeto_github.'</a>';
                    echo '<br><br>';
               }

               if( !empty($projeto_equipe) ){
                    echo '<h3>Equipe do projeto</h3>';
                    echo '<ul>';
                    foreach ( $projeto_equipe as $projeto_participante ) {
                        echo '<li>'. trim( $projeto_participante ) .'</li>';
                    }
                    echo '</ul>';
               }

             edit_post_link(__('Editar dados do projeto'));

            endwhile;

            ?>
                <hr>
                <?php previous_post_link(); ?>
                <div style="float: right;" ><?php next_post_link(); ?></div> 
                
            </article>
    </main>
</div>


<?php

get_footer();

// Autocomplete categories http://jsfiddle.net/7gWMv/157/
?>