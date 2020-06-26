<?php

return [
    'uid' => '1111-1111-1111-1111',
    'login' => 'Simple',
    'password' => \password_hash('asdf12345', PASSWORD_DEFAULT),
    'firstName' => 'Hans',
    'lastName' => 'Zimmer',
    'email' => 'Hans333@email.com',
    'birthday' => include 'birthday.php',
    'address' => [include 'address.php'],
    'id' => '77-877a',
    'paymentMethod' => 'card',
];