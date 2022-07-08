<?php
session_start();
$db = new PDO('mysql:host=localhost;dbname=immersion', 'root', 'root');

function get_user_by_email($email){
    global $db;
    $sql = "SELECT * FROM users WHERE email=:email";
    $statement = $db->prepare($sql);
    $statement->execute(["email" => $email]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
function get_user_by_id($id){
    global $db;
    $sql = "SELECT * FROM users WHERE id=:id";
    $statement = $db->prepare($sql);
    $statement->execute(["id" => $id]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
function get_all_user(){
    global $db;
    $sql = "SELECT * FROM users ";
    $statement = $db->prepare($sql);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
function add_user($email, $password){
    global $db;
    if(!get_user_by_email($email)){
      $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
      $statement = $db->prepare($sql);
      $statement->execute([
          "email" => $email,
          "password" => $password,
      ]);
      set_flash_message('success', 'Регистрация успешна');
      return true;
    }else{
        set_flash_message('errors', 'Пользователь с такой почтой уже существует');
       return false;
    }
}
function set_flash_message($name, $message){
    $_SESSION[$name] = $message;
}
function set_session($name, $message){
    $_SESSION[$name] = $message;
}
function redirect_to($path){
    header('Location: '.$path);exit;
}
function login($email, $password){
    $user = get_user_by_email($email);
    if(!empty($user) && $user['password'] == $password){
        set_session('user', $email);
        set_session('admin', $user['is_admin']);
        set_session('id', $user['id']);
        update_status($email, 1);
        return true;
    }else{
        set_flash_message('errors', 'Не правильный логин или пароль');
        return false;
    }
}
function is_admin(){
    if($_SESSION['admin']){
        return true;
    }
    return false;
}
function is_not_login(){
    if( !empty($_SESSION['user']) ){
        return false;
    }
    return true;
}
function exit_session(){
    update_status($_SESSION['user'], 3);
    unset($_SESSION['user']);
    unset($_SESSION['is_admin']);
}
function create_user_for_admin($name, $work, $number, $address, $email, $password, $status, $img, $vk, $tg, $inst){
    global $db;
    if(!get_user_by_email($email)){
        $sql = "INSERT INTO users (name, work_place, phone_number, address, email, password, status, img, vk, telegramm, instagarm, is_admin) VALUES (:name, :work, :number, :address, :email, :password, :status, :img, :vk, :tg, :inst, 0)";
        $statement = $db->prepare($sql);
        $statement->execute([
            "name" => $name,
            "work" => $work,
            "number" => $number,
            "address" => $address,
            "email" => $email,
            "password" => $password,
            "status" => $status,
            "img" => $img,
            "vk" => $vk,
            "tg" => $tg,
            "inst" => $inst
        ]);

        set_flash_message('success', 'Пользователь добавлен');
        return true;
    }else{
        set_flash_message('errors', 'Пользователь с такой почтой уже существует');
        return false;
    }

}
function get_status($status){
    if($status == 1){
        return 'success';
    }elseif ($status == 2){
        return  'warning';
    }elseif ($status == 3){
        return 'danger';
    }
}
function update_status($email, $status){
    global $db;
    $sql = " UPDATE users SET status = :status WHERE email = :email";
    $statement = $db->prepare($sql);
    $statement->execute([
        "status" => $status,
        "email" => $email,
    ]);
    return true;
}
function delete($id){
    global $db;
    $sql = " DELETE FROM users WHERE id = :id";
    $statement = $db->prepare($sql);
    $statement->execute([
        "id" => $id,
    ]);
    return true;
}
function update_status_page($id, $status){
    global $db;
    $sql = " UPDATE users SET status = :status WHERE id = :id";

    $statement = $db->prepare($sql);
    $statement->execute([
        "status" => $status,
        "id" => $id,
    ]);
    set_flash_message('success', 'Статус успешно обновлен.');
    return true;
}
function update_general($id, $name, $work_place, $phone_number, $address){
    global $db;
    $sql = " UPDATE users SET name = :name, work_place = :work_place, phone_number = :phone_number, address = :address WHERE id = :id";
    $statement = $db->prepare($sql);
    $statement->execute([
        "id" => $id,
        "name" => $name,
        "work_place" => $work_place,
        "phone_number" => $phone_number,
        "address" => $address,
    ]);
    set_flash_message('success', 'Профиль успешно обновлен.');
    return true;
}
function update_security($id, $email, $password){
    global $db;
    $sql = " UPDATE users SET email = :email, password = :password WHERE id = :id";
    $statement = $db->prepare($sql);
    $statement->execute([
        "id" => $id,
        "email" => $email,
        "password" => $password,
    ]);
    set_flash_message('success', 'Профиль успешно обновлен.');
    return true;
}

function update_img($id, $img){
    global $db;
    $sql = " UPDATE users SET img = :img WHERE id = :id";
    $statement = $db->prepare($sql);
    $statement->execute([
        "id" => $id,
        "img" => $img,
    ]);
    set_flash_message('success', 'Профиль успешно обновлен.');
    return true;
}

