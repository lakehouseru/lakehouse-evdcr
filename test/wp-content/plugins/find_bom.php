<? /*

���� ������ ���� �����, ����������� � BOM.
�������������: 
1. ������ �� ������ � �������� ���������� �����
2. � �������� ������ �������� ������� http://���.����/find_bom.php

���� WordPress ��������� �� � ����� �����, �� ������ ����� �������� � ����������,
��� ����� WordPress, � �� ��� �� � ���������.

��� ���������� �������� ������ ����������� ������ �� ����������, 
� ������� ������ ������������ ������ ���� �����.

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
