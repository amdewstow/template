<?php
    namespace template;
    class template {
        var $template_dir = '';
        var $clean_level = 10;
        var $always = array( );
        var $template_file_sub_name = '';
        var $debug = false;
        var $debug_filenames = false;
        var $_js_ver = '';
        var $add_js_ver = true;
        function __construct( $template_settings = array( ) ) {
            if ( isset( $template_settings[ 'template_dir' ] ) ) {
                $this->template_dir = $template_settings[ 'template_dir' ];
            } else {
                $this->template_dir = "./_templates/";
            }
            if ( !is_dir( $this->template_dir ) ) {
                throw new \Exception( "\n\nNot a real folder '" . $this->template_dir . "'\n\n" );
            }
            if ( isset( $template_settings[ 'always' ] ) && ( is_array( $template_settings[ 'always' ] ) ) ) {
                foreach ( $template_settings[ 'always' ] as $key => $val ) {
                    $this->add_always( $key, $val );
                }
            }
            if ( isset( $template_settings[ 'clean_level' ] ) && ( is_numeric( $template_settings[ 'clean_level' ] ) ) ) {
                $this->set_clean_level( $template_settings[ 'clean_level' ] );
            }
            if ( isset( $template_settings[ 'debug' ] ) && ( is_bool( $template_settings[ 'debug' ] ) ) ) {
                $this->set_debug( $template_settings[ 'debug' ] );
            }
            if ( isset( $template_settings[ 'js_ver' ] ) ) {
                $this->set_js_ver( $template_settings[ 'js_ver' ] );
            }
            if ( isset( $template_settings[ 'add_js_ver' ] ) && ( is_bool( $template_settings[ 'add_js_ver' ] ) ) ) {
                $this->set_add_js_ver( $template_settings[ 'add_js_ver' ] );
            }
        }
        public function add_always( $key, $value, $add_underscores = true ) {
            if ( $add_underscores === true ) {
                $key = "_" . $key . "_";
            }
            $this->always[ $key ] = $value;
        }
        public function set_clean_level( $clean_level = 0 ) {
            $this->clean_level = $clean_level;
        }
        public function set_debug( $debug = false ) {
            $this->debug = $debug;
        }
        public function set_js_ver( $js_ver = true ) {
            $this->_js_ver = "?r=" . $js_ver;
        }
        public function set_add_js_ver( $add_js_ver = true ) {
            $this->add_js_ver = $add_js_ver;
        }
        /**
         * load_template System
         * @param string $ff <p>The Filename</p>    
         * @param $bk <p> load from where?</p>
         *  <p> Default is ./_templates/</p>
         *  <p> 'full' will assume $ff is a full path</p>    
         *  <p> or pass a full directory path
         * @return string the html
         */
        public function load_template( $ff, $bk = false ) {
            if ( substr( $ff, -5 ) != ".html" ) {
                //not ending in .html 
                if ( $this->template_file_sub_name != '' ) {
                    //add site name
                    $try = $ff . "_" . $this->template_file_sub_name . ".html";
                    if ( ( $bk === 1 ) || ( $bk === true ) ) {
                        $fzz = realpath( "../" . $this->template_dir . $try );
                    } else if ( $bk == 'full' ) {
                        $fzz = realpath( $try );
                    } else if ( $bk == 'base' ) {
                        //public template
                        $fzz = realpath( __DIR__ . "/../_templates/" . $try );                   
                    } else if ( is_dir( $bk ) ) {
                        $fzz = realpath( $bk . $try );
                    } else {
                        $fzz = realpath( $this->template_dir . $try );
                    }
                    if ( ( $fzz !== '' ) && ( is_file( $fzz ) ) ) {
                        $ff = $try;
                    }
                }
            }
            if ( substr( $ff, -5 ) != ".html" ) {
                $ff .= ".html";
            }
            if ( ( $bk === 1 ) || ( $bk === true ) ) {
                $fzz = realpath( "../" . $this->template_dir . $ff );
                if ( $fzz == '' ) {
                    throw new \Exception( '<br><hr>Failed to find ' . "../" . $this->template_dir . $ff . " AKA " . $fzz );
                }
            } else if ( $bk == 'full' ) {
                $fzz = realpath( $ff );
                if ( $fzz == '' ) {
                    throw new \Exception( '<br><hr>Failed to find ' . $ff . " AKA " . $fzz );
                }
            } else if ( $bk == 'base' ) {
                //public template
                $fzz = realpath( __DIR__ . "/../_templates/" . $ff );
                if ( $fzz == '' ) {
                    throw new \Exception( '<br><hr>Failed to find base ' . __DIR__ . "/../_templates/" . $ff . " AKA " . $fzz );
                }
            } else if ( is_dir( $bk ) ) {
                $fzz = realpath( $bk . $ff );
            } else {
                $fzz = realpath( $this->template_dir . $ff );
                if ( $fzz == '' ) {
                    throw new \Exception( '<br><hr>Failed to find ' . $this->template_dir . $ff );
                }
            }
            $r = file_get_contents( $fzz );
            if ( $r === false ) {
                throw new \Exception( 'Error with ' . $this->template_dir . $ff . ' :: ' . realpath( $this->template_dir ) . ' :: ' . $fzz );
                return 'Error with ' . $fzz;
            }
            if ( $this->debug_filenames === true ) {
                $r = "\n<!--==START " . $fzz . "==-->\n" . $r . "\n<!--==END " . $fzz . "==-->\n";
            }
            return $r;
        }
        /**
         * clean_template System
         * @param string $s <p>
         * The HTML
         * </p>    
         * @param int $clean_level <p>
         * 0-5, higher = smaller files
         * </p>    
         * @return string the html
         */
        public function clean_template( $s, $clean_level = null ) {
            $this->replacments = 0;
            if ( $clean_level !== null ) {
                $this->clean_level = $clean_level;
            }
            if ( $this->_js_ver == '' ) {
                $this->_js_ver = "?r=" . sha1( microtime( 1 ) );
            }
            $before = strlen( $s );
            $ars    = array( );
            //$ars[] = array(replace,find,find...)
            if ( $this->clean_level >= 1 ) {
                $s  = trim( $s );
                $sp = array( );
                $nl = array( );
                for ( $kki = 1; $kki <= 10; $kki++ ) {
                    $sp[ ] = str_repeat( " ", $kki );
                    $nl[ ] = str_repeat( "\n", $kki );
                }
                $ars[ ] = $sp;
                $ars[ ] = $nl;
            }
            if ( $this->clean_level >= 2 ) {
                $ars[ ] = array(
                     '',
                    "\t" 
                );
                $ars[ ] = array(
                     '; ',
                    ';  ' 
                );
                $ars[ ] = array(
                     '">',
                    '" >' 
                );
                $ars[ ] = array(
                     '</',
                    ' </',
                    "\n</" 
                );
                $ars[ ] = array(
                     "'>",
                    "' >" 
                );
                $ars[ ] = array(
                     " <",
                    "  <" 
                );
                //$ars[ ] = $this->every_space('<!--');
                //$ars[ ] = $this->every_space('-->');
            }
            if ( $this->clean_level >= 3 ) {
                $ars[ ] = $this->every_space( "<tr>" );
                $ars[ ] = array(
                     "<td",
                    "\n<td" 
                );
                $ars[ ] = $this->every_space( "<td>" );
                $ars[ ] = array(
                     "\n",
                    "\n ",
                    " \n" 
                );
                $ars[ ] = array(
                     "</tr",
                    " </tr",
                    "\n</tr" 
                );
                $ars[ ] = $this->every_space( "</td>" );
                $ars[ ] = array(
                     "<p",
                    "\n<p" 
                );
                $ars[ ] = array(
                     "<img",
                    "\n<img" 
                );
                $ars[ ] = $this->every_space( "</div>" );
                $ars[ ] = $this->every_space( "</em>" );
                $ars[ ] = $this->every_space( "</i>" );
                $ars[ ] = $this->every_space( "</li>" );
                $ars[ ] = $this->every_space( "</ol>" );
                $ars[ ] = $this->every_space( "</p>" );
                $ars[ ] = $this->every_space( "</s>" );
                $ars[ ] = $this->every_space( "</span>" );
                $ars[ ] = $this->every_space( "</u>" );
                $ars[ ] = $this->every_space( "</ul>" );
                $ars[ ] = $this->every_space( "<br/>" );
                $ars[ ] = $this->every_space( "<br>" );
                $ars[ ] = $this->every_space( "<div>" );
                $ars[ ] = $this->every_space( "<em>" );
                $ars[ ] = $this->every_space( "<i>" );
                $ars[ ] = $this->every_space( "<li>" );
                $ars[ ] = $this->every_space( "<ol>" );
                $ars[ ] = $this->every_space( "<p>" );
                $ars[ ] = $this->every_space( "<s>" );
                $ars[ ] = $this->every_space( "<span>" );
                $ars[ ] = $this->every_space( "<u>" );
                $ars[ ] = $this->every_space( "<ul>" );
                $ars[ ] = array(
                     "",
                    "//FK//" 
                );
            }
            if ( $this->add_js_ver === true ) {
                $ars[ ] = array(
                     '.js' . $this->_js_ver . '">',
                    '.js">' 
                );
                $ars[ ] = array(
                     '.js' . $this->_js_ver . '" integrity=',
                    '.js" integrity=' 
                );
                $ars[ ] = array(
                     '.css' . $this->_js_ver . '">',
                    '.css">' 
                );
                $ars[ ] = array(
                     '.css' . $this->_js_ver . '"/>',
                    '.css"/>' 
                );
                $ars[ ] = array(
                     '.css' . $this->_js_ver . '" ',
                    '.css" ' 
                );
                $ars[ ] = array(
                     $this->_js_ver,
                    '{_js_ver_}' 
                );
            } else {
                $ars[ ] = array(
                     '',
                    '{_js_ver_}' 
                );
            }
            if ( $this->clean_level >= 4 ) {
                $ars[ ] = array(
                     '',
                    "<!-- -->",
                    "<!---->" 
                );
                $ars[ ] = array(
                     ' ',
                    '  ',
                    "\r\n",
                    "\n\r",
                    "\t",
                    "\n",
                    "\r",
                    "\n ",
                    " \n",
                    "\n\n" 
                );
            }
            if ( $this->clean_level >= 4 ) {
                //clean double spaces again
                $ars[ ] = array(
                     ' ',
                    "\n ",
                    "  " 
                );
            }
            if ( $this->clean_level >= 4 ) {
                $ars[ ] = array(
                     '><',
                    '> <' 
                );
            }
            foreach ( $ars as $kk => $vv ) {
                $t = $vv[ 0 ];
                unset( $vv[ 0 ] );
                foreach ( $vv as $f ) {
                    if ( $f !== $t ) {
                        $n = 1;
                        while ( $n > 0 ) {
                            $b = $s;
                            $s = str_replace( $f, $t, $s, $n );
                            $this->replacments += $n;
                            if ( $b === $s ) {
                                $n = -1;
                            }
                        }
                    }
                }
            }
            if ( $this->clean_level >= 1 ) {
                $s = trim( $s );
            }
            if ( $this->debug === true ) {
                $s     = str_replace( "<!--", "\n<!--", $s );
                $s     = str_replace( "-->", "-->\n", $s );
                $after = strlen( $s );
                $saved = ( $before - $after );
                $s .= "\n<!-- Saved " . $saved . ":" . round( ( ( $saved / $before ) * 100 ), 0 ) . "% clean_level:" . $this->clean_level . " clean_count:" . count( $ars ) . " :: " . number_format( $this->replacments ) . "-->";
            }
            return $s;
        }
        /**
         * Smart Template System
         * @param string $string <p>
         * The HTML
         * </p>    
         * @param array $array <p>
         * The associated array of data
         * </p>    
         * @return string the data
         */
        public function smart_template( $string, array $array, $looped = false ) {
            $st = microtime( true );
            if ( !is_array( $array ) ) {
                throw new \Exception( "Non Array Passed to smart_template();" );
            }
            foreach ( $this->always as $kk => $vv ) {
                $array[ $kk ] = $vv;
            }
            if ( isset( $_SESSION[ 'debug' ] ) && ( $_SESSION[ 'debug' ] >= 3 ) ) {
                //printr( htmlspecialchars( $s ) );
                printhtmlspecialchars( $array );
            }
            foreach ( $array as $kk => $vv ) {
                //            echo "\n<br>:".$kk.":".$vv;
                if ( is_array( $vv ) ) {
                    $vv = $this->multi_implode( "\n", $vv );
                }
                $string = str_replace( "{" . $kk . "}", $vv, $string );
            }
            if ( $looped !== -5 ) {
                $string = $this->clean_template( $string );
            }
            $n = 1;
            while ( $n ) {
                $string = str_replace( " </", '</', $string, $n );
            }
            if ( $looped === false ) {
                if ( strstr( $string, '{' ) !== false ) {
                    $string = $this->smart_template( $string, $array, true );
                }
            }
            if ( $this->debug_filenames === true ) {
                $sk = md5( $string );
                return "\n<!-- START $sk -->\n" . $string . "\n<!-- END $sk t:" . number_format( ( microtime( true ) - $st ), 7 ) . "-->\n";
            } else {
                return $string;
            }
        }
        /**
         * multi_implode
         * @param string $glue <p>
         * The separator 
         * </p>    
         * @param array $array <p>
         * The array of data
         * </p>    
         * @return string the string
         */
        public function multi_implode( $glue, $array ) {
            $out = "";
            foreach ( $array as $item ) {
                if ( is_array( $item ) ) {
                    if ( empty( $out ) ) {
                        $out = multi_implode( $glue, $item );
                    } else {
                        $out .= $glue . multi_implode( $glue, $item );
                    }
                } else {
                    if ( empty( $out ) )
                        $out = $item;
                    else
                        $out .= $glue . $item;
                }
            }
            return $out;
        }
        /**
         * every_space
         * @param string $og <p>
         * The separator 
         * </p>             
         * @return array for find replace logic
         */
        public function every_space( $og ) {
            $a    = array( );
            $a[ ] = $og;
            $a[ ] = " " . $og . " ";
            $a[ ] = " " . $og;
            $a[ ] = $og . " ";
            $a[ ] = $og . "\n";
            if ( $this->clean_level >= 4 ) {
                //hard returns allwoed before tags until we get to clean level 4
                $a[ ] = "\n" . $og;
            }
            return $a;
        }
    }