<?elements
head HEAD scripts "css/website.css,js/jquery.js"
string CONTENT
string title
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
  <div class="content">
    {PRECONTENT}{CONTENT}
  </div>
</body>

</html>