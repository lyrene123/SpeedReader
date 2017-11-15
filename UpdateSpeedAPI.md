**Update Speed**
----
  Sends a request to update the user wpm speed value in the database

* **URL**

  /speedreaderajax.php/:selectedSpeed

* **Method:**

  POST

*  **URL Params**

   None

* **Data Params**

  selectedSpeed=[integer]

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** `{ "result" : "Speed Updated}` or `{ "result" : "Speed Not Updated"}`

* **Error Response:**

  * **Code:** 4XX, 5XX or any code that is not 200 <br />
    **Content:** `{ errorMessage : "Something bad happened. Problem updating speed." }`
                  Error will be handled in the javascript side, and error message will be displayed
                  in the webpage

* **Sample Call:**

  `function updateSpeed(selectedSpeed){
    if(selectedSpeed.match(/^\d+$/) && g.speedChoicesList.indexOf(selectedSpeed) !== -1){
      var req = createHTTPRequest();
      req.open("POST", "speedreaderajax.php", true);
      req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200) {
          var jsonResponse = JSON.parse(req.responseText);
          if(jsonResponse !== null){  
            console.log(jsonResponse.result);
          }
        } else if (req.readyState == 4) {
          g.speedReaderError.innerText = "Something bad happened. Problem updating speed.";
        }
      };
      req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
      req.send('selectedSpeed=' + selectedSpeed);
    }
  }`
