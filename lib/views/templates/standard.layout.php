<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link to external CSS for quick and easy prototyping. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <link href="/css/style.css" rel="stylesheet">
    <title><?php if (!empty($title)) { echo $title; } ?></title>
</head>
<body>
    <header class="p-3 bg-dark text-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="/" class="nav-link px-2 text-white">Home</a></li>
          <li><a href="/articlelist" class="nav-link px-2 text-white">Article List</a></li>
          <li><a href="#" class="nav-link px-2 text-white">Pricing</a></li>
          <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
          <li><a href="#" class="nav-link px-2 text-white">About</a></li>
          <?php
            if (!empty($nav_perm) && $nav_perm == 3) {
              echo "<li><a href=\"/signup\" class=\"nav-link px-2 text-white\">Add new user</a></li>";
            }
          ?>
        </ul>
        <?php
          if (!empty($nav_name)) {
            echo "<div class='col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3'><a href=\"/editAccount\"><p style='margin:auto;'>{$nav_name}</p></a></div>";
          }
            // <!-- <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search"> -->
        ?>
        <div class="text-end">
            <?php
                if (!empty($is_auth) && $is_auth === true) {
                  echo "<a href='/addarticle'><button type='button' class='btn btn-outline-light me-2'>Create-Article</button></a>";
                  echo "<a href='/editarticleslist'><button type='button' class='btn btn-outline-light me-2'>Edit-Articles</button></a>";
                  echo "<a href='/signout'><button type='button' class='btn btn-warning'>Sign-out</button></a>";
                } else {
                    echo "<a href='/signin'><button type='button' class='btn btn-outline-light me-2'>Sign-in</button></a>";
                }
            ?>
        </div>
      </div>
    </div>
    </header>
    
    <section id="content">
      <?php if (!empty($page_content)) { require $page_content; } ?>
    </section>

</body>

<!-- Link to external JS for quick and easy CSS/visual prototyping. -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

<script type="text/javascript" src="/scripts/jsHyperlinkTags.js"></script>

</html>