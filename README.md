# Kohana 3.3 Minify Module 

Simple Minify wrapper for Kohana 3. Yet basically tailored to suit my needs in a specific project.

## Usage (original)

    // Minify given string / file contents
    $min = Minify::factory('js')->set( $filecontents )->min();
    $min = Minify::factory('css')->set( $filecontents )->min(); 

    // Minify list of files; 
	//write result into media folder as defined in config
	// use md5 hash of filelist and apps build number as filename
	
    Controller::after()  
    $this->template->jsFiles = Minify::factory('js')->minify( $this->template->jsFiles, $build );
    $this->template->cssFiles = Minify::factory('css')->minify( $this->template->cssFiles, $build );
    
## Как делаю я
### в application/config/minify.php

<?
return array(
    'enabled' => true,
    'gz' => true,
    'path' => array(
        'js' => '', // дополнительный путь к js. Например, все js храняться в папке /assets/ или на другом сайте http://site.com/assets/, 
        //тогда можно при добавлении файла не указывать этот путь
        'css' => '', // дополнительный путь к css
        'media' => 'assets/cache/', // куда будут размещены готовые скрипты
    ),
    'url' => array(
        'js' => '/', //  
        'css' => '/', // url добавляется к относительным путям в свойствах url()
        'media' => '/', // url к сгенерированным скриптам.
    ),
);
?>


### в controller
	$this->template->styles[] = 'assets/libs/jcrop/css/jquery.Jcrop.css';
	$this->template->styles[] = 'assets/libs/bootstrap/css/bootstrap.css';
        
	$this->template->scripts[] = 'assets/js/jquery.min.js';
	$this->template->scripts[] = 'assets/libs/bootstrap/js/bootstrap.js';
        
### в view
        $js = Minify::factory('js')->minify($scripts);
        echo HTML::script($js[0]);
        $cs = Minify::factory('css')->minify($styles);
        echo HTML::style($cs[0]);

## Credits

BASED ON THE JSMIN CODE BY rgrove: http://code.google.com/p/jsmin-php 

BASED ON THE CSSMIN CODE BY joe.scylla: http://code.google.com/p/cssmin

BASED ON THE Kohana 2 Minify Driver by Tom Morton 


## Update
2013.11.19 
- Изменен имя генерируемых файлов. Теперь minify_md5().build.
- удаление сгенерированных файлов
- сжатие gz сгенерированных файлов
- возможность указывать внешний url для сгенерированных файлов
- изменение путей в css-файлах в зависимости от расположение css-файла
- в генерируемом css указаны файлы, которые были слиты
