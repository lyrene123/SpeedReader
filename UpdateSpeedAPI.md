**Update Speed**
----
  Sends a request to update the user wpm speed value in the database

* **URL**

  /speedreaderajax.php/:request

* **Method:**

  POST

*  **URL Params**

   None

* **Data Params**

  request=['initial' or 'next']
  where initial refers to request of user's current book line and speed
  and next refers to the next book line and updated speed if changed.

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** `{ "book_line" : "this is a book line", "speed" : 100}`

* **Error Response:**

  * **Code:** 4XX, 5XX or any code that is not 200 <br />
    **Content:** `{ errorMessage : "Something bad happened. Problem displaying words." }`
                  Error will be handled in the javascript side, and error message will be displayed
                  in the webpage

* **Sample Call:**

  `function retrieveNextLineAndSpeedFromDb(){
    var req = new XMLHttpRequest();
    req.open("POST", "speedreaderajax.php", true);
    req.onreadystatechange = function() {
      if (req.readyState == 4 && req.status == 200) {
        var jsonResponse = JSON.parse(req.responseText);
        if(jsonResponse !== null){
          var line = jsonResponse.book_line;
          var speed = jsonResponse.speed;
          //do something with line and speed
        }
      } else if (req.readyState == 4) {
        g.speedReaderError.innerText = "Something bad happened. Problem displaying words.";
      }
    };
    req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    req.send('request=' + request);
  }`
