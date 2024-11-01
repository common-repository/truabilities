<?php
/**
 * Admin Ajax
 *
 **/
?>
<script type="text/javascript">
(function($) {

  /* Auth Submission */
  $('#truabAuthForm').submit(function(e) {
    e.preventDefault();
    var userName = $('#userName').val();
    var passWord = $('#passWord').val();
    var siteUrl = $('#site_url').val();

    $.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
          action: 'truab_get_user_auth',
          username: userName,
          password: passWord,
          url: siteUrl
        }, // data to submit
        beforeSend: function() {
          jQuery('.spinner-border').show();
        },
        complete: function() {
          jQuery('.spinner-border').hide();
        },
        success: function(response, status, xhr) {
          if (response.license_status == 'auth' || response.license_status == 'trial') {

            $('#truabAuthForm').hide();
            // Location Reload
            var url = window.location.href;
            if (url.indexOf('?') > -1) {
              url += '&authAda=true';
            } else {
              url += '?authAda=true';
            }
            window.location.href = url;
          } else {
            $('.alert-danger').show();
            $('.alert-danger').html(response.usrmsg);
          }
        },
        error: function(jqXhr, textStatus, errorMessage) {
          console.log(errorMessage);
        },
        dataType: 'json',
      }
    );
  });

  /* Deauthentication */
  $('.deauth-key').on('click', function() {
    if(confirm('Are you sure to deauthorize your widget license key?')) {
      var data = {
        action: 'deauth_truab_key',
        url: '<?php echo site_url(); ?>'
      }
      jQuery('.spinner-border').show();
      $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(resp){
        jQuery('.spinner-border').hide();
        if(resp.flag == true) {
          $('.alert-danger').hide();
          $('.alert-success').show();
          $('.alert-success').html(resp.usrmsg);
          window.location.href = resp.url;
        } else {
          $('.alert-success').hide();
          $('.alert-danger').show();
          $('.alert-danger').html(resp.usrmsg);
        }
      }, 'json');
    } else {
      return;
    }
  });

})(jQuery);

</script>