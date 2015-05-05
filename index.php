<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Interview Test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      <section class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                  <h1>PHP Test</h1>
                  <p>This test's purpose is to demonstrate the understanding and relationship between HTML, Javascript, PHP and HTTP.
                  There are 3 files associated with this test, index.php, request.php, and people.csv.  The index.php will serve as the
                  as the front end webpage that will be opened in the web browser.  Upon loading, it will send an AJAX to the request.php
                  and get all of the people from the csv.  It should load them into the table of index.php.  Buttons on the webpage should
                  allow the user to sort the table.  Sorting can be done either server side or client side in javascript.</p>

                  <h3>Instructions</h3>
                  <ol>
                      <li>Parse csv with PHP in request.php</li>
                      <li>Create object structure using classes, people and person.</li>
                      <li>Create method in which takes these objects from the previous step and return them via HTTP</li>
                      <li>Use Jquery to request the return from the previous step</li>
                      <li>Now these objects are in Java script, fill the HTML with the results</li>
                      <li>Add sorting functionality to buttons, serverside or clientside(hint: think this through before choosing)</li>
                  </ol>
                  <h3>HTML Table</h3>
                  <button data-orderby="name" class="btn btn-default">Sort by last name</button>
                  <button data-orderby="height" class="btn btn-default">Sort by height</button>
                  <button data-orderby="gender" class="btn btn-default">Sort by gender</button>
                  <button data-orderby="birthdate" class="btn btn-default">Sort by birthdate</button>
                  <BR><BR>
                  <table class="table table-striped table-bordered">
                      <thead>
                          <tr>
                              <th>Name</th>
                              <th>Height</th>
                              <th>Gender</th>
                              <th>Birthdate</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                  </table>
              </div>
          </div>
      </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script>
      $( document ).ready(function() {
          $.get('request.php', {orderBy: 'none'}, function(data) {

              $(".table tbody").children('tr').remove();

              $.each( data, function( key, value ) {
                  $(".table tbody").append(
                      "<tr>"+
                        "<td>"+value.name+"</td>"+
                        "<td>"+value.height+"</td>"+
                        "<td>"+value.gender+"</td>"+
                        "<td><time datetime="+value.birthdate+">"+value.birthdate+"</time></td>"+
                      "</tr>"
                    );
                });
          });

          $(".btn").click(function(e) {

              $(".table tbody").children('tr').remove();

              $.get('request.php', {orderBy: $(this).attr('data-orderby')}, function(data) {
                  $.each( data, function( key, value ) {
                      $(".table tbody").append(
                          "<tr>"+
                            "<td>"+value.name+"</td>"+
                            "<td>"+value.height+"</td>"+
                            "<td>"+value.gender+"</td>"+
                            "<td><time datetime="+value.birthdate+">"+value.birthdate+"</time></td>"+
                          "</tr>"
                        );
                    });
              });
          });
      });

    </script>
  </body>
</html>
