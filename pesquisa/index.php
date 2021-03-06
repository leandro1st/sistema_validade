<?php
require("../externo/c.php");

date_default_timezone_set('America/Sao_Paulo');
$produto = trim($_POST['nome_pesquisa']);
if (!$_POST['nome_pesquisa']) {
    header("location: ../");
}
$pesquisar = mysqli_query($connect, "SELECT * FROM $vencimentos WHERE $nome_produto like '%" . $produto . "%' or DATE_FORMAT(validade, '%d/%m/%Y') like '%" . $produto . "%' or id like '%" . $produto . "%' ORDER BY validade ASC");
$numero_produto = mysqli_num_rows($pesquisar);
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php
        if ($produto == "" || preg_match('/^[\pZ\pC]+|[\pZ\pC]+$/u', $produto)) {
            echo "Pesquisa";
        } else {
            echo "Pesquisa | " . $produto;
        }
        ?>
    </title>
    <link rel="stylesheet" href="../externo/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../externo/style.css">
    <script src="../externo/jquery/jquery-3.4.0.min.js"></script>
    <!-- <script src="../externo/bootstrap/js/bootstrap.min.js"></script> -->
    <script src="../externo/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        /* Ao clicar no ícone de editar, executa a seguinte função que oculta a validade antiga e mostra um input */
        function editar(id) {
            // alert(id);
            document.getElementById('editar_validade-' + id + '').style.display = 'inline';
            document.getElementById('editar_validade-' + id + '').focus();
            document.getElementById('validade_editada-' + id + '').style.display = 'none';
        } /* Ao clicar no ícone de editar, executa a seguinte função que oculta a validade antiga e mostra um input */

        /* Data de hoje */
        var hoje = new Date();
        var dd = String(hoje.getDate()).padStart(2, '0');
        var mm = String(hoje.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = hoje.getFullYear();
        hoje = yyyy + '-' + mm + '-' + dd; /* Data de hoje */

        /* Função que ao perder o foco do input, oculta o input e retorna a validade editada, ao final executa o ajax */
        function mouse(id, produto, validade_antiga) {
            var validade_nova = $('#editar_validade-' + id).val();
            if (!validade_nova) {
                hoje2 = dd + '/' + mm + '/' + yyyy; /* Data de hoje */
                document.getElementById('editar_validade-' + id + '').style.display = 'none';
                document.getElementById('editar_validade-' + id + '').className = 'form-control is-valid';
                document.getElementById('validade_editada-' + id + '').style.display = 'inline';
                if (validade_nova < validade_antiga) {
                    document.getElementById('validade_editada-' + id + '').innerHTML = validade_antiga;
                    document.getElementById("validade_nova").innerHTML = validade_antiga;
                } else if (validade_nova > validade_antiga) {
                    document.getElementById('validade_editada-' + id + '').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                    document.getElementById('validade_nova').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                } else {
                    document.getElementById('validade_editada-' + id + '').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                    document.getElementById('validade_nova').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                }
                if (validade_antiga != hoje2 && validade_nova != hoje2) {
                    document.getElementById('linha-' + id + '').className = '';
                }

                document.getElementById("id_produto_editado").innerHTML = id;
                document.getElementById("produto_editado").innerHTML = produto;
                document.getElementById("validade_antiga").innerHTML = validade_antiga;
                $("#modalEditado").modal('show');
                //document.getElementById('editar_validade-' + id + '').className = 'form-control is-invalid';
            } else if (validade_nova <= hoje) {
                var arr = validade_nova.split("-");
                data_hoje = hoje.split("-")[2] + "/" + hoje.split("-")[1] + "/" + hoje.split("-")[0];
                document.getElementById('validade_nova_erro').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                document.getElementById('data_hoje').innerHTML = data_hoje;
                $("#modalErro").modal('show');
                document.getElementById('editar_validade-' + id + '').style.display = 'none';
                document.getElementById('editar_validade-' + id + '').className = 'form-control is-valid';
                document.getElementById('validade_editada-' + id + '').style.display = 'inline';
            } else if (validade_nova > hoje) {
                var arr = validade_nova.split("-");
                document.getElementById('editar_validade-' + id + '').style.display = 'none';
                document.getElementById('editar_validade-' + id + '').className = 'form-control is-valid';
                document.getElementById('validade_editada-' + id + '').style.display = 'inline';
                document.getElementById('validade_editada-' + id + '').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                document.getElementById('linha-' + id + '').className = '';
                $.ajax({
                    method: 'POST',
                    url: '../cadastrar/editar.php',
                    data: $('#form-' + id + '').serialize(),
                    success: function(data) {
                        document.getElementById("id_produto_editado").innerHTML = id;
                        document.getElementById("produto_editado").innerHTML = produto;
                        document.getElementById("validade_antiga").innerHTML = validade_antiga;
                        document.getElementById('validade_nova').innerHTML = arr[2] + "/" + arr[1] + "/" + arr[0];
                        $("#modalEditado").modal('show');
                    },
                    error: function(data) {
                        alert("Ocorreu um erro!");
                    }
                });
            }
        }
    </script>
</head>

<body>
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="../">
            <img src="../imagens/logo.png" alt="logo" width="35px">
            <!-- <i class="far fa-calendar-alt" style="font-size: 35px;"></i> -->
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item px-1">
                    <a class="nav-link" href="../"><i class="fas fa-home" style="font-size: 24px; vertical-align: middle"></i></a>
                </li>
                <li class="nav-item px-1">
                    <a class="nav-link text-success" href="../cadastrar/"><i class="fas fa-edit text-success" style="font-size: 24px; vertical-align: middle"></i> </a>
                </li>
                <li class="nav-item px-1">
                    <a class="nav-link" href="../excluir/"><i class="far fa-trash-alt text-danger" style="font-size: 24px; vertical-align: middle"></i></a>
                </li>
                <li class="nav-item px-1 active">
                    <a class="nav-link underline" href="javascript:void(0)"><i class="fas fa-search" style="font-size: 24px; vertical-align: middle"></i></a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="./" method="POST">
                <input class="form-control mr-sm-2" name="nome_pesquisa" placeholder="Nome do produto" aria-label="Search" style="width: 300px; background-color: #eee; border-radius: 9999px; border: none; padding-left: 20px; padding-right: 42px">
                <button type="submit" style="position: absolute; margin-left: 259px; border: none; cursor: pointer"><i class="fas fa-search text-success"></i></button>
            </form>
        </div>
    </nav>
    <nav aria-label="breadcrumb" style="position: absolute; z-index: 10;">
        <ol class="breadcrumb asap_regular" style="background: none; margin: 0; word-break: break-word;">
            <li class="breadcrumb-item"><a href="../"><i class="fas fa-home"></i> Página Inicial</a></li>
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)" class="none_li"><i class="fas fa-search"></i>
                    <?php if ($produto == "" || preg_match('/^[\pZ\pC]+|[\pZ\pC]+$/u', $produto)) {
                        echo "Pesquisa";
                    } else {
                        echo "Pesquisa | " . $produto;
                    } ?>
                </a>
            </li>
        </ol>
    </nav>
    <div id="carousel" class="carousel slide carousel-fade" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carousel" data-slide-to="0" class="active"></li>
            <li data-target="#carousel" data-slide-to="1"></li>
            <li data-target="#carousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <div class="view">
                    <img class="d-block w-100" src="../imagens/mountain.jpg" alt="First slide">
                </div>
                <div class="carousel-caption">
                    <h1 class="montara" style="padding-bottom: 10px">Validades</h1>
                </div>
            </div>
            <div class="carousel-item">
                <div class="view">
                    <img class="d-block w-100" src="../imagens/emilia.png" alt="Second slide">
                </div>
                <div class="carousel-caption">
                    <h1 class="montara" style="padding-bottom: 10px">Validades</h1>
                </div>
            </div>
            <div class="carousel-item">
                <div class="view">
                    <img class="d-block w-100" src="../imagens/kimi_no_na.jpg" alt="Third slide">
                </div>
                <div class="carousel-caption">
                    <h1 class="montara" style="padding-bottom: 10px">Validades</h1>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <?php
    if ($numero_produto > 0) { ?>
        <main class="container" style="margin-top: 1.5rem">
            <h4 class="asap_bold">Resultados: <small class="asap_regular"><?php echo $produto ?></small></h4>
            <p class="asap_regular" style="font-size: 1.25em">
                <?php if ($numero_produto == 1) {
                    echo "<b>" . $numero_produto . "</b> resultado encontrado";
                } else {
                    echo "<b>" . $numero_produto . "</b> resultados encontrados";
                } ?>
            </p>
            <table class="table table-hover table-striped text-center">
                <thead>
                    <tr class="table-warning">
                        <th scope="col" class="lead" width="8%"><b>#</b></th>
                        <th scope="col" class="lead"><b>PRODUTO</b></th>
                        <th scope="col" class="lead" width="10%"><b>VALIDADE</b></th>
                        <th scope="col" class="lead" width="20%"><b>CADASTRO</b></th>
                        <th scope="col" class="lead" width="5%"><i class="fas fa-cogs"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < $numero_produto; $i++) {
                        $vetor = mysqli_fetch_array($pesquisar);
                        $vetor_produto = $vetor['nome_produto'];
                        $vetor_validade = $vetor['validade'];
                        $vetor_hora_cadastro = $vetor['hora_cadastro'];
                        $vetor_id = $vetor['id'];
                        if (date('d-m-Y') == date("d-m-Y", strtotime($vetor_validade))) { ?>
                            <tr id="linha-<?php echo $vetor_id ?>" class="bg-warning">
                            <?php } else { ?>
                            <tr id="linha-<?php echo $vetor_id ?>">
                            <?php } ?>
                            <form id="form-<?php echo $vetor_id ?>" method="post">
                                <td><?php echo $vetor_id ?></td>
                                <td class="text-left" style="max-width: 600px; word-wrap: break-word"><?php echo $vetor_produto ?></td>
                                <td>
                                    <?php $amanha = date("Y-m-d", strtotime("+1 days")) ?>
                                    <b id="validade" class="text-danger">
                                        <!-- mostra a validade -->
                                        <span id="validade_editada-<?php echo $vetor_id ?>"><?php echo date("d/m/Y", strtotime($vetor_validade)) ?></span>
                                        <div id="div-vencimento">
                                            <input type="hidden" id="id_produto" name="cod_produto" value="<?php echo $vetor_id ?>">
                                            <input id="editar_validade-<?php echo $vetor_id ?>" name="validade" type="date" class="form-control" value="<?php echo $vetor_validade ?>" min="<?php echo $amanha ?>" max="2099-12-31" style="display: none; width: 200px" required onblur="mouse(<?php echo $vetor_id ?>, '<?php echo $vetor_produto ?>', '<?php echo date('d/m/Y', strtotime($vetor_validade)) ?>')" onkeydown="return event.key != 'Enter';">
                                            <div class="invalid-feedback">
                                                <?php $amanha2 = date("Y/m/d", strtotime("+1 days")); ?>
                                                Por favor, digite o data de vencimento! (min: <?php echo $amanha2 ?> | máx: 31/12/2099)
                                            </div>
                                        </div>
                                    </b>
                                </td>
                                <td><?php echo date("d/m/Y H:i:s", strtotime($vetor_hora_cadastro)) ?></td>
                                <td>
                                    <i class="fas fa-edit" style="cursor: pointer; color: green; font-size: 25px;" onclick="editar(<?php echo $vetor_id ?>)"></i>
                                </td>
                            </form>
                            </tr>
                        <?php } ?>
                </tbody>
            </table>
        </main>
    <?php } else { ?>
        <script>
            $(document).ready(function() {
                if (window.matchMedia("(max-width:1366px)").matches) {
                    document.getElementById("footer1").style.marginBottom = "-269px";
                } else if (window.matchMedia("(min-width:1600px) and (max-width:1920px)").matches) {
                    document.getElementById("footer1").style.marginBottom = "-68px";
                }
            });
        </script>
        <main class="container" style="margin-top: 1.5rem">
            <!-- <h4>Resultados: <small style="color: black; text-decoration: none;"><?php echo $produto ?></small></h4> -->
            <p class="lead text-center" style="padding-top: 8%; font-size: 25px"><b>Nenhum </b>resultado foi encontrado!</p>
        </main>
    <?php } ?>

    <!--Modal: modalEditado-->
    <div class="modal fade" id="modalEditado" tabindex="-1" role="dialog" aria-labelledby="modalEditadoTitle" aria-hidden="true" onkeypress="$('#modalEditado').modal('toggle');">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 650px;">
            <div class="modal-content">
                <div class="modal-header text-center bg-success">
                    <h3 class="modal-title w-100 asap_regular" id="exampleModalLongTitle" style="font-size: 26px"><b>Validade alterada</b></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-justify asap_regular">
                    <!-- Mostra mensagem no modal -->
                    <div class="container" style="font-size: 1.25em">
                        <table class="table table-hover table-striped">
                            <tbody>
                                <tr>
                                    <td class="text-right">Código</td>
                                    <td width="65%"><b id="id_produto_editado"></b></td>
                                </tr>
                                <tr>
                                    <td class="text-right">Produto</td>
                                    <td width="65%" style="word-break: break-word"><b id="produto_editado"></b></td>
                                </tr>
                                <tr>
                                    <td class="text-right">Validade antiga</td>
                                    <td width="65%"><b id="validade_antiga"></b></td>
                                </tr>
                                <tr>
                                    <td class="text-right">Validade nova</td>
                                    <td width="65%"><b><span id='validade_nova' class='text-success'></span></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success asap_regular" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal: modalErro-->
    <div class="modal fade" id="modalErro" tabindex="-1" role="dialog" aria-labelledby="modalErroTitle" aria-hidden="true" onkeypress="$('#modalErro').modal('toggle');">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title w-100 asap_regular text-warning" id="exampleModalLongTitle" style="font-size: 26px"><b>Validade não alterada</b></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-justify asap_regular">
                    <!-- Mostra mensagem no modal -->
                    <div class="container">
                        <h5>A validade nova (<span id="validade_nova_erro"></span>) é menor igual a <span id="data_hoje"></span>.</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-warning asap_regular" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="footer1" class="footer" style="margin-bottom: -250px">
        <!-- Footer Elements -->
        <div style="background-color: #3e4551; padding: 16px">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2 offset-md-3 text-right">
                        <a href="https://www.facebook.com/sakamototen/" class="btn-social btn-facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                    <div class="col-md-2 text-center">
                        <a href="https://github.com/leandro1st" class="btn-social btn-github"><i class="fab fa-github"></i></a>
                    </div>
                    <div class="col-md-2">
                        <a href="https://www.instagram.com/sakamototen/" class="btn-social btn-instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Elements -->
        <!-- Copyright -->
        <div class="text-center asap_regular" style="background-color: #323741; padding: 16px; color: #dddddd">©
            2020 Copyright –
            <a href="https://sakamototen.com.br/" style="text-decoration: none"> SakamotoTen – Produtos Orientais e
                Naturais</a>
        </div>
        <!-- Copyright -->
    </footer>
    <!-- Footer -->
</body>

</html>