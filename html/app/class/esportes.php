<?

function listar_planejamento($id_modalidade)
{
    $coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');
    $coopex->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM colegio.planejamento WHERE id_modalidade = $id_modalidade";
    $res = $coopex->query($sql);
    return $res->fetch(PDO::FETCH_OBJ);
}