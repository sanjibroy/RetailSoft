function sendData(formData, formIdentifier) {
    return new Promise(function(resolve, reject) {
      $.ajax({
        url: "your_php_script.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          resolve(response); // Resolve the promise with the response
        },
        error: function(xhr, status, error) {
          reject(error); // Reject the promise with the error
        }
      });
    });
  }
  
  // Usage example:
  var form1 = $("#form1");
  var formData = form1.serialize();
  var formIdentifier = "form1";
  
  sendData(formData, formIdentifier)
    .then(function(response) {
      console.log(response); // Handle the response
      // Continue with the next steps
    })
    .catch(function(error) {
      console.log("Ajax request failed:", error);
    });
  