<?php

function getTicketPanelBadge($status)
{
  $badge = new stdClass();
  $badge->message = "";
  $badge->class_name = "";

  if ($status == 1) {
    $badge->message = "Solucionado";
    $badge->class_name = "badge-success";
  }

  if ($status == 2) {
    $badge->message = "Cancelado";
    $badge->class_name = "badge-danger";
  }

  if ($status == 3) {
    $badge->message = "Em Andamento";
    $badge->class_name = "badge-info";
  }

  if ($status == 4) {
    $badge->message = "Aguardando";
    $badge->class_name = "badge-warning";
  }

  return $badge;
}
