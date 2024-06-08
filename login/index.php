<!doctype html>
<html lang="en" class="dark bg-gray-900 text-white" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vault Login</title>
  <link href="../src/bootstrap.css" rel="stylesheet">
</head>

<body class='bg-dark text-white'>
  <div class='container-fluid'>
    <div class="row">
      <div class="col-12 py-3">
        <!-- 
        Basic (View Only)
        USer (Pull Codes for Self)
        Mod (Pull codes for Others)
        Admin (Add and Modify Codes)
        -->
<?php
        if(isset($_POST['login'])){
          $addedCookieData = "{$_POST['login']}";
          if($_POST['login'] != "basic") $addedCookieData .= "-{$_POST['login_as']}";
          setcookie("Vault_login",$addedCookieData, time() + (86400 * 30), "/");
          echo "<meta http-equiv='refresh' content=\"0; url=./\">";
        }
        if(!empty($_COOKIE['Vault_login'])) echo "<meta http-equiv='refresh' content=\"0; url=../\">";
?>

        <div class="accordion" id="accordionLogin">


          <div class="accordion-item">
            <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Basic" aria-expanded="false" aria-controls="Basic">
              Basic (View Only)</button>
            </h2>
            <div id="Basic" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
              <div class="accordion-body">
                <form action='' method="POST">
                  <input name='login' value='basic' type='hidden' />
                  <button class="btn btn-success" type="submit">Log in at Basic User</button>
                </form>
              </div>
            </div>
          </div>


          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#User" aria-expanded="false" aria-controls="User">
                User (Pull Codes for Self)
              </button>
            </h2>
            <div id="User" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
              <div class="accordion-body">
                <form action='' method="POST">
                  <input name='login' value='user' type='hidden' />
                  <div class="input-group">
                    <input class='form-control' name='login_as' type='text' placeholder="Enter Username Name" required />
                    <button class="btn btn-success" type="submit">Usere Login</button>
                  </div>
                </form>
              </div>
            </div>
          </div>


          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Mod" aria-expanded="false" aria-controls="Mod">
                Mod (Pull Codes for Others and Set Codes Invalid)
              </button>
            </h2>
            <div id="Mod" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
              <div class="accordion-body">
                <form action='' method="POST">
                  <input name='login' value='mod' type='hidden' />
                  <div class="input-group">
                    <input class='form-control' name='login_as' type='password' placeholder="Mod Password" required />
                    <button class="btn btn-success" type="submit">Mod Login</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Admin" aria-expanded="false" aria-controls="Admin">
                Admin (Add and Modify Codes)
              </button>
            </h2>
            <div id="Admin" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
              <div class="accordion-body">
                <form action='' method="POST">
                  <input name='login' value='admin' type='hidden' />
                  <div class="input-group">
                    <input class='form-control' name='login_as' type='password' placeholder="Admin Password" required />
                    <button class="btn btn-success" type="submit">Admin Login</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>



      </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </div>
</body>

</html>