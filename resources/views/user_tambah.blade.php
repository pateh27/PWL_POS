<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form tambah user</title>
</head>
<body>
    <h1>Form Tambah Data User</h1>
    <form action="user/tambah" method="post">
        {{csrf_field()}}

        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username">
        <br>
        <label>Nama</label>
        <input type="text" name="nama" placeholder="Masukkan nama">
        <br>
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password">
        <br>
        <label>Level ID</label>
        <input type="number" name="level_id" placeholder="Masukkan level id">
        <br><br>
        <input type="submit" class="btn btn-success" value="Simpan">
    </form>
</body>
</html>