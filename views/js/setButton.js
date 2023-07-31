// $(document).ready(function(){
//   //$('#establishment_address').on('keyup', function(){
//   $('#establishment_Refresh').on('click', function(){
//       //searchProduct();
//       search3();
//   })

//   search3();
// })


// function search3(){
//   var res = '';
//   var buscar = "MAQUERY"; //$('#establishment_address').val();
//   if (buscar.length > 0) {
//       $('#establishment_id').val("");
//       //console.log(buscar);
//       $.ajax({
//           type: "GET",
//           //url: "/admin045zchygu/index.php/modules/mygooglerewiews/ajax.php",
//           url: "/admin045zchygu/index.php/modules/configure-tabs/ajaxget?_token=aafvqHOtKIshd0W13HU5kp4rmfu8TAjL1h0NZvCqD58",
//           data: {'establishment_address' : buscar},
//           dataType: "json",
//           success: function(response){
//               console.log(response['results'][0]['place_id']);
//               res = response['results'][0]['place_id'];
//               // $.each(response, function(i, v) {
//               //     res += '<tr><td>'+v.id_product+'</td><td>'+v.name+'</td></tr>';
//               // })
//               //$('#result').html(res);
//               $('#establishment_id').val(res);
//           },
//       });
//   } else {
//       $('#result').html('');        
//   }
// }

$(() => {
    // initialize the Router component in PS 1.7.8+ 
    if (typeof window.prestashop.component !== 'undefined') { window.prestashop.component.initComponents(['Router']); }
   
    // initiate the search on button click
    $(document).on('click', '#establishment_Refresh', () => search3($('#demo_search_customer').val()));
    $(document).on('click', '#refresh_reviews', () => refresh_review("ChIJb4jLDFQDyhIR-N-AV2AKLqs"));
    //$(document).on('click', '#establishment_Refresh', () => test());

    
    $.urlParam = function(name){
      var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
      return results[1] || 0;
    }

    function search3(){
      var res = '';
      var address_search = "Krysakids, Mallemort"; //$('#establishment_address').val();
      //<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...
      $('#establishment_Refresh').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp Loading...');
      if (address_search.length > 0) {
          $('#establishment_id').val("");
          //console.log(buscar);
          $.ajax({
              type: "POST",
              //url: "/admin045zchygu/index.php/modules/mygooglerewiews/ajax.php",
              url: "/admin045zchygu/index.php/modules/configure-tabs/ajaxgetplaceid?_token="+$.urlParam('_token'), //+$('#establishment__token').val(),
              data: {'establishment_address' : address_search},
              dataType: "json",
          })
          .done(
            function (response) { 
                $data =jQuery.parseJSON(response);
                //console.log($data['results']);
                console.log(" ");
                res = $data['results'][0]['place_id'];
                $('#establishment_placeid').val(res);
                $('#establishment_Refresh').html('Refresh');
             }
          )
          .fail(function (jqXHR, textStatus, errorThrown) { 
            //alert(textStatus);
            $('#error_mess').append(jqXHR);
            $('#error_mess').append(textStatus);
            $('#error_mess').append(errorThrown);
          });
      } else {
          $('#result').html('');        
      }
    }

    function refresh_review(placeid){

      //alert(placeid);
      var res = '';
      //var address_search = "Krysakids, Mallemort"; //$('#establishment_address').val();
      //<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...
      //$('#establishment_Refresh').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp Loading...');
      if (placeid.length > 0) {
          //$('#establishment_id').val("");
          console.log(placeid);
          console.log($('#refresh_reviews').attr( "value" ));
          $.ajax({
              type: "POST",
              //url: "/admin045zchygu/index.php/modules/mygooglerewiews/ajax.php",
              url: adminlink_refresh_reviews, //"/admin045zchygu/index.php/modules/configure-tabs/ajaxgetreviews?_token="+$.urlParam('_token'), //+$('#establishment__token').val(),
              data: {'placeid' : placeid, 'id' : $('#refresh_reviews').attr( "value" )},
              dataType: "json",
          })
          .done(
            function (response) { 
                //data =jQuery.parseJSON(response);
                data = response;
                
                console.log(data['result']);
                user_ratings_total = data['result']['user_ratings_total'];
                rating = data['result']['rating'];
                reviews = data['result']['reviews'];

                //location.reload(true);

                // res = $data['results'][0]['place_id'];
                // $('#establishment_placeid').val(res);
                // $('#establishment_Refresh').html('Refresh');
             }
          )
          .fail(function (jqXHR, textStatus, errorThrown) { 
            //alert(textStatus);
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

  