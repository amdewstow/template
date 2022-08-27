# template
Template Engine

A simple template engine designed to allow for a lot of flexibility. 


# Install
To Install with composer

Add this to your composer.json

    "repositories": [
        {
          "type": "package",
          "package": {
            "name": "amdewstow/template",
            "version": "1.0.0",
            "type": "git",
            "source": {
              "url": "https://github.com/amdewstow/template.git",
              "type": "git",
              "reference": "main"
            }
          }
        }
    ]

Then

composer require amdewstow/template


basic usage

    $t                     = new template( );
    $ar                    = array( );
    $ar['item']            = 'This is the item';//text that will replace {item}
    $html                  = $t->load_template( 'template_filename.html' );
    echo $t->smart_template( $html, $ar );