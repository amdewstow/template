<?php
    //load via autoload
    //require( 'vendor/autoload.php' );
    //or require directly
    require( '../src/template.php' );
    use template\template;
    $__template_settings                             = array( );
    $__template_settings[ 'always' ]                 = array( );
    $__template_settings[ 'always' ][ 'start_time' ] = microtime( true );
    $__template_settings[ 'js_ver' ]                 = sha1( date( 'd/m/Y' ) );
    $__template_settings[ 'add_js_ver' ]             = true;
    $__template_settings[ 'debug' ]                  = false;
    $t                                               = new template( $__template_settings );
    $t->add_always( 'HOST', 'http://localhost/' );
    $t->add_always( 'css_dir', 'http://localhost/css/', false );
    $ar            = array( );
    $ar[ 'title' ] = "Clean Template Example";
    $html          = $t->load_template( 'clean_template_levels', __DIR__ . '/_templates/' );
    $t->set_clean_level( 0 );
    //$t->debug = true;
    $ugly              = $t->load_template( 'ugly_html', __DIR__ . '/_templates/' );
    $ugly_row          = $t->load_template( 'ugly_rows', __DIR__ . '/_templates/' );
    $start_size        = strlen( $ugly );
    $ar[ 'ugly_rows' ] = array( );
    $t->add_js_ver     = false;
    for ( $cc = 0; $cc <= 4; $cc++ ) {
        $cleaned        = $t->clean_template( $ugly, $cc );
        $cleaned_size   = strlen( $cleaned );
        $arr            = array( );
        $arr[ 'clean' ] = $cc;
        $arr[ 'bytes' ] = number_format( $cleaned_size );
        $saved          = $start_size - $cleaned_size;
        $arr[ 'saved' ] = number_format( $saved ) . " : " . round( ( $saved / $start_size ) * 100, 2 ) . "%";
        $arr[ 'html' ]  = htmlspecialchars( $cleaned );
        $t->set_clean_level( 0 );
        $ar[ 'ugly_rows' ][ ] = $t->smart_template( $ugly_row, $arr );
    }
    echo $t->smart_template( $html, $ar );