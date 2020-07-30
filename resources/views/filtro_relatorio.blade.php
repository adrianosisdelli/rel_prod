<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

	<style type="text/css">
		body {
			margin-top: 10px;
		}

		.ocorrencia_negativa {

			color: #953a4b;
		}
	</style>
	<title></title>
</head>
<body>
	<div class="container-fluid">

		<div class="jumbotron jumbotron-fluid">
			<div class="container">
				<h1 class="display-8">0061 - 01 - ANALÍTICO ACIONAMENTOS</h1>
				<p class="lead">Entre {{ $dataInicial }} e {{ $dataFinal }}</p>
				<ul>
					<li>Acionamentos <span class="badge badge-light">{{ $contagem }}</span></li>
				</ul>
				<ul class="nav">
					<li class="nav-item" style="margin-right: 10px;">
						<button id="btnInicio" type="button" class="btn btn-info">Página inicial</button>
  					</li>
  					<li class="nav-item">
					  <div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Exportar
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<button id="btnExportExcel" class="dropdown-item">Excel (.xls)</a>
							</div>
						</div>
  					</li>
				</ul>
				
			</div>
		</div>

		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">Relatórios</a></li>
				<li class="breadcrumb-item"><a href="#">0061 - 01 - ANALÍTICO ACIONAMENTOS</a></li>
			</ol>
		</nav>
		<form style="display: hidden" action="/report/exportar/061/teste" method="POST" id="formsql">
			@csrf
        	<input type="hidden" id="sql_field" name="sql" value="{{ $consultaRelatório }}"/>
    	</form>

		<table style="font-size: 12px;" class="table table-hover w-auto small">
			<thead>
				<th>Descrição</th>
				<th>CPF/CNPJ</th>
				<th>Contrato</th>
				<th>Data</th>
				<th>Descrição</th>
				<th>Ocorrência Positiva</th>
				<th>Complemento</th>
				<th>Tipo Complemento</th>
				<th>Observação</th>
				<th>CPC</th>
				<th>Cód. Ocorrência</th>
			</thead>
			<tbody>
				@for ($i = 0; $i < $indiceMaxTabela; $i++)
				<tr>
					<td>{{ utf8_encode($contratos[$i]->Descricao) }}</td>
					<td><a href="http://tsp.datacob.com.br/Historico/{{ $contratos[$i]->Id_Contrato }}" target="_blank">{{ utf8_encode($contratos[$i]->Cpfcnpj) }}</a></td>
					<td>{{ utf8_encode($contratos[$i]->Numero_Contrato) }}</td>
					<td>{{ utf8_encode($contratos[$i]->Dt_Ocorr) }}</td>
					<td>{{ utf8_encode($contratos[$i]->descr) }}</td>
					<td @if($contratos[$i]->Ocorrencia_Positiva === 0) class="ocorrencia_negativa" @endif><strong>@if ($contratos[$i]->Ocorrencia_Positiva === 1) Sim @else Não @endif</strong></td>
					<td>{{ utf8_encode($contratos[$i]->Descricao_Complemento) }}</td>
					<td>{{ utf8_encode($contratos[$i]->Tipo_Compl) }}</td>
					<td>{{ utf8_encode($contratos[$i]->Observacao) }}</td>
					<td>@if ($contratos[$i]->CPC === 1) Sim @else Não @endif</td>
					<td><strong>{{ utf8_encode($contratos[$i]->Cod_Ocorr_Sistema) }}</strong></td>
				</tr>
				@endfor
			</tbody>
		</table>
	</div>

	<script>
		$(document).ready(function(){
			$("#btnExportExcel").click(function(){
				$("#formsql").submit();
			});

			$("#btnInicio").click(function(){
				window.location.href = 'http://192.168.0.163';
			});
		});
	</script>
</body>
</html>