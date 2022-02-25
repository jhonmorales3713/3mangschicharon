$('#entry-city').tagsInput({

  // min/max number of characters
  minChars: 0,
  maxChars: null,

  // max number of tags
  limit: null,

  // RegExp
  validationPattern: null,

  // duplicate validation
  unique: true
  
});

$('#entry-city').tagsInput({

  'autocomplete': {
    source: [
      'jQuery',
      'Script',
      'Net',
      'Demo'
    ]
  }

});

$('#entry-city#tags').addTag('newTag');
$('#entry-city#tags').removeTag('newTag');
$('#entry-city#tags').importTags('newTag1, newTag2, newTag3');

$('.tagsinput#tags').tagsInput({
  interactive: true,
  placeholder: 'Add a tag',
  minChars: 2,
  maxChars: 20, // if not provided there is no limit
  limit: 5, // if not provided there is no limit
  validationPattern: new RegExp('^[a-zA-Z]+$'), // a pattern you can use to validate the input
  width: '300px', // standard option is 'auto'
  height: '100px', // standard option is 'auto'
  autocomplete: { option: value, option: value},
  hide: true,
  delimiter: [',',';'], // or a string with a single delimiter
  unique: true,
  removeWithBackspace: true,
  whitelist: [], // null or aray of whitelisted values
  onAddTag: callback_function,
  onRemoveTag: callback_function,
  onChange: callback_function
});

$('#entry-city#tags').tagExist('newTag')({ 
  // do something
});

$('#entry-city').tagsInput({

  onAddTag: ()=>{},
  onRemoveTag: ()=>{},
  onChange: ()=>{}
  
});

  $( function() {
    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    $( "#entry-city" ).autocomplete({
      source: availableTags
    });
  } );