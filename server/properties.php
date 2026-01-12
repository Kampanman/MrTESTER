<?php

/**
 * プロパティ諸設定
 */
$contents_name = "Mr.TESTER";

$dsn = 'localhost';
$dbname = 'crud';
$username = 'root';
$password = '';

$fontset = '<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet" />';
$bootset = function ($position) {
  if ($position == "head") {
    return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" '
      . 'rel="stylesheet" crossorigin="anonymous">';
  } else {
    return '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>'
      . '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>'
      . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>'
      . '<script src="https://code.jquery.com/jquery-3.6.0.min.js" '
      . 'integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>';
  }
};
$datatableset = function ($position) {
  if ($position == "head") {
    return '<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">';
  } else {
    return '<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>'
      . '<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>';
  }
};
