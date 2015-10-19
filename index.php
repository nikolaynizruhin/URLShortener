<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>URL Shortener</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="jumbotron">
        <h1>URL Shortener</h1>
        <p>Paste your long URL here:</p>
        <p><input type="url" class="form-control"></p>
        <p>
          <form class="form-inline">
            <div class="checkbox">
              <label>
                <input type="checkbox" id="checkbox"> Time to live
              </label>
            </div>
            <input type="number" min="1" class="form-control hidden"> <span class="hidden">days</span>
          </form>
        </p>
        <div class="alert alert-success hidden" role="alert"></div>
        <div class="alert alert-danger hidden" role="alert">The long url you entered was not valid.</div>
        <p><a class="btn btn-primary btn-lg" role="button">Shorten URL</a></p>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/urlshortener.js"></script>
  </body>
</html>