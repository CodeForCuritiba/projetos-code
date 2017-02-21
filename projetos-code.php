<?php
/*
Plugin Name: Projetos CodeforCuritiba
Plugin URI: http://codeforcuritiba.org
Description: Plugin para registro de projeto
Version: 1.0
Author: Daniel Ikenaga
Author URI: http://concatenando.compact
Text Domain: projetos-code
License: GPL2
*/

require dirname(__FILE__).'/lib/class-tgm-plugin-activation.php';

class Projetos_code {
    private static $instance;
    const TEXT_DOMAIN = 'projetos-code';
    const FIELD_PREFIX = 'pc_';

    public static function getInstance(){
        if (self::$instance == NULL){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){
        add_action('init', 'Projetos_code::register_post_type');
        add_action('init', 'Projetos_code::register_taxonomies');
        add_action('tgmpa_register', array($this,'check_required_plugins'));
        add_action('rwmb_meta_boxes', 'Projetos_code::metabox_custom_fields');
        add_filter('user_can_richedit' , create_function('' , 'return false;') , 50);
        add_action('template_include', array($this, 'add_template_single') );
        add_action('wp_enqueue_scripts', array($this, 'add_style_css') );
    }

    public static function register_post_type(){
        register_post_type('projetos-code', array(
            'labels' => array(
                'name' => 'Projetos',
                'singular_name' => 'Projeto',
                'add_new' => 'Adicionar projeto',
                'edit_item' => 'Editar projeto',
                'add_new_item' => 'Adicionar novo projeto'
                ),
            'description' => 'Post para cadastro de projeto',
            'supports' => array(
                'title', 'editor', 'author', 'revisions', 'thumbnail', 
                ),
            'public' => TRUE,
            'menu_icon' => 'dashicons-universal-access',
            'menu_position' => 3

        ));
    }

    public static function register_taxonomies(){
        register_taxonomy('tipos_projetos', array( 'projetos-code' ),
                            array(
                                'labels' => array(
                                    'name' => __('Tipos de Projetos'),
                                    'singular_name' => __('Tipos de Projetos')
                                ),
                                'public' => TRUE,
                                'hierarchical' => TRUE,
                                'rewrite' => array('slug' => 'tipos-projetos')
                            )
        );
    }

    function check_required_plugins(){
        $plugins = array(
            array(
                'name' => 'Meta Box',
                'slug' => 'meta-box',
                'required' => TRUE,
                'force_activation' => false,
                'force_desactivation' => false,
            )
        );
    $config = array(
            'domain'       => TEXT_DOMAIN,
            'default_path' => '',
            'parent_slug'  => 'plugins.php',
            'capability'   => 'update_plugins',
            'menu'         => 'install-required-plugins',
            'has_notices'  => TRUE,
            'is_automatic' => false,
            'message'      => '',
            'strings'      => array(
                'page_title'    => __( 'Install Required Plugins', TEXT_DOMAIN ),
                'menu_title'    => __( 'Install Plugins', TEXT_DOMAIN ),
                'installing'    => __( 'Install Plugin: %s', TEXT_DOMAIN ),
                'oops'          => __( 'Error in Install Plugin', TEXT_DOMAIN ),
                'nag_type'      => 'updated',
            )
            );
    tgmpa( $plugins, $config);
    }

public function metabox_custom_fields(){

    $meta_boxes[] = array(

        'id'       => 'data_projeto',
        'title'    => __('Informações do Projeto', 'projetos-code' ),
        'post_types' => 'projetos-code',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(

                        array(
                            'name' => __('Data início do projeto', FIELD_PREFIX ),
                            'desc' => __('', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_inicio',
                            'type' => 'number',
                            'std'  => date('Y'),
                            'min' => '2010',
                        ),
                        array(
                            'name' => __('Link do projeto', FIELD_PREFIX ),
                            'desc' => __('Adicione o link completo para acessar o projeto.(incluir http)', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_link',
                            'size' => 60,
                            'type' => 'url',
                            'std'  => '',
                        ),
                        array(
                            'name' => __('Equipe do projeto', FIELD_PREFIX ),
                            'desc' => __('', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_equipe',
                            'type' => 'textarea',
				            'rows' => 6,
                            'std'  => '',
                        ),
                        array(
                            'name' => __('Solicitação de integrantes', FIELD_PREFIX ),
                            'desc' => __('', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_novo_integrante',
                            'type' => 'textarea',
				            'rows' => 6,
                            'std'  => '',
                        ),
                        array(
                            'name' => __('Situação do projeto', FIELD_PREFIX ),
                            'desc' => __('', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_situacao',
                            'type' => 'radio',
                            'options' => array(
                                            'value1' => __( 'Finalizado ', FIELD_PREFIX ),
                                            'value2' => __( 'Parado ', FIELD_PREFIX ),
                                            'value3' => __( 'Em desenvolvimento ', FIELD_PREFIX ),
                                            'value4' => __( 'Não iniciado ', FIELD_PREFIX ),
                                        ),
                            'std'  => '',
                        ),
                        array(
                            'name' => __('Link no Github', FIELD_PREFIX ),
                            'desc' => __('Adicione o link completo.', FIELD_PREFIX ),
                            'id'   => self::FIELD_PREFIX.'projeto_github',
                            'size' => 60,
                            'type' => 'url',
                            'std'  => 'https://github.com/CodeForCuritiba/',
                        ),
                    )

    );
    return $meta_boxes;
}

function add_template_single($template){
    if(is_singular('projetos-code')){

        if(file_exists(get_stylesheet_directory().'single-projeto_code.php')){
            return get_stylesheet_directory().'single-projeto_code.php';
        }
        return plugin_dir_path(__FILE__).'single-projeto_code.php';
    }

  return $template;
}

function add_style_css(){
    wp_enqueue_style('projeto-code-style', plugin_dir_path(__FILE__).'projeto_code.css');
}


public static function activate(){
        self::register_post_type();
        self::register_taxonomies();
        flush_rewrite_rules();
    }
}

Projetos_code::getInstance();


register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'Projetos_code::activate' );

/*


Público ========================
Identificação do problema
   Ishikawa
Recursos necessários
Recursos disponíveis
Definição do escopo
   Qual problema a ser resolvido
Justificativa
Público alvo / Categoria
Validação
Proposta de engajamento
================================

Desenvolvimento ================
Metodologia
    5W2H
Pessoas Envolvidas
Proposta de Cronograma
Pontos fracos
================================

Registro =======================
Titulo
Resumo da Proposta
Equipe
Solicitação de integrantes
Data de início do Projeto
Projeto no Github
Situação do Projeto
   Ativo/Recrutando Inativo
Categorias
================================

*/