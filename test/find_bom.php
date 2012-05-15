<? /*

Этот скрипт ищет файлы, сохраненные с BOM.
Использование: 
1. залить на сервер в корневую директорию сайта
2. в адресной строке броузера набрать http://ваш.сайт/find_bom.php

Если WordPress находится не в корне сайта, то скрипт нужно положить в директорию,
где лежит WordPress, и из нее же и запускать.

Для увеличения скорости работы проверяются только те директории, 
в которые обычно пользователи кладут свои файлы.

*/

xdir('.',0);
xdir('./wp-content/themes',1);
xdir('./wp-content/plugins',1);

function xdir($path,$recurs) {
	global $find;
	if ($dir = @opendir($path)) {
		while($file = readdir($dir)) {
			if ($file == '.' or $file == '..') continue;
			$file = $path.'/'.$file;
			if (is_dir($file) && $recurs)  {
				xdir($file,1);
			}
			if (is_file($file) && strstr($file,'.php')) { 
				$f = fopen($file,'r');
				$t = fread($f, 3);
				if ($t == "\xEF\xBB\xBF") {
				 	$find = 1;
				 	echo "$file<br>\n";
				}
				fclose ($f);
			}
		}  
		closedir($dir);
 	}
}
if ($find == 0) echo "All clear";
?>
<hr>
&copy;<a href="http://blog.portal.kharkov.ua">Yuri Belotitsky</a>
