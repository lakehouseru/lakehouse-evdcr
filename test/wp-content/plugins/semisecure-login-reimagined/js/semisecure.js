jQuery(document).ready(function($) {
  // jsbn recommends the following
  $(window).click(jsbn_rng_seed_time);
  $(window).keypress(jsbn_rng_seed_time);
})

var SemisecureLoginReimagined = {
  /**
   * @param string|Array passwords (passwords and names should have the same number of elements)
   * @param string|Array names     (and be passed in the same relative order)
   * @param string nonce_js        (the nonce or the async URL where we can grab the nonce from)
   * @param string public_n
   * @param string public_e
   * @param string uuid
   * @param string secret_key_algo
   * @param string rand_chars
   * @param int max_rand_chars
   * @return Array|false
   */
  encrypt: function(passwords, names, nonce_js, public_n, public_e, uuid, secret_key_algo, rand_chars, max_rand_chars) {
    // if not an array, then transform the string into an array
    if (passwords.constructor != Array) {
      var temp_password = passwords;
      passwords = [];
      passwords[0] = temp_password;
    }

    if (names.constructor != Array) {
      var temp_name = names;
      names = [];
      names[0] = temp_name;
    }
    
    if (passwords.length === names.length) {
      var nonce = nonce_js;
      if (nonce_js.toLowerCase().substring(0, 7) == 'http://' || nonce_js.toLowerCase().substring(0, 8) == 'https://') {
        jQuery.ajax({
          url: nonce_js,
          cache: false,
          async: false,
          success: function(data) {
            nonce = data;
          }
        });
      }

      var rsa = new jsbn_RSAKey();
      rsa.setPublic(public_n, public_e);

      var arr = [];
      var return_arr = true;

      for (var i = 0; i < names.length; i++) {
        var secret_key = this.secret_key(rand_chars, max_rand_chars);
        var rand_pad = this.rand_pad(rand_chars); // disguise message length
        var to_be_encrypted = nonce + rand_pad + passwords[i];

        var encrypted;
        if (secret_key_algo == 'aes-cbc') {
          encrypted = Crypto.AES.encrypt(to_be_encrypted, secret_key, { mode: Crypto.mode.CBC });
        }
        else if (secret_key_algo == 'aes-ofb') {
          encrypted = Crypto.AES.encrypt(to_be_encrypted, secret_key, { mode: Crypto.mode.OFB });
        }
        else if (secret_key_algo == 'rabbit') {
          encrypted = Crypto.Rabbit.encrypt(to_be_encrypted, secret_key);
        }
        else { // marc4
          encrypted = Crypto.MARC4.encrypt(to_be_encrypted, secret_key);
        }

        var res = rsa.encrypt(secret_key);

        if (res && encrypted) {
          var rsaPwd = document.createElement('input');
          rsaPwd.setAttribute('type', 'hidden');
          rsaPwd.setAttribute('name', names[i] + '__' + uuid);
          rsaPwd.value = jsbn_hex2b64(res) + '|' + encrypted;

          arr[i] = rsaPwd;
        }
        else
          return_arr = false;
      }

      if (return_arr)
        return arr;
    }

    return false;
  },

  /**
   * @param string rand_chars
   * @return string
   */
  rand_pad: function(rand_chars) {
    var rand_dec = Math.floor(Math.random() * 256);
    var rand_hex = rand_dec.toString(16);
    if (rand_hex.length == 1) rand_hex = '0' + rand_hex;
    var rand_pad = rand_hex;
    for (var i = 0; i < rand_dec; i++) {
      var rand = Math.floor(Math.random() * rand_chars.length);
      rand_pad += rand_chars.substring(rand, rand+1);
    }
    return rand_pad;
  },

  /**
   * @param string rand_chars
   * @param int max_rand_chars
   * @return string
   */
  secret_key: function(rand_chars, max_rand_chars) {
    var secret_key = '';
    for (var i = 0; i < max_rand_chars; i++) {
      var rand = Math.floor(Math.random() * rand_chars.length);
      secret_key += rand_chars.substring(rand, rand+1);
    }
    return secret_key;
  }
}
