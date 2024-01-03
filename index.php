<html>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <!-- <head> -->
    <style>
      /*
      *
      * ==========================================
      * CUSTOM UTIL CLASSES
      * ==========================================
      *
      */

      .file-upload input[type='file'] {
        display: none;
      }

      /*
      *
      * ==========================================
      * FOR DEMO PURPOSES
      * ==========================================
      *
      */

      body {
        background: #00B4DB;
        background: -webkit-linear-gradient(to right, #0083B0, #00B4DB);
        background: linear-gradient(to right, #0083B0, #00B4DB);
        height: 100vh;
      }

      .rounded-lg {
        border-radius: 1rem;
      }

      .custom-file-label.rounded-pill {
        border-radius: 50rem;
      }

      .custom-file-label.rounded-pill::after {
        border-radius: 0 50rem 50rem 0;
      }
    </style>
  </head>

  <body>
    <?php
    $f = @fopen("upload/results.txt", "r");
    if ($f == True){
      echo "<html><body><table class='table table-dark'>\n\n";
      $f = fopen("upload/results.txt", "r");
      while (($line = fgetcsv($f)) !== false) {
              echo "<tr>";
              foreach ($line as $cell) {
                      echo "<td>" . htmlspecialchars($cell) . "</td>";
              }
              echo "</tr>\n";
      }
      fclose($f);
      echo "\n</table></body></html>";
   // user has clicked a delete hyperlink
   if($_GET['action'] && $_GET['action'] == 'delete') {
       unlink("upload/results.txt");
       echo "<meta http-equiv='refresh' content='0;url=/index.php'>";
       exit();
   }
   echo "<a href='index.php?action=delete' class='file-upload btn btn-danger btn-block rounded-pill shadow' style='width:200px; margin:auto;'>Delete File</a>";
    } else { ?>
      <section>
    <div class="container p-5">
      <!-- For demo purpose -->
      <div class="row mb-5 text-center text-white">
        <div class="col-lg-10 mx-auto">
          <h1 class="display-4">HIBP Email Checker </h1>
          <p class="lead">Check a list of email addresses against HaveIBeenPwned Service.</p>
        </div>
      </div>
      <!-- End -->
      <div class="row">
          <div class="col-lg-5 mx-auto">
            <div class="p-5 bg-white shadow rounded-lg"><img src="https://store-images.s-microsoft.com/image/apps.2401.13510798883203237.d9b40472-ef9e-459e-88a8-fd9ee8e1466b.2a730341-fe3c-4e7f-baba-f147cbd7532b?mode=scale&q=90&h=300&w=300" alt="" width="200" class="d-block mx-auto mb-4 rounded-pill">

              <!-- Default bootstrap file upload-->

              <h6 class="text-center mb-4 text-muted">
                Upload CSV file containing all email addresses that needs checking
              </h6>
              <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
              <div class="custom-file overflow-hidden rounded-pill mb-5">
                <input name ="file" id="file" type="file" class="custom-file-input rounded-pill">
                <label for="file" class="custom-file-label rounded-pill">Choose file</label>
              </div>
              <!-- End -->

              <!-- Custom bootstrap upload file-->
                            <input name="submit" type="submit" class="file-upload btn btn-primary btn-block rounded-pill shadow">
                        </label>
              <!-- End -->
              </form>
              <div class="progress">
                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> <?php
    }
     ?>
  </body>
</html>

<?php
if ( isset($_POST["submit"]) ) {

   if ( isset($_FILES["file"])) {

            //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        }
        else {
                 //Print file details
             // echo "Upload: " . $_FILES["file"]["name"] . "<br />";
             // echo "Type: " . $_FILES["file"]["type"] . "<br />";
             // echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
             // echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

                 //if file already exists
             if (file_exists("upload/" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " already exists. ";
             }
             else {
                    //Store file in directory "upload" with the name of "uploaded_file.txt"
            $storagename = "uploaded_file.csv";
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
            //echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";

            $bom = "\xef\xbb\xbf";

            //get total number of rows for progress bar
            $file = file("upload/uploaded_file.csv");
            $totalrows = count($file);
            $i=1;
            $increment = 100/$totalrows;
            echo "<script> var totalrows = ".$totalrows.";</script>";
            flush();

            echo "<html><body><table class='table table-dark'>\n\n";
            $f = fopen("upload/uploaded_file.csv", "r");

            if (fgets($f, 4) !== $f) {
                // BOM not found - rewind pointer to start of file.

              while (($line = fgetcsv($f)) !== false) {
                      echo "<tr>";
                      foreach ($line as $cell) {
                              echo "<td>" . htmlspecialchars($cell) . "</td>";
                              echo "<td>" . check_hibp($cell) . "</td>";
                              echo "<script> document.getElementById('progressbar').style.width = ".$i*$increment." +'%'; </script>";
                              $i++;
                              flush();
                      }
              }
              echo "<script> document.getElementById('progressbar').style.backgroundColor = '#57ff01'; </script>";
              echo "</tr>\n";
            rewind($f);
            }

            fclose($f);
            echo "\n</table></body></html>";
            unlink("upload/uploaded_file.csv");


            // $file = fopen("upload/uploaded_file.csv","r");
            // while(! feof($file))
            //   {
            //     check_hibp();
            //   print_r(fgetcsv($file));
            //   }
            // fclose($file);

            }
        }
     } else {
             echo "No file selected <br />";
     }
}


function check_hibp($email){
  $i = 0;
  sleep(6);
  $hibp_api_key = 'INSERT_API_KEY_HERE';
  $url = 'https://haveibeenpwned.com/api/v3/breachedaccount/'.$email;
  // print($url);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'hibp-api-key: '.$hibp_api_key,
    'user-agent: HIBP Email Checker'
  ));
  curl_setopt($ch, CURLOPT_VERBOSE, 1); // remove this if your not debugging
  curl_setopt($ch, CURLOPT_RETURNTRANSFER ,True);

  $result = curl_exec($ch);
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  file_put_contents('upload/results.txt', $email, FILE_APPEND);
  // print("status = $status_code\n");
  // echo $result;
  if ($status_code == 200) { // OK
    file_put_contents('upload/results.txt', ', Breached!'.PHP_EOL, FILE_APPEND);
    return "Breached!";
  } elseif ($status_code == 429) {
    file_put_contents('upload/results.txt', ', API Limit Reached!'.PHP_EOL, FILE_APPEND);
    // sleep(2);
    // return check_hibp($email);
    return "API Limit Reached!";
  } else {  // Error occured
    file_put_contents('upload/results.txt', ', Not Breached!'.PHP_EOL, FILE_APPEND);
    return "Not Breached!";
  }
  curl_close ($ch);
}
?>

<script type="text/javascript" language="javascript" >
$('.custom-file-input').on('change', function() {
   let file = $(this).val().split('\\').pop();
   $(this).next('.custom-file-label').addClass("selected").html(file);

   // var totalrows = 50;
   // // var w = parseInt(document.getElementById('progressbar').style.width);
   // for (var x = 0; x < 2; x++) {
   //  // setTimeout(init_progressbar(),1000);
   //  init_progressbar();
   // }

   // var totalrows = 5;
   //
   // for (var x = 0; x < totalrows; x++) {
   //   setTimeout(function(y) {
   //     console.log("%d => %d", y, totalrows[y] += 10);
   //   }, x * 2000, x); // we're passing x
   // }
});
</script>
