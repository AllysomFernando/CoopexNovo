<?

function listar_material(){
    $coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');
    $coopex->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM colegio_app.material";
    $res = $coopex->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
}

function listar_mochila(){
    $coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');
    $coopex->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM colegio_app.mochila";
    $res = $coopex->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
}