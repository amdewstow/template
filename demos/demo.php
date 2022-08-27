<?php
    //load via autoload
    //require( 'vendor/autoload.php' );
    //or require directly
    require( '../src/template.php' );
    use template\template;
    $__template_settings                             = array( );
    $__template_settings[ 'always' ]                 = array( );
    $__template_settings[ 'always' ][ 'start_time' ] = microtime( true );
    //js_ver gets added to external fiel refenced
    $__template_settings[ 'js_ver' ]                 = sha1( date( 'd/m/Y' ) );
    $__template_settings[ 'add_js_ver' ]             = true;
    //
    $__template_settings[ 'debug' ]                  = false;
    $__template_settings[ 'template_dir' ]           = __DIR__ . '/_templates/';
    $t                                               = new template( $__template_settings );
    $t->add_always( 'HOST', 'http://localhost/' );
    $t->add_always( 'css_dir', 'http://localhost/css/', false );
    $ar                    = array( );
    $html                  = $t->load_template( 'tester' );
    //if $userD ws from your DB
    $userD                 = array( );
    $userD[ 'first_name' ] = "Anthony";
    $userD[ 'last_name' ]  = "Dewstow";
    //
    $ar[ 'first_name' ]    = $userD[ 'first_name' ];
    $ar[ 'last_name' ]     = $userD[ 'last_name' ];
    $ar[ 'title' ]         = "Template Example";
    $ar[ 'one' ]           = 1;
    $ar[ 'two' ]           = 2;
    $ar[ 'lines' ]         = array(
         'a',
        'b' 
    );
    $ar_rows               = array( );
    $ar_rows[ ]            = array(
         'ID' => 1,
        'Thing' => 'Apple' 
    );
    $ar_rows[ ]            = array(
         'ID' => 2,
        'Thing' => 'Banana' 
    );
    $ar_rows[ ]            = array(
         'ID' => 3,
        'Thing' => 'Cantaloupe' 
    );
    $html_rows             = $t->load_template( 'tester_rows', __DIR__ . '/demos/_templates/' );
    foreach ( $ar_rows as $kk => $vv ) {
        $ar[ 'rows' ][ ] = $t->smart_template( $html_rows, $vv );
    }
    echo $t->smart_template( $html, $ar );