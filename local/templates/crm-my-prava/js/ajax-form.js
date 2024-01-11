$(function() {
  console.log('ajaaaax')
  $('.ajax').each(function() {
    $(this).on('change', function() {
      console.log( $(this).closest('.form-ajax'))
      $(this).closest('.form-ajax').submit()
    })
  })

  $('.form-ajax').each(function() {
    $(this).on('submit', function(e) {
      var $form = $(this);
      $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: $form.serialize()
      }).done(function(response) {
        console.log('Success')
      }).fail(function() {
        console.log('Fail');
      });

      e.preventDefault(); 
    })
  })
})