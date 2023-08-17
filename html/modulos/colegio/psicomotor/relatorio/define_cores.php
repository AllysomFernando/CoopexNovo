<?php

function defineCores($element) {
  if ($element == "F") {
    return "style='color: green'";
  } elseif ($element == "D") {
    return "style='color: red'";
  }
  return "";
}