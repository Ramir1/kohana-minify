<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 *  // Minify given string
 *  $min = Minify::factory('js')->set( $filecontents )->min();
 *  $min = Minify::factory('css')->set( $filecontents )->min(); 
 *
 *  // Minify list of files; write result into media folder 
 *  Controller::after()  
 * 	$this->template->jsFiles = Minify::factory('js')->minify( $this->template->jsFiles, $build );
 * 	$this->template->cssFiles = Minify::factory('css')->minify( $this->template->cssFiles, $build );
 *
 *  View:
  foreach ($cssFiles as $css) {
  if ( ! Kohana::config('minify.enabled') || $debug )
  $js = "views/css/{$css}?{$build}";
  echo HTML::style($css),"\n";
  }
  // application js files
  foreach ($jsFiles as $js) {
  if ( ! Kohana::config('minify.enabled') || $debug )
  $js = "views/jscript/{$js}?{$build}";
  echo HTML::script($js),"\n";
  }
 */
class Minify_Core {

    protected $type;
    protected $file;
    protected $filepath; //путь к файлу
    protected $input = '';
    protected $inputLength = 0;

    public function __construct($type) {
        $this->type = $type;
    }

    public static function factory($type) {
        $class = 'Minify_' . ucfirst($type);
        return new $class($type);
    }

    // Dateien zusammenfassen, komprimieren und im media verzeichnis ablegen
    public function minify($files, $build = '') {
        if (Kohana::$config->load('minify.enabled', false)) {
            $name = 'minify_'.substr(md5(json_encode($files)),0,16);
            $outfile = Kohana::$config->load('minify.path.media') . $name . $build . '.' . $this->type;
            if (!is_file($outfile)) {
                if (!is_array($files) && 1 == 2)
                    $files = array($files);
                $files = array_unique($files);
                $output = '';
                foreach ($files as $file) {

                    if (stripos($file, '/') === 0) {
                        $this->filepath = mb_substr($file, 1);
                        $this->file = $file;
                    } else {
                        $this->file = Kohana::$config->load('minify.path.' . $this->type) . $file;
                        $this->filepath = Kohana::$config->load('minify.path.' . $this->type) . $file;
                    }

//                    echo 'ОРИГ: ' . $file . '<br> FILE: ' . $this->file . '<br>';
//                    echo "".substr($this->filepath, 0, strlen($this->filepath) - strpos($this->filepath, '?'))."<br>";

                    if (strpos($this->filepath, '?') != 0)
                        $this->filepath = substr($this->filepath, 0, strpos($this->filepath, '?'));

//                    echo 'PLACE: ' . $this->filepath . ' = ' . is_file($this->filepath) . '<br><br>';
                    if (is_file($this->filepath)) {
                        $this->set(file_get_contents($this->filepath));
                        $output .= "/*" . $this->filepath . "*/\r\n" . $this->min() . "\r\n";
                    }
//                     echo "<br><hr><br>";
                }
//                die();
                // write minified file 
//                $output .=  "\r\n\r\n/*".count($files)."*/\r\n";
                $f = fopen($outfile, 'w');
                if ($f) {
                    fwrite($f, $output);
                    fclose($f);
                }
                if (Kohana::$config->load('minify.gz', true)) {
                    $gzdata = gzencode($output, 5);
                    file_put_contents($outfile . '.gz', $gzdata);
                }
            }
            return array($outfile);
        } else
            return $files;
    }

    // text an minifier bergeben (per member variable)
    public function set($input) {
        $this->input = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
        return $this;
    }

    // text komprimieren (abgeleitete Klasse)
    public function min() {
        return $this->input;
    }

    public function clearCache() {
        $path = Kohana::$config->load('minify.path.media');
//        var_dump(glob($path."minify_*.css"));
//        die();
array_map("unlink", glob($path."minify_*.css"));
array_map("unlink", glob($path."minify_*.css.gz"));
array_map("unlink", glob($path."minify_*.js"));
array_map("unlink", glob($path."minify_*.js.gz"));

//        unlink($path . '*.js');
//        unlink($path . '*.css');
//        unlink($path . '*.js.gz');
//        unlink($path . '*.css.gz');
    }

}
