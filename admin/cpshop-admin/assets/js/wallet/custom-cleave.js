$(function(){

  $('.card-input').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numericOnly: true,
      delimiter: '-',
      blocks: [4, 4, 4, 4]
    });

  });

  $('.number-input').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      delimiter: ""
    });

  });

  $('.number-input-2').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      numeralIntegerScale: 3,
      delimiter: ""
    });

  });

  $('.number-input-3').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      numeralIntegerScale: 1,
      numeralDecimalScale: 1,
      delimiter: ""
    });

  });

  $('.number-input-4').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      numeralPositiveOnly: true,
      numeralIntegerScale: 11,
      delimiter: "",
      onValueChanged: function(e){
        // console.log(e);
        field.setAttribute('data-raw', e.target.rawValue);
      }
    });
  });

  $('.money-input').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      block:[3],
      delimiter: ",",
      numeralPositiveOnly: true,
      onValueChanged: function(e){
        // console.log(e);
        field.setAttribute('data-raw', e.target.rawValue);
      }
    });
  });

  $('.currency-input').toArray().forEach(function(field){
    var cleave = new Cleave(field, {
      numeral: true,
      numeralDecimalScale: 6,
      block:[3],
      delimiter: ",",
      onValueChanged: function(e){
        // console.log(e);
        field.setAttribute('data-raw', e.target.rawValue);
      }
    });
  });

  // $('.time-picker').toArray().forEach(function(field){
  //   var cleave = new Cleave(field, {
  //     time: true,
  //     timePattern: ['h', 'm'],
  //     onValueChanged: function(e){
  //       // console.log(e);
  //       field.setAttribute('data-raw', e.target.rawValue);
  //     }
  //   });
  // });

});
