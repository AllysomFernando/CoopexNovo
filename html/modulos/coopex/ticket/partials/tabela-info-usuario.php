<?php

if ($usuario == null || $usuario->id_pessoa == null) {
  $usuario = $repository->coopex->getUserInfoByUserId($ticket->id_usuario);
}

?>


<ul class="list-group">
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Nome</span>
    <?php echo utf8_encode($usuario->nome) ? utf8_encode($usuario->nome) : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">ID Pessoa</span>
    <?php echo $usuario->id_pessoa ? $usuario->id_pessoa : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">ID Usuario</span>
    <?php echo isset($usuario->id_usuario) ? $usuario->id_usuario : "..." ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">RA</span>
    <?php echo $usuario->ra ? $usuario->ra : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">CPF</span>
    <?php echo $usuario->cpf ? $usuario->cpf : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Email</span>
    <?php echo $usuario->email ? $usuario->email : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Curso</span>
    <?php echo isset($usuario->curso) ? $usuario->curso : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Tipo</span>
    <?php echo utf8_encode($usuario->tipo) ? utf8_encode($usuario->tipo) : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Usuario de login</span>
    <?php echo $usuario->usuario ? $usuario->usuario : "..."; ?>
  </li>
</ul>