$(document).ready(function() {  

    const carousel = $('.carousel');
    const seats = $('.carousel-seat');
    
    const next = function(el) { if (el.next().length > 0) { return el.next(); } else { return seats.first(); } };
    const prev = function(el) { if (el.prev().length > 0) { return el.prev(); } else { return seats.last(); } };
    
    $('.toggle').on('click', function(e) {
        console.log('you click toggle class');
      let new_seat;
      const el = $('.is-ref').removeClass('is-ref');
    
      if ($(e.currentTarget).data('toggle') === 'next') {
        new_seat = next(el);
        carousel.removeClass('is-reversing');
      } else {
        new_seat = prev(el);
        carousel.addClass('is-reversing');
      }
      
      new_seat.addClass('is-ref').css('order', 1);
      for (let i = 2, end = seats.length, asc = 2 <= end; asc ? i <= end : i >= end; asc ? i++ : i--) { new_seat = next(new_seat).css('order', i); }
        
      carousel.removeClass('is-set');
      return setTimeout((() => carousel.addClass('is-set')), 50);
    });

});