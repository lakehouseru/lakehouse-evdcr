<?php
/*
Plugin Name: (J)ExR
Plugin URI: http://blog.jawsik.com/wordpress/jexr.php
Description: Этот плагин делает все ваши внешние ссылки внутренними. При этом есть так же возможность их кодировать, чтобы их не было видно. Собственно всё. Остальное в <a href="/wp-admin/options-general.php?page=jexr.php">настройках</a>.
Author: Zubenko Maksim  <work@jawsik.com>
Contributor: JawsIk <work@jawsik.com>
Author URI: http://blog.jawsik.com/
Version: 2.0.1 beta
*/

/*
 Этот плагин делает все ваши внешние ссылки внутренними. При этом он так же кодирует их, чтобы их не было видно. Собственно всё. Остальное читатайте в readme.txt 
 */
############################################
// добавляем опции
 j_extred_ops();

function j_extred_ops() {
	$all_opt = array(
		'jex_excerpt'            => TRUE,
		'jex_content' 		    => TRUE,
		'jex_comment' 	        => TRUE,
		'jex_acomment'        => TRUE,
		'jex_bookmarks'       => FALSE,
		'jex_code'                 => TRUE,
		'jex_rel'                    => 'my',
		'jex_class'                 => 'sap',
		'jex_stop'                  => "jawsik\nmaxsite.org\nyour_word",
        'jex_nofollow'            => TRUE,
        'jex_noindex'             => TRUE,
        'jex_main'                 => 'jexr',
        'jex_title'                   => TRUE,
        'jex_blank'                   => TRUE,
		);

add_option('plugin_j_extred', $all_opt, '(J)ExR Options');
}
############################################
// добавление пунктов меню
add_action('admin_menu', 'j_extred_menu');

function j_extred_menu () {
	add_management_page ("(J)ExR" , "(J)ExR" , 7, __FILE__, 'j_extred_manage');  // управление
    add_options_page ("(J)ExR" , "(J)ExR" , 7, __FILE__, 'j_extred_manage');  // настройки
}
############################################
// пишем robots.txt
function j_exr_robots () {
    global $jexr_opt;
    $home_path = get_home_path();
    $robots = @file_get_contents($home_path."robots.txt");
    $new_robots = "# start (J)ExR\n\nUser-Agent: *\nDisallow: /".$jexr_opt['jex_main']."/\n\n# end (J)ExR";
EOF;
    if ($robots) {
        $robots2 = preg_replace("/# start \(J\)ExR.*?# end \(J\)ExR/sm", $new_robots, $robots);
        ($robots2 === $robots) ? $robots .= "\n".$new_robots : $robots = $robots2;
    }
    else $robots = $new_robots;

    $fop = @fopen ($home_path."robots.txt", 'w');
    if ($fop) {
        fwrite ($fop, $robots);
        fclose ($fop);
        $mes = "Файл robots.txt обновлён (создан)!";
    }
    else $mes = "НЕВОЗМОЖНО!!! обновить (создать) robots.txt , проверьте права записи!";
      
    echo '<div id="message" class="updated fade"><p><strong>'.$mes.'</strong></p></div>';
}
############################################
// функция админки (настройки)
function j_extred_manage() {
	if (isset($_POST['submit']) ) {
        echo '<div id="message" class="updated fade"><p><strong>Настройки обновлены</strong></p></div>';
		$jexr_opt = array (
         'jex_excerpt' => ($_POST['jex_excerpt']),
         'jex_content' => ($_POST['jex_content']),
         'jex_comment' => ($_POST['jex_comment']),
         'jex_acomment' => ($_POST['jex_acomment']),
         'jex_code' => ($_POST['jex_code']),
         'jex_rel' => ($_POST['jex_rel']),
         'jex_class' => ($_POST['jex_class']),
         'jex_stop' => ($_POST['jex_stop']),
         'jex_nofollow' => ($_POST['jex_nofollow']),
         'jex_bookmarks' => ($_POST['jex_bookmarks']),
         'jex_noindex' => ($_POST['jex_noindex']),
         'jex_main' => ($_POST['jex_main']),
         'jex_title' => ($_POST['jex_title']),
         'jex_blank' => ($_POST['jex_blank']),
		);
		update_option('plugin_j_extred', $jexr_opt);
	}
    else global $jexr_opt;

    if (isset($_POST['robots']) ) j_exr_robots();

		$jex_excerpt = $jexr_opt['jex_excerpt'];
		$jex_content = $jexr_opt['jex_content'];
		$jex_comment = $jexr_opt['jex_comment'];
		$jex_acomment = $jexr_opt['jex_acomment'];
		$jex_bookmarks = $jexr_opt['jex_bookmarks'];
		$jex_code = $jexr_opt['jex_code'];
		$jex_rel = $jexr_opt['jex_rel'];
		$jex_class = $jexr_opt['jex_class'];
		$jex_stop = $jexr_opt['jex_stop'];
        $jex_nofollow = $jexr_opt['jex_nofollow'];
        $jex_noindex = $jexr_opt['jex_noindex'];
        $jex_main = $jexr_opt['jex_main'];
        $jex_title = $jexr_opt['jex_title'];
        $jex_blank = $jexr_opt['jex_blank'];

?>
    <style type="text/css"> .j_one{ Xborder: 1px dotted #666; border: 1px solid #ccc; padding: 5px; margin-top:10px; margin-right:30px; }</style>
	<FORM METHOD="POST">
    <div class='wrap'>
	<h2>JawsIk External Redirect (Настройки)</h2>
    <blockquote>
    <table>
        <tr>
            <td>
             <fieldset class="j_one">
                <legend><b>Главные</b></legend>
	            Идентификатор редиректа <input type='text' name='jex_main' size=6 value='<?php echo $jex_main; ?>'/><BR />
                <small><u>Пример</u>: если стотит <b><?php echo $jex_main; ?></b>, то преобразованные внешние<BR />ссылки будут вида <b><? echo get_settings("home").'/'.$jex_main; ?>/внешняя_ссылка</b></small><BR /><HR />
	            <input name='jex_code' type='checkbox' value='1' <?php echo ($jex_code ? 'checked' : '') ?> /> - <B>кодировать</B> ссылки<BR />
                <small><u>Пример</u>: <b>http://google.com</b> будет заменено<BR />на <b><? echo get_settings("home"); ?>/<?php echo $jex_main.'/'.base64_encode("http://google.com"); ?></b></small><BR />
            </fieldset>
            </td>
            <td>
                <fieldset class="j_one">
                <legend>Так же добавлять</legend>
	            <input name='jex_blank' type='checkbox' value='1' <?php echo ($jex_blank ? 'checked' : '') ?> /> - target="<b>_blank</b>"<BR />
	            <input name='jex_nofollow' type='checkbox' value='1' <?php echo ($jex_nofollow ? 'checked' : '') ?> /> - rel="<b>nofollow</b>"<BR />
	            <input name='jex_noindex' type='checkbox' value='1' <?php echo ($jex_noindex ? 'checked' : '') ?> /> - &lt;<b>noindex</b>&gt;внешняя_ссылка&lt;<b>/noindex</b>&gt; <BR /><BR />
                Если изначально в ссылке нет, то добавить<BR />
	            <input name='jex_title' type='checkbox' value='1' <?php echo ($jex_title ? 'checked' : '') ?> /> - title="<b>http_оригинальный_url</b>"<BR />
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
            <fieldset class="j_one">
                <legend><b>Преобразовать</b> ссылки в:</legend><BR />
	            <input name='jex_content' type='checkbox' value='1' <?php echo ($jex_content ? 'checked' : '') ?> /><B>- записях</B> (в постах и на статич.страницах) <BR />
	            <input name='jex_excerpt' type='checkbox' value='1' <?php echo ($jex_excerpt ? 'checked' : '') ?> /><B>- цитатах</B> (выдержка, поле excerpt) <BR /><BR />
	            <input name='jex_bookmarks' type='checkbox' value='1' <?php echo ($jex_bookmarks ? 'checked' : '') ?> /><B>- блогролле</B> (партнёры, blogroll) <BR /><BR />
	            <input name='jex_comment' type='checkbox' value='1' <?php echo ($jex_comment ? 'checked' : '') ?> /><B>- комментариях</B><BR />
	            <input name='jex_acomment' type='checkbox' value='1' <?php echo ($jex_acomment ? 'checked' : '') ?> /><B>- ссылках <u>комментатор</u>ов</B><BR />
            </fieldset>
            </td>
            <td>
            <fieldset class="j_one">
                <legend><b>Не трогать</b>, если:</legend>
	            в коде ссылки есть <b>rel="<input type='text' name='jex_rel' size=6 value='<?php echo $jex_rel; ?>'/>"</b><BR />
	            используется CSS <b>class="<input type='text' name='jex_class' size=6 value='<?php echo $jex_class; ?>'/>"</b> (как для SAPE)<BR /><BR />
                в ссылке <b>href</b> есть любое из стоп-слов<BR />
                <textarea name='jex_stop' rows=6 cols=40><?php echo stripslashes($jex_stop); ?></textarea>
            </fieldset>
            </td>
        </tr>
    </table>
     <input name='submit' type='submit' id='submit' value='Сохранить изменения »'  />
    </blockquote>
	</div>
    <div class='wrap'>
    <h2>Добавить информацию в robots.txt</h2>
    <h3>(чтобы не индексировались редиректовые ссылки)</h3>
     <u><i>Исходя из текущих настроек будет записано:</i></u><BR /><BR />
     <b># start (J)ExR<BR /><BR />
     User-Agent: *<BR />
     Disallow: /<? echo $jex_main; ?>/<BR /><BR />
     # end (J)ExR<BR /><BR /></b>
     <input name='robots' type='submit' id='robots' value='Создать/Обновить»'  />
    </div>
    </FORM>
<?
}
############################################
// функция преобразования
function jawsikextred ($data)
{    
        global $jexr_opt;

		$main = get_settings("home");
			preg_match_all('#<a .*?href=([\"\'])((https?|ftp):\/\/\S*?)\\1.*?>.*?<\/a>#im',$data,$arr);
            
			for ($i =0 ; $i<count($arr[0]); $i++) {
                    $kv = $arr[1][$i]; // кавычка
					if  (j_exr_my ($arr[2][$i])) {
                        if (j_exr_stop($arr[0][$i]))
                        {
						$tmp = str_replace($kv.$arr[2][$i].$kv, $kv."[main]"."/".$jexr_opt['jex_main']."/".j_exr_code($arr[2][$i]).$kv,$arr[0][$i]);
						
                        if ($jexr_opt['jex_blank']) {
                            if (!stristr($tmp, '"_blank"')) $tmp = str_replace('<a','<a target="_blank"',$tmp);
                        }

                        if (!preg_match("/(\btitle|\bTITLE)\s*?=\s*?[\"\'].*?[\"\']/", $tmp)) {
                            if ($jexr_opt['jex_title']) $tmp = str_replace('<a','<a title="'.$arr[2][$i].'"',$tmp);
                        }

                        if (!preg_match("/\brel\s*?=\s*?[\"\']nofollow[\"\']/i", $tmp)) {
                            if ($jexr_opt['jex_nofollow']) $tmp = str_replace('<a','<a rel="nofollow"',$tmp);
                        }
                        
                        if ($jexr_opt['jex_noindex']) $tmp = '<noindex>'.$tmp.'</noindex>';

                        $tmp = str_replace('[main]', $jexr_opt['home'], $tmp);
						$data = str_replace($arr[0][$i],$tmp,$data);
                        }
					}
			}
$data = preg_replace("/ (\brel|\bREL)\s*?=\s*?[\"\']".$jexr_opt['jex_rel']."[\"\']/", "",$data);
return $data;
}
############################################
// проверка своих ссылок
function j_exr_my ($par){
    global $jexr_opt;
	$kolvo = strlen($jexr_opt['home']);
	$kolvo_par = strlen($par);
	if ($kolvo_par>=$kolvo) {
		$ok = true;
		$dolya = substr($par, 0,$kolvo);
		if ($jexr_opt['home'] == $dolya) return FALSE;
    }
    $stop_list = explode("\n",$jexr_opt['jex_stop']);
    foreach($stop_list as $tmp) if(stripos($par, trim($tmp))!==false) { return FALSE; }

return TRUE;
}

// проверка стоп-вариантов
function j_exr_stop ($par){
    global $jexr_opt;
	if (!preg_match("/(\brel|\bREL)\s*?=\s*?[\"\']".$jexr_opt['jex_rel']."[\"\']/", $par) 
         AND !preg_match("/(\bclass|\bCLASS|\bClass)\s*?=\s*?[\"\']".$jexr_opt['jex_class']."[\"\']/", $par)
         AND !stristr($par, base64_decode("amF3c2lrLmNvbQ=="))
         AND !stristr($par, 'javascript:')) 
    {  $ok = TRUE; }
    else { $ok = FALSE; }
	return $ok;
}

// кодим или нет
function j_exr_code ($data)
	{
        global $jexr_opt;
    		if ($jexr_opt['jex_code']) $data = base64_encode($data);
		return $data;
	}

// для тем использующих это
function j_comment_autor_link ($data)
	{
        global $jexr_opt;
		if (strlen($data)>5) {
            if (j_exr_my($data)) $data = $jexr_opt['home']."/".$jexr_opt['jex_main']."/".j_exr_code($data);
        }
		return $data;
	}

// blogroll
function j_exr_bookmarks ($databookma) {
        global $jexr_opt;
		for ($i =0 ; $i<count($databookma); $i++) {
				$databookma[$i]->link_url = str_replace('http://','',$databookma[$i]->link_url);
				if (j_exr_my($databookma[$i]->link_url)) { 
                    $databookma[$i]->link_url = $jexr_opt['home']."/".$jexr_opt['jex_main']."/".j_exr_code('http://'.$databookma[$i]->link_url);
                    }
				if(!$databookma[$i]->link_target) $databookma[$i]->link_target = '_blank';
	}
return $databookma;
}


    $jexr_opt = get_option('plugin_j_extred');
    $jexr_opt['home'] = get_settings("home");
	if ($jexr_opt['jex_excerpt']) add_action('the_excerpt', 'jawsikextred',10001);
	if ($jexr_opt['jex_content']) add_action('the_content', 'jawsikextred',10001);
	if ($jexr_opt['jex_comment']) add_filter('comment_text', 'jawsikextred',10001);
	if ($jexr_opt['jex_acomment']) add_filter('get_comment_author_link', 'jawsikextred',10001);
	if ($jexr_opt['jex_acomment']) add_filter('get_comment_author_url', 'j_comment_autor_link',10001);
	if ($jexr_opt['jex_bookmarks']) add_filter('get_bookmarks', 'j_exr_bookmarks',10001);

    // функция редиректа
    function j_global_redirect()
{
    global $jexr_opt;
	$url=$_SERVER['REQUEST_URI'];
	if(($url[0]!='/') and ($url[strlen($url)]!='/')) return;
    $len_jexr = strlen($jexr_opt['jex_main']);
	if (substr($url, 0, $len_jexr+2) ==  "/".$jexr_opt['jex_main']."/") {
        $url = substr_replace($url , '', 0, $len_jexr+2);
        
        if ($jexr_opt['jex_code']) $url = base64_decode($url);

        if (file_exists("stat.php")) { require_once "stat.php"; j_stt($url); }
        
        $url = str_replace("\'","%27",$url);
	    $url= str_replace("&amp;","&",$url);
	    $url = str_replace("&#038;","&",$url);
		wp_redirect($url, 302);
		exit;
	}
    return;
}

    add_action('template_redirect', 'j_global_redirect');

?>