<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container" style="margin-top: 20px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">0061 - 01 - ANALÍTICO ACIONAMENTOS</li>
            </ol>
        </nav>
        <br>
        <form action="/report/01" method="POST">
            {{ csrf_field() }}
            <p><strong>Intervalo:</strong></p>
            <input name="data_inicial" type="date"> a 
            <input name="data_final" type="date">

            <br><br>
            <p><strong>Exibir ocorrência:</strong></p>
            <input name="exibir" type="text" list="exibir_ocorrencia" />
            <datalist id="exibir_ocorrencia">
                <option value="Sim">
                <option value="Não">
            </datalist>
            <br><br>

            <p><strong>Carteira:</strong></p>
            <select name="idCarteira">
                <option value="0" selected>Todas</option>
                <option value="1">Consig. Itaú</option> 
                <option value="2">Consig. BMG</option>
                <option value="4">Cartão BMG</option>
                <option value="8">Consig. Exonerado</option>
            </select>
            <br><br>
            <input type="submit" value="Gerar relatório">
        </form>
    </div>
</body>
</html>