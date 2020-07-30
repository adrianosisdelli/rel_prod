<table>
	<tr>
		<th>Descricao</th>
		<th>CPF/CNPJ</th>
	</tr>
	@foreach ($contratos as $contrato)
	<tr>
		<td>{{ $contrato[0] }}</td>
		<td>{{ $contrato[1] }}</td>
	</tr>
	@endForeach
	
</table>