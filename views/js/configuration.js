


//$(() => {
  $(document).ready(function() {    

    // initialize the Router component in PS 1.7.8+ 
    if (typeof window.prestashop.component !== 'undefined') { window.prestashop.component.initComponents(['Router']); }
   
    // initiate the search on button click
    $(document).on('click', '#refresh_placeid', () => getPlaceid($('#demo_search_customer').val()));

    
    $.urlParam = function(name){
      var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);

      //alert(window.location.href);
      return results[1] || 0;
    }

    function getPlaceidxxx() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'ajax-tab.php',
            data: {
                ajax: true,
                controller: 'AdminFooBar',
                action: 'youraction', // small letter case
                var1: your_variable, // if you want to send some var
                token: $('#your_DOM_identifier').attr('data-token'), // your token previously set in your DOM element
            },
        })
        .done(function (data) {

        })    
    }   

    function getPlaceid(){

      var res = '';
      var address_search = $('#MYGGOGLEREVIEWS_ADDRESS').val();
      var token_api = $('#MYGGOGLEREVIEWS_GOOGLE_TOKEN').val();
      var ajaxurl = $('#MYGGOGLEREVIEWS_AJAX_ROUTE').val();

      $('#refresh_placeid').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp Loading...');
      if (address_search.length > 0) {

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {'establishment_address' : address_search, 'token_api' : token_api},
              dataType: "json",
          })
          .done(
            function (response) { 
            
                data =jQuery.parseJSON(response);
                //console.log(data);
                //alert(data)
                res = data['results'][0]['place_id'];
                $('#MYGGOGLEREVIEWS_GOOGLE_PLACEID').val(res);
                $('#refresh_placeid').html('Get Place ID');

                btclass = $('#module_form_submit_btn').attr('class');
                if (res!=''){
                    $('#module_form_submit_btn').attr('class', btclass.replace('disabled',''));
                    alert("Great! You can now register.");
                }
             }
          )
          .fail(function (jqXHR, textStatus, errorThrown) { 
            $('#error_mess').append(jqXHR);
            $('#error_mess').append(textStatus);
            $('#error_mess').append(errorThrown);
          });
      } else {
          $('#result').html('');        
      }
    }

  }
);

  