# template
Template Engine

A simple template engine designed to allow for a lot of flexibility. 


To Install with composer
composer require amdewstow/template


basic usage

    $t                     = new template( );
    $ar                    = array( );
    $ar['item']            = 'This is the item';//text that will replace {item}
    $html                  = $t->load_template( 'template_filename.html' );
    echo $t->smart_template( $html, $ar );