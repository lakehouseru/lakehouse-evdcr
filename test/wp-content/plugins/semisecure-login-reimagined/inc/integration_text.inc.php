<?php defined('ABSPATH') OR die(); ?>

<p>
  <?php _e('By default, this plugin will encrypt your password on the <code>wp-login.php</code>, <code>wp-admin/profile.php</code>, <code>wp-admin/user-edit.php</code>, and <code>wp-admin/user-new.php</code> pages. Here is a quick guide showing how you can integrate with this plugin to encrypt passwords on other pages too (most likely via a plugin).', $this->text_domain); ?>
</p>
<p>
  <?php _e('<em>Note: v3 of this plugin has made some (breaking) changes from the v2 integration</em>', $this->text_domain); ?>
</p>
<p>
  <?php _e('For starters, let\'s assume you have a form like the following (this only works for forms where <code>method="post"</code>):', $this->text_domain); ?>
</p>
<pre style="border:1px solid #000;margin:15px;overflow:auto;padding:5px;color:#000;background-color:#F9F9F9;">
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;form</span> <span style="color: #000066;">id</span>=<span style="color: #ff0000;">&quot;loginform&quot;</span> <span style="color: #000066;">method</span>=<span style="color: #ff0000;">&quot;post&quot;</span> <span style="color: #000066;">action</span>=<span style="color: #ff0000;">&quot;&quot;</span><span style="color: #000000; font-weight: bold;">&gt;</span></span>
  <span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;input</span> <span style="color: #000066;">type</span>=<span style="color: #ff0000;">&quot;password&quot;</span> <span style="color: #000066;">id</span>=<span style="color: #ff0000;">&quot;user_pwd&quot;</span> <span style="color: #000066;">name</span>=<span style="color: #ff0000;">&quot;pwd&quot;</span> <span style="color: #000000; font-weight: bold;">/&gt;</span></span>
  <span style="color: #808080; font-style: italic;">&lt;!-- <?php _e('Other form elements', $this->text_domain); ?> --&gt;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;/form<span style="color: #000000; font-weight: bold;">&gt;</span></span></span>
</pre>
<p>
  <?php _e('The basics of encrypting are as follows:', $this->text_domain); ?>
</p>
<pre style="border:1px solid #000;margin:15px;overflow:auto;padding:5px;color:#000;background-color:#F9F9F9;">
<span style="color: #000000; font-weight: bold;">&lt;?php</span>
  <span style="color: #666666; font-style: italic;">// <?php _e('Make sure that all the external JavaScript is available (including jQuery)', $this->text_domain); ?></span>
  <span style="color: #666666; font-style: italic;">// <?php _e('Optionally, pass TRUE into this method if the page doesn\'t automatically call wp_print_scripts', $this->text_domain); ?></span>
  SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">enqueue_js</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
<span style="color: #000000; font-weight: bold;">?&gt;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;script</span> <span style="color: #000066;">type</span>=<span style="color: #ff0000;">&quot;text/javascript&quot;</span><span style="color: #000000; font-weight: bold;">&gt;</span></span>
jQuery<span style="color: #009900;">&#40;</span>document<span style="color: #009900;">&#41;</span>.<span style="color: #660066;">ready</span><span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">function</span><span style="color: #009900;">&#40;</span>$<span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
  <span style="color: #006600; font-style: italic;">// <?php _e('Bind to the form\'s submit event', $this->text_domain); ?></span>
  $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'form#loginform'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">submit</span><span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">function</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
    <span style="color: #006600; font-style: italic;">// <?php _e('Collect the password(s) and form name(s)', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> password <span style="color: #339933;">=</span> $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">val</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> <span style="color: #000066;">name</span> <span style="color: #339933;">=</span> <span style="color: #3366CC;">'pwd'</span><span style="color: #339933;">;</span>

    <span style="color: #006600; font-style: italic;">// <?php _e('Pass the needed PHP values over to the JavaScript side', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> public_n <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">public_n</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> public_e <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">public_e</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> uuid <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">uuid</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> nonce_js <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">nonce_js</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> max_rand_chars <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">max_rand_chars</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> rand_chars <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> <span style="color: #990000;">addslashes</span><span style="color: #009900;">&#40;</span>SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">rand_chars</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> secret_key_algo <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">secret_key_algo</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>

    <span style="color: #006600; font-style: italic;">// <?php _e('Encrypt the password(s)', $this->text_domain); ?></span>
    <span style="color: #006600; font-style: italic;">// <?php _e('This function will return an Array on success or FALSE on failure', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> arr <span style="color: #339933;">=</span> SemisecureLoginReimagined.<span style="color: #660066;">encrypt</span><span style="color: #009900;">&#40;</span>password<span style="color: #339933;">,</span> <span style="color: #000066;">name</span><span style="color: #339933;">,</span> nonce_js<span style="color: #339933;">,</span> public_n<span style="color: #339933;">,</span> public_e<span style="color: #339933;">,</span> uuid<span style="color: #339933;">,</span> secret_key_algo<span style="color: #339933;">,</span> rand_chars<span style="color: #339933;">,</span> max_rand_chars<span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>

    <span style="color: #000066; font-weight: bold;">if</span> <span style="color: #009900;">&#40;</span>arr<span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
      <span style="color: #006600; font-style: italic;">// <?php _e('Loop through the array and append the controls to the form', $this->text_domain); ?></span>
      <span style="color: #000066; font-weight: bold;">for</span> <span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">var</span> i <span style="color: #339933;">=</span> <span style="color: #CC0000;">0</span><span style="color: #339933;">;</span> i <span style="color: #339933;">&lt;</span> arr.<span style="color: #660066;">length</span><span style="color: #339933;">;</span> i<span style="color: #339933;">++</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
        $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'form#loginform'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">append</span><span style="color: #009900;">&#40;</span>arr<span style="color: #009900;">&#91;</span>i<span style="color: #009900;">&#93;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
      <span style="color: #009900;">&#125;</span>

      <span style="color: #006600; font-style: italic;">// <?php _e('Finally, don\'t submit the plain-text password(s)', $this->text_domain); ?></span>
      <span style="color: #006600; font-style: italic;">// <?php _e('One option is to submit asterisks in place of the actual password', $this->text_domain); ?></span>
      <span style="color: #003366; font-weight: bold;">var</span> temp <span style="color: #339933;">=</span> <span style="color: #3366CC;">''</span><span style="color: #339933;">;</span>
      <span style="color: #000066; font-weight: bold;">for</span> <span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">var</span> i <span style="color: #339933;">=</span> <span style="color: #CC0000;">0</span><span style="color: #339933;">;</span> i <span style="color: #339933;">&lt;</span> password.<span style="color: #660066;">length</span><span style="color: #339933;">;</span> i<span style="color: #339933;">++</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span> temp <span style="color: #339933;">+=</span> <span style="color: #3366CC;">'*'</span><span style="color: #339933;">;</span> <span style="color: #009900;">&#125;</span>
      $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">val</span><span style="color: #009900;">&#40;</span>temp<span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
      <span style="color: #006600; font-style: italic;">// <?php _e('Another option is to disable the control(s) with the plain-text password(s) altogether', $this->text_domain); ?></span>
      $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">attr</span><span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'disabled'</span><span style="color: #339933;">,</span> <span style="color: #3366CC;">'true'</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
    <span style="color: #009900;">&#125;</span>
  <span style="color: #009900;">&#125;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
<span style="color: #009900;">&#125;</span><span style="color: #009900;">&#41;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;/script<span style="color: #000000; font-weight: bold;">&gt;</span></span></span>
</pre>
<p>
  <?php _e('After WordPress\' <code>init</code> hook has run, <code>$_POST[\'pwd\']</code> will contain the decrypted password.', $this->text_domain); ?>
</p>
<p>
  <?php _e('It\'s possible to pass multiple <em>passwords</em> and <em>names</em> into the <code>SemisecureLoginReimagined.encrypt</code> function. Instead of passing a single string, you can pass an array of strings. Just make sure that the password values match up with the names by keeping the elements in the same respecitve order.', $this->text_domain); ?>
</p>
<p>
  <?php _e('When creating your own integration, you might want to verify that this plugin has been activated. You can do that by using PHP\'s <code>method_exists</code> function. You can also compare the current version of this plugin by calling the <code>SemisecureLoginReimagined::version()</code> method.', $this->text_domain); ?>
</p>
<p>
  <?php _e('Two helper functions are available: <code>SemisecureLoginReimagined::is_rsa_key_ok()</code> and <code>SemisecureLoginReimagined::is_openssl_avail()</code>. These can be used to show an appropriate message to the user (both need to be true for this plugin to function properly).', $this->text_domain); ?>
</p>
<p>
   <?php _e('For some complete examples, check out <code>inc/login_head.inc.php</code> and <code>inc/admin_head.inc.php</code> included with this plugin.', $this->text_domain); ?>
</p>
