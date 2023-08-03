$(() => {
    // initialize the Router component in PS 1.7.8+ 
    if (typeof window.prestashop.component !== 'undefined') { window.prestashop.component.initComponents(['Router']); }
   
    // initiate the search on button click
    $(document).on('click', '#refresh_reviews', () => refresh_review("ChIJb4jLDFQDyhIR-N-AV2AKLqs"));
    
    $.urlParam = function(name){
      var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
      return results[1] || 0;
    }

    function refresh_review(placeid){

      if (placeid.length > 0) {
          //$('#establishment_id').val("");
          $('#refresh_reviews').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp Loading...');

          //console.log(placeid);
          $.ajax({
              type: "POST",
              url: adminlink_refresh_reviews,
              data: {'placeid' : placeid, 'id' : $('#refresh_reviews').attr( "value" )},
              dataType: "json",
          })
          .done(
            function (response) { 

              data = response;
              
              user_ratings_total = data['result']['user_ratings_total'];
              rating = data['result']['rating'];
              reviews = data['result']['reviews'];

              location.reload(true);

              // res = $data['results'][0]['place_id'];
              // $('#establishment_placeid').val(res);
              $('#refresh_reviews').html('Refresh');

              alert('Refresh successful! Page will be reloaded!')
             }
          )
          .fail(function (jqXHR, textStatus, errorThrown) { 
            objtoarray(jqXHR);
            objtoarray(errorThrown);
            alert(textStatus);
            $('#error_mess').append(jqXHR);
            $('#error_mess').append(textStatus);
            $('#error_mess').append(errorThrown);
            $('#refresh_reviews').html('Refresh');
            
            //location.reload(true);
          });
      } else {
          $('#result').html('');        
      }
    }

    function objtoarray(obj){
      var acc = [];
      $.each(obj, function (index, value) {
        acc.push(index +" : " + value);
      });
      console.log(JSON.stringify(acc) + textStatus + errorThrown);
    }
  }
);

  