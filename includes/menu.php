<?php

function menu($items) {
  $menu = '<ul id="menu">';
  for ($items as $key => $val) {
    $menu .= '<li><a href="' . $val . '">' . $key . '</a></li>';
  }
  $menu = '</ul>';
  print $menu;
}
