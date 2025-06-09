<?php

$tipoUsuario = 'admin';
$nomeUsuario = 'joao';

$response = file_get_contents("http://localhost:5093/api/tarefas/admin/joao");
$tarefas = json_decode($response, true);


//deletar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id = $_POST['id_apagar'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $nomeUsuario = $_POST['nomeUsuario'];

    $url = "http://localhost:5093/api/tarefas/$id?tipoUsuario=$tipoUsuario&nomeUsuario=$nomeUsuario";

    $options = [
        'http' => [
            'method' => 'DELETE',
            'header' => "Content-Type: application/json"
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // echo "<div class='alert alert-success'>Tarefa $id excluída com sucesso!</div>";

    // Opcional: recarrega a página após exclusão
    header("Refresh:1");
    exit;
}

//atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mudar_status'])) {
    $id = $_POST['id_atualizar'];
    $tipoUsuario = $_POST['tipoUsuario'];
    $nomeUsuario = $_POST['nomeUsuario'];

    $url = "http://localhost:5093/api/tarefas/$id/status?tipoUsuario=$tipoUsuario&nomeUsuario=$nomeUsuario";

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json",
            'content' => json_encode([]) // corpo vazio
        ]
    ];


    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $error = error_get_last();
    print_r($error);

    if ($response === false) {
        echo "<div class='alert alert-danger'>Erro ao atualizar status.</div>";
    } else {
        // echo "<div class='alert alert-success'>Status atualizado com sucesso.</div>";
        header("Refresh:1");
        exit;
    }


    //criar

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $usuario = $_POST['usuario'];
    $titulo = $_POST['titulo'];

    $url = "http://localhost:5093/api/tarefas";

    // Monta o corpo JSON
    $dados = json_encode([
        "Usuario" => $usuario,
        "Titulo" => $titulo
    ]);

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => $dados
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $error = error_get_last();

    if ($response === false) {
        echo "<div class='alert alert-danger'>Erro ao criar tarefa.</div>";
    } else {
        // echo "<div class='alert alert-success'>Tarefa criada com sucesso!</div>";
        header("Refresh:1");
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            text-align: right;
            margin-bottom: 10px;
        }

        .search-bar input {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .status-pendente {
            color: #ffc107;
            font-weight: bold;
        }

        .status-concluido {
            color: #28a745;
            font-weight: bold;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        select {
            padding: 5px;
            border-radius: 5px;
        }

        .btn .btn-status.concluido {
            background-color: #28a745;
            /* verde */
            color: white;
        }

        .btn-status.pendente {
            background-color: #ffc107;
            /* amarelo */
            color: black;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>ToDo List</h1>
        <div class="d-flex flex-row justify-content-between align-items-center">
            <form method="post" class="d-flex gap-1 column">
                <input type="hidden" name="criar" value="1">
                <label for="usuario"></label>
                <input type="text" id="usuario" name="usuario" placeholder="Nome do usuario" class="form-control" required>

                <label for="titulo"></label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo da tarefa" class="form-control" required>

                <button type="submit" class="btn btn-success col-3">Criar Tarefa</button>
            </form>
            <div class="search-bar">
                <label for="search"></label>
                <input type="text" id="search" placeholder="Pesquisar Título">
            </div>
        </div>

        <table id="tabela-tarefas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contador = 1;
                foreach ($tarefas as $tarefa): ?>
                    <?php
                    $id = htmlspecialchars($tarefa['id']);
                    $titulo = htmlspecialchars($tarefa['titulo']);
                    $usuario = htmlspecialchars($tarefa['usuario']);
                    $status = $tarefa['status'] ? 'concluido' : 'pendente'; // verdadeiro = concluído, falso = pendente

                    ?>
                    <tr>
                        <td><?= $contador++ ?></td>
                        <td><?= $titulo ?></td>
                        <td><?= $usuario ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id_atualizar" value="<?= $id ?>">
                                <input type="hidden" name="tipoUsuario" value="admin">
                                <input type="hidden" name="nomeUsuario" value="<?= $usuario ?>">
                                <button type="submit" name="mudar_status" class="btn btn-sm <?= $status === 'concluido' ? 'btn-success' : 'btn-warning' ?>">
                                    <?= $status = $tarefa['status'] ? 'concluido' : 'pendente'; // booleano → string
                                    ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id_apagar" value="<?= $id ?>">
                                <input type="hidden" name="tipoUsuario" value="admin">
                                <input type="hidden" name="nomeUsuario" value="<?= $usuario ?>">
                                <button type="submit" name="excluir" class="btn-delete">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#tabela-tarefas tbody tr');

            linhas.forEach(function(linha) {
                const titulo = linha.children[1].textContent.toLowerCase(); // Coluna 1 = Título
                if (titulo.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>