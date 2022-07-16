<?elements
head HEAD scripts "css/website.css"
string CONTENT
string title
link home lb "Home" route "home"
link test lb "Room test" route "home/test"
messages PRECONTENT
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{if title}{title} | {/if}LGen</title>

  <!-- Favicon -->
  <link rel="icon" href="">

  {HEAD}
</head>


<body>
  {home} | {test}
  <div class="content">

    {PRECONTENT}{CONTENT}
  </div>
</body>

</html>