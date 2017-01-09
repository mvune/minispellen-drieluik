window.addEventListener('load', function() {

var wheels = document.getElementsByClassName('wheel');

/*
 * Verhoogt het getal in het wiel met 1. Na 6 volgt weer 0.
 */
function turnWheel(wheel) {
  
  var inputField = wheel.getElementsByTagName('input')[0];
  var cipher = parseInt(inputField.value, 10);
  
  inputField.value = (cipher >= 0 && cipher < 6) ? cipher + 1 : 0;
}

if(document.getElementById('result-message').innerHTML === "") {
  
  document.getElementsByTagName('input')[0].select();
  
  for(var i = 0; i < wheels.length; i++) {
    
    // Click event op wiel => turnWheel(wheel).
    wheels[i].addEventListener('click', function() {
      turnWheel(this);
      this.getElementsByTagName('input')[0].setCustomValidity('');
    });
    
    var inputField = wheels[i].getElementsByTagName('input')[0];
    
    // Click event op invoerveld in wiel => Selecteert invoer.
    inputField.addEventListener('click', function(event) {
      this.select();
      event.stopPropagation();
    });
    
    // Input event op invoerveld in wiel => Staat alleen getallen 
    // 1 t/m 6 toe en verplaats focus naar volgende wiel.
    inputField.addEventListener('input', function() {
      var input         = parseInt(this.value, 10);
      var inputFields     = document.getElementsByClassName('number');
      var nextInputFieldName  = +this.getAttribute('name') + 1;
      
      if(input > 0 && input <= 6) {
        this.value = input;
        this.setCustomValidity('');
      
        if(nextInputFieldName < inputFields.length) {
          document.getElementsByName(nextInputFieldName)[0].select();
        } else {
          inputFields[0].select();
        }
      } else {
        this.value = 0;
      }
    });
    
    // Custom foutmelding bij invalid input.
    inputField.addEventListener('invalid', function() {
      this.setCustomValidity('Probeer eens een getal tussen de 1 en 6.');
    });
  }
}
});
