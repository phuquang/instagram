# Instagram API

index.php
```xml
<?php
include 'Instagram.php';

$instagram = new Instagram();
$instagram->setClientId('');
$instagram->setClientSecret('');
$instagram->setRedirectUri('');

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $instagram->setResponseType($_POST['type']);
    $instagram->authorize();
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Instagram API</title>
<style>.item{width: 150px;display: inline-block;}img{width: 100%;height: auto;}</style>
</head>
<body>
Authorize
<form action="" method="post">
    <button type="submit" name="type" id="ImplicitOAuth" value="code">Implicit OAuth</button>
    <button type="submit" name="type" id="ExplicitOAuth" value="token">Explicit OAuth</button>
</form>

Access token
<form action="" method="GET">
<input type="text" name="access_token" id="access_token" value="<?php echo empty($_GET['access_token'])?'':$_GET['access_token'] ?>">
<button type="submit" id="btn_access_token">Get Access token from hash tag</button>
</form>
<script>
function removeLocationHash(){
    var noHashURL = window.location.href.replace(/#.*$/, '');
    window.history.replaceState('', document.title, noHashURL) 
}
if(window.location.hash) {
    var hash = window.location.hash.substring(14); //Puts hash in variable, and removes the # character
    var name = window.location.hash.substring(1, 13); //Puts hash in variable, and removes the # character
    document.getElementById('access_token').value = hash;
    if(name = "access_token") {
        var path = window.location.href.substr(0, window.location.href.indexOf('#'))
        document.getElementById("btn_access_token").click();
        // window.location.href = path;
    }
}
removeLocationHash();
</script>
<pre>
<?php
if (!empty($_GET['access_token'])){
    $instagram = new Instagram();
    $instagram->setAccessToken($_GET['access_token']);
    $media = $instagram->getMediaRecent();
    foreach ($media as $v) {
        echo '<div class="item"><a href="'.$v['link'].'" target="_blank"><img src="'.$v['thumbnail']['url'].'" alt="'.$v['alt'].'"></a></div>';
    }
}
?>
</body>
</html>
```

callback.php
```php
include 'Instagram.php';
if (!empty($_GET['code'])){
    $instagram = new Instagram();
    $instagram->setClientId('');
    $instagram->setClientSecret('');
    $instagram->setRedirectUri('');

    $instagram->setCode($_GET['code']);
    $result = $instagram->getAccessToken();
    $result = json_decode($result, true);
    if ( !isset($result['error_message']) ) {
        $access_token = $result['access_token'];
        // $user = $result['user'];
        // $user['id'];
        // $user['username'];
        // $user['profile_picture'];
        // $user['full_name'];
        // $user['bio'];
        // $user['website'];
        // $user['is_business'];
        print_r($result);
        header("location: index.php/?access_token=".$access_token );
    }else{
        print_r($result['error_message']);
    }
}else{
    header("location: index.php" );
}
```
