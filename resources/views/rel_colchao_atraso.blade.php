<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container" style="margin-top: 20px;">

        <div class="jumbotron jumbotron-fluid">
          <div class="container">
            <h1 class="display-4"><strong>Projecao</strong></h1>
        </div>
    </div>

    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Banco</th>
                <th>Contrato</th>
                <th>Cpf / Cnpj</th>
                <th>Atraso original</th>
                <th>Vencimento</th>
                <th>Nr Parcela</th>
                <th>Nr Plano</th>
                <th>Vl. Parcela</th>
                <th>Vencimento</th>
                <th>Login</th>
                <th>Atraso acordo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dados as $registro)
            <tr>
                <td>{{ utf8_encode($registro->Banco) }}</td>
                <td>{{ utf8_encode($registro->numero_contrato) }}</td>
                <td>{{ utf8_encode($registro->Cpfcnpj) }}</td>
                <td>{{ utf8_encode($registro->atraso_original) }}</td>
                <td>{{ $registro->Dt_Vencimento }}</td>
                <td>{{ $registro->Nr_Parcela }}</td>
                <td>{{ $registro->Nr_Plano }}</td>
                <td>{{ $registro->Vl_Parcela }}</td>
                <td>{{ $registro->Dt_Venc }}</td>
                <td>{{ $registro->Login }}</td>
                <td>{{ $registro->atraso_acordo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</body>
</html>