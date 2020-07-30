<?php


use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\PHPExcel_Cell_DataType; 

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/tests', function() {

	function teste($registro) {

		if ($registro->parcelas_acordo > 1) return "parcelamento";
		else if ($registro->parcelas_acordo == 1 && $registro->p_em_atraso == $registro->p_originais_acordo && $registro->p_originais_acordo < $registro->p_originais_abertas) return "atualizacao";
		else if (($registro->parcelas_acordo == 1 && $registro->p_em_atraso == $registro->p_originais_acordo && $registro->p_originais_acordo == $registro->p_originais_abertas)) return "quitacao";
		else if ($registro->parcelas_acordo == 1 && $registro->p_em_atraso < $registro->p_originais_abertas) return "avulso";
		else return "indef.";
	}

	$config = (object) array(
		'xCode' => '0000x12545AK'
		, 'authorName' => 'Adriano Sisdelli Costa'
		, 'cache_key_document' => 'doc'
		, 'disponiveis' => (object) array(1, 2)
		, 'sql_command' => "select distinct g.descricao as Banco , c.numero_contrato , f.Cpfcnpj , datediff(day,p.Dt_Vencimento,getdate()) as atraso_original , t1.Nr_Acordo , t1.Situacao , t1.Status , pa.Dt_Pagamento , pa.Dt_Venc_Boleto , pa.Dt_Vencimento , t1.Dt_Liberacao , pa.Nr_Parcela , pa.Nr_Plano , pa.Vl_Parcela , b.Vl_Boleto , b.Dt_Emiss as Emissao_Boleto , b.Dt_Venc , b.Dt_Pago , u.Login , datediff(day,pa.dt_vencimento,getdate()) as atraso_acordo , b.Tipo_Envio , t1.Tipo_Envio ,( select count(*) from cob.Parcela_Acordo pac left join cob.Acordo ac on pac.Id_Acordo = ac.Id_Acordo left join cob.Contrato ct on ac.Id_Agrupamento = ct.Id_Agrupamento where ac.Id_Acordo = t1.Id_Acordo) as parcelas_acordo , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' and DATEDIFF(day, cob.Parcela.Dt_Vencimento, getdate()) > 0 ) p_em_atraso , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' and cob.parcela.Id_Acordo is not null ) p_originais_acordo , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' ) p_originais_abertas from cob.contrato c with (nolock) left join cob.financiado f with (nolock) on c.Id_Financiado = f.Id_Financiado left join cob.acordo t1 with (nolock) on c.Id_Agrupamento = t1.Id_Agrupamento left join cob.Parcela_Acordo pa with (nolock) on t1.Id_Acordo = pa.Id_Acordo left join par.grupo g with (nolock) on c.Id_Grupo = g.Id_Grupo left join cob.Boleto b with (nolock) on pa.Id_Boleto = b.Id_Boleto left join cob.Negociacao n with (nolock) on b.Id_Negociacao = n.Id_Negociacao left join usu.Usuario u with (nolock) on n.Id_Usuario = u.Id_Usuario left join cob.parcela p with (nolock) on c.Id_Primeira_Parcela = p.Id_Parcela where 1=1 and pa.Nr_Parcela = 1 and u.Id_Usuario <> 170 and pa.Dt_Vencimento between '2020-05-19 00:00:00.000' and '2020-05-19 09:59:59.000' and c.Id_Grupo = 1 "

	);

	$results = DB::select($config->sql_command);

	foreach ($results as $registro) {

		echo(teste($registro) . '<br>');
	}
	

});




Route::get('/', function () {
	return view('welcome');
});

Route::get('/report/01/filter', function() {

	return view('report01_filter');
});




Route::post('/report/01', function() {

	$sql = "select distinct [PGrupoIG].[Descricao] as [Grupo], CASE WHEN [CHistoricoIH].[Id_Usuario_Inseriu] = [UUsuarioIU].[Id_Usuario] THEN [UUsuarioIU].[Login] END as [Usuario], [CFinanciadoIF].[Cpfcnpj] as [Cpf/Cnpj], [CContratoIC].[Numero_Contrato] as [Contrato], [CHistoricoIH].[Dt_Ocorr] as [Data Histórico], [POcorrenciaSistemaIOS].[Descricao] as [Ocorrência], isnull([POcorrenciaSistemaIOS].[Ocorrencia_Positiva], 0) as [Ocorrencia Positiva], [POcorrenciaSistemaIOS].[Descricao_Complemento] as [Descrição Complemento], [POcorrenciaSistemaIOS].[Tipo_Compl] as [Formato Complemento], [CHistoricoIH].[Complemento] as [Complemento Histórico], isnull([POcorrenciaSistemaIOS].[CPC], 0) as [CPC], [POcorrenciaSistemaIOS].[Cod_Ocorr_Sistema] as [Código Ocorrência], [CEnderecoIE].[Uf] as [Uf] from [Cob].[Contrato] as [CContratoIC] with(nolock) right join [Par].[Grupo] as [PGrupoIG] with(nolock) on [PGrupoIG].[Id_Grupo] = [CContratoIC].[Id_Grupo] and [PGrupoIG].[Id_Cliente_Web] = 88 right join [Cob].[Financiado] as [CFinanciadoIF] with(nolock) on [CFinanciadoIF].[Id_Financiado] = [CContratoIC].[Id_Financiado] and [CFinanciadoIF].[Id_Cliente_Web] = 88 right join [Cob].[Endereco] as [CEnderecoIE] with(nolock) on [CEnderecoIE].[Id_Endereco] = [CFinanciadoIF].[Id_Endereco_Carta] left join(select [ENDERECO_SUPER].[Id_Endereco] from [Cob].[Endereco] as [ENDERECO_SUPER] with(nolock) inner join [Cob].[Endereco_Comercial] as [ENDERECO_COMERCIAL_SUB] with(nolock) on [ENDERECO_COMERCIAL_SUB].[Id_Endereco] = [ENDERECO_SUPER].[Id_Endereco]) as [CEnderecoComercialIE] on [CEnderecoComercialIE].[Id_Endereco] = [CEnderecoIE].[Id_Endereco] inner join [Cob].[Historico] as [CHistoricoIH] with(nolock) on [CHistoricoIH].[Id_Contrato] = [CContratoIC].[Id_Contrato] inner join [Par].[Ocorrencia_Sistema] as [POcorrenciaSistemaIOS] with(nolock) on [POcorrenciaSistemaIOS].[Id_Ocorrencia_Sistema] = [CHistoricoIH].[Id_Ocorrencia_Sistema] and [POcorrenciaSistemaIOS].[Id_Cliente_Web] = 88 left join [Aud].[Registro_Atendimento] as [ARegistroAtendimentoIRA] with(nolock) on [ARegistroAtendimentoIRA].[Id_Registro_Atendimento] = [CHistoricoIH].[Id_Registro_Atendimento] and [ARegistroAtendimentoIRA].[Id_Cliente_Web] = 88 right join [Usu].[Usuario] as [UUsuarioIU] with(nolock) on [UUsuarioIU].[Id_Usuario] = [ARegistroAtendimentoIRA].[Id_Usuario] and [UUsuarioIU].[Id_Cliente_Web] = 88 where ([CContratoIC].[Id_Cliente_Web] = 88) and (( ( [CHistoricoIH].[Dt_Ocorr] between cast('[data_inicial] 00:00:00' as datetime) And cast('[data_final] 23:59:00' as datetime)) ) and ( [POcorrenciaSistemaIOS].[Descricao] not in ('Emissao Acordo' , 'Emissao Boleto') ) and ( isnull([POcorrenciaSistemaIOS].[Exibir_Ocorrencia], 0) like '%1%')) order by [CFinanciadoIF].[Cpfcnpj] asc  ";


	if ($_POST['idCarteira'] != 0) {

		$sql = str_replace('[carteira]', $_POST['idCarteira'], $sql);
	} 
	else {

		$sql = str_replace('[carteira]', '1, 2, 4, 8', $sql);
	}

	
	$sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = str_replace('[data_final]', $_POST['data_final'], $sql);


	$sql = $sql. ' order by h.Dt_Ocorr asc';
	//var_dump($sql); die();

	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:K1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Grupo');
	$sheet->setCellValue('B1', 'Usuario');
	$sheet->setCellValue('C1', 'Cpf/Cnpj');
	$sheet->setCellValue('D1', 'Contrato');
	$sheet->setCellValue('E1', 'Data Historico');
	$sheet->setCellValue('F1', 'Ocorrência');
	$sheet->setCellValue('G1', 'Ocorrência Positiva');
	$sheet->setCellValue('H1', 'Descrição Complemento');
	$sheet->setCellValue('I1', 'Complemento Histórico');
	$sheet->setCellValue('J1', 'CPC');
	$sheet->setCellValue('K1', 'Código Ocorrência');
	
	foreach($contratos as $contrato) {
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->grupo).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->usuario).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->cpfcnpj).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->contrato).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->dt_historico).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->ocorrencia).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->ocorrencia_positiva).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->descricao_complemento).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->complemento_historico).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->cpc).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->codigo_ocorrencia).'"');

		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_analitico_acionamentos.xlsx');

	return response()->download('rel_analitico_acionamentos.xlsx')->deleteFileAfterSend();



});

// -----------------------------------------------




Route::get('/rel_colchao_atraso', function() {

	$sql = "select distinct g.descricao as Banco ,c.numero_contrato , f.Cpfcnpj , datediff(day,p.Dt_Vencimento,getdate()) as atraso_original , pa.Dt_Vencimento , pa.Nr_Parcela , pa.Nr_Plano , pa.Vl_Parcela , b.Dt_Venc , u.Login , datediff(day,pa.dt_vencimento,getdate()) as atraso_acordo from cob.contrato c with(nolock) left join cob.financiado f with (nolock) on c.Id_Financiado = f.Id_Financiado left join cob.acordo t1 with (nolock) on c.Id_Agrupamento = t1.Id_Agrupamento left join cob.Parcela_Acordo pa with (nolock) on t1.Id_Acordo = pa.Id_Acordo left join par.grupo g with (nolock) on c.Id_Grupo = g.Id_Grupo left join cob.Boleto b with (nolock) on pa.Id_Boleto = b.Id_Boleto left join cob.Negociacao n with (nolock) on b.Id_Negociacao = n.Id_Negociacao left join usu.Usuario u with (nolock) on n.Id_Usuario = u.Id_Usuario left join cob.parcela p with (nolock) on c.Id_Primeira_Parcela = p.Id_Parcela where 1=1 and g.Id_Grupo in (1,2,4,8) and pa.Nr_Parcela > 1 and pa.Dt_Pagamento is null and b.Dt_Pago is null and c.Contrato_Aberto = 1 and t1.Situacao = 'L' and datediff(day,pa.dt_vencimento,getdate()) between 1 and 14 and not b.Dt_Venc >= '[data_vencimento]'";



	$sql = str_replace('[data_vencimento]', date("Y-m-d"), $sql);

	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:L1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Banco');
	$sheet->setCellValue('B1', 'Nr_Contrato');
	$sheet->setCellValue('C1', 'CpfCnpj');
	$sheet->setCellValue('D1', 'atraso_original');
	$sheet->setCellValue('E1', 'dt_vencimento');

	$sheet->setCellValue('F1', 'Nr_Parcela');
	$sheet->setCellValue('G1', 'Nr_Plano');
	$sheet->setCellValue('H1', 'Vl_Parcela');
	$sheet->setCellValue('I1', 'Dt_Venc');
	$sheet->setCellValue('J1', 'Login');
	$sheet->setCellValue('K1', 'atraso_acordo');
	
	foreach($contratos as $contrato) {
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Banco).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->numero_contrato).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->atraso_original).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Vencimento).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Parcela).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Plano).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Parcela).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Venc).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Login).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->atraso_acordo).'"');

		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_colchao_atraso.xlsx');

	return response()->download('rel_colchao_atraso.xlsx')->deleteFileAfterSend();
});

Route::get('/rel_emissao', function() {

	$sql = "select distinct g.descricao as Banco ,c.numero_contrato , f.Cpfcnpj , t1.Nr_Acordo , pa.Dt_Vencimento , pa.Nr_Parcela , pa.Nr_Plano , datediff(day,pa.dt_vencimento,getdate()) as atraso_acordo , t1.Tipo_Envio from cob.contrato c with(nolock) left join cob.financiado f with (nolock) on c.Id_Financiado = f.Id_Financiado left join cob.acordo t1 with (nolock) on c.Id_Agrupamento = t1.Id_Agrupamento left join cob.Parcela_Acordo pa with (nolock) on t1.Id_Acordo = pa.Id_Acordo left join par.grupo g with (nolock) on c.Id_Grupo = g.Id_Grupo left join cob.Boleto b with (nolock) on pa.Id_Boleto = b.Id_Boleto left join cob.Negociacao n with (nolock) on b.Id_Negociacao = n.Id_Negociacao left join usu.Usuario u with (nolock) on n.Id_Usuario = u.Id_Usuario left join cob.parcela p with (nolock) on c.Id_Primeira_Parcela = p.Id_Parcela where 1=1 and g.Id_Grupo in (1,2,4,8) and pa.Nr_Parcela > 1 and pa.Dt_Vencimento between '2020-07-01 00:00:00' and '2020-07-31 23:59:59' and pa.Dt_Pagamento is null and c.Contrato_Aberto = 1 and t1.Situacao = 'L' and u.login is null ";

	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:L1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Banco');
	$sheet->setCellValue('B1', 'Nr_Contrato');
	$sheet->setCellValue('C1', 'CpfCnpj');
	$sheet->setCellValue('D1', 'Nr_Acordo');
	$sheet->setCellValue('E1', 'Dt_Vencimento');
	$sheet->setCellValue('F1', 'Nr_Parcela');
	$sheet->setCellValue('G1', 'Nr_Plano');
	$sheet->setCellValue('H1', 'atraso_acordo');
	$sheet->setCellValue('I1', 'Tipo_Envio');
	
	foreach($contratos as $contrato) {
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Banco).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->numero_contrato).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Acordo).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Vencimento).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Parcela).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Plano).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->atraso_acordo).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Tipo_Envio).'"');

		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_emissao_colchao.xlsx');

	return response()->download('rel_emissao_colchao.xlsx')->deleteFileAfterSend();
});


Route::get('/report/acordos_preventivo/filter', function(){


	return view('filtros/boletos_preventivo_filter');
});




Route::post('/report/acordos_preventivo', function() {

	$sql = "select grp.Descricao as Banco , fin.Cpfcnpj , ct.Numero_Contrato , neg.Dt_Liberacao , bol.Dt_Venc , DATEDIFF(day,(select cob.Parcela.Dt_Vencimento from cob.Parcela where cob.Parcela.Id_Parcela = ct.Id_Primeira_Parcela), GETDATE()) atraso , bol.Vl_Boleto , us.Login , bol.Status , (select min(Parcela_Acordo.Nr_Parcela) from cob.Parcela_Acordo where cob.Parcela_Acordo.Dt_Pagamento is null and cob.Parcela_Acordo.Id_Acordo = aco.Id_Acordo) Nr_Parcela , (select max(Parcela_Acordo.Nr_Parcela) from cob.Parcela_Acordo where cob.Parcela_Acordo.Dt_Pagamento is null and cob.Parcela_Acordo.Id_Acordo = aco.Id_Acordo) Plano_Parcela , (case when (select max(Parcela_Acordo.Nr_Parcela) from cob.Parcela_Acordo where cob.Parcela_Acordo.Dt_Pagamento is null and cob.Parcela_Acordo.Id_Acordo = aco.Id_Acordo) = 1 then 'avulso' else 'colchao' end) tipo from cob.Boleto bol join cob.Negociacao neg on bol.Id_Negociacao = neg.Id_Negociacao join cob.Contrato ct on neg.Id_Agrupamento = ct.Id_Contrato join par.Grupo grp on ct.Id_Grupo = grp.Id_Grupo join cob.Financiado fin on ct.Id_Financiado = fin.Id_Financiado join usu.Usuario us on neg.Id_Usuario = us.Id_Usuario join cob.Acordo aco on neg.Id_Acordo = aco.Id_Acordo where ct.Contrato_Aberto = 1 and us.Login not in ('boletagem','jhonathan_queiroz','natalia_trevisani','Stefany_mendes') and bol.status = 'A' and bol.Dt_Venc between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000'"; 

	if ($_POST['idCarteira'] != 0) {

		$sql = $sql.' and grp.Id_Grupo = [carteira]';
		$sql = str_replace('[carteira]', $_POST['idCarteira'], $sql);
	} 

	
	$sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = str_replace('[data_final]', $_POST['data_final'], $sql);

	$sql = $sql." order by grp.Descricao desc , ct.Numero_Contrato , bol.Dt_Venc ";

	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:L1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Banco');
	$sheet->setCellValue('B1', 'Cpfcnpj');
	$sheet->setCellValue('C1', 'Numero_contrato');
	$sheet->setCellValue('D1', 'Dt_Liberacao');
	$sheet->setCellValue('E1', 'dt_vencimento');
	$sheet->setCellValue('F1', 'atraso');
	$sheet->setCellValue('G1', 'Vl_Boleto');
	$sheet->setCellValue('H1', 'Login');
	$sheet->setCellValue('I1', 'status');
	$sheet->setCellValue('J1', 'Nr_Parcela');
	$sheet->setCellValue('K1', 'Plano_Parcela');
	$sheet->setCellValue('L1', 'tipo');
	
	foreach($contratos as $contrato) {
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Banco).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Numero_Contrato).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Liberacao).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Venc).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->atraso).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Boleto).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Login).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Status).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Parcela).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->Plano_Parcela).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->tipo).'"');


		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_acordos_preventivo.xlsx');

	return response()->download('rel_acordos_preventivo.xlsx')->deleteFileAfterSend();
});



Route::get('/rel_pagamento', function() {

	$sql = "select distinct g.descricao as Banco ,c.numero_contrato , f.Cpfcnpj , t1.Nr_Acordo , pa.Dt_Pagamento , pa.Dt_Vencimento , pa.Nr_Parcela , pa.Nr_Plano , pa.Vl_Parcela , b.Dt_Venc , b.Dt_Pago , u.Login , b.Tipo_Envio , t1.Tipo_Envio TTipo_Envio from cob.contrato c with(nolock) left join cob.financiado f with (nolock) on c.Id_Financiado = f.Id_Financiado left join cob.acordo t1 with (nolock) on c.Id_Agrupamento = t1.Id_Agrupamento left join cob.Parcela_Acordo pa with (nolock) on t1.Id_Acordo = pa.Id_Acordo left join par.grupo g with (nolock) on c.Id_Grupo = g.Id_Grupo left join cob.Boleto b with (nolock) on pa.Id_Boleto = b.Id_Boleto left join cob.Negociacao n with (nolock) on b.Id_Negociacao = n.Id_Negociacao left join usu.Usuario u with (nolock) on n.Id_Usuario = u.Id_Usuario left join cob.parcela p with (nolock) on c.Id_Primeira_Parcela = p.Id_Parcela where 1=1 and g.Id_Grupo in (1,2,4,8) and pa.Nr_Parcela > 1 and pa.Dt_Pagamento between '[data] 00:00:00' and '[data] 23:59:59' and b.Dt_Pago between '[data] 00:00:00' and '[data] 23:59:59'  and not pa.Dt_Pagamento is null and not b.Dt_Pago is null";

	
	$data = date('Y-m-d H:i:s');
	$data = date('w', strtotime($data . ' -1 day'));

	if ($data == 0) {

		$data = date('Y-m-d H:i:s');
		$data = date('Y-m-d', strtotime($data . ' -3 day'));
	}
	
	else if ($data == 6) {

		$data = date('Y-m-d H:i:s');
		$data = date('Y-m-d', strtotime($data . ' -2 day'));
	}

	else {

		$data = date('Y-m-d H:i:s');
		$data = date('Y-m-d', strtotime($data . ' -1 day'));

	}

	$sql = str_replace('[data]', $data, $sql);

	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:N1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Banco');
	$sheet->setCellValue('B1', 'numero_contrato');
	$sheet->setCellValue('C1', 'CpfCnpj');
	$sheet->setCellValue('D1', 'Nr_Acordo');
	$sheet->setCellValue('E1', 'Dt_Pagamento');
	$sheet->setCellValue('F1', 'Dt_Vencimento');
	$sheet->setCellValue('G1', 'Nr_Parcela');
	$sheet->setCellValue('H1', 'Nr_Plano');
	$sheet->setCellValue('I1', 'Vl_Parcela');
	$sheet->setCellValue('J1', 'Dt_Venc');
	$sheet->setCellValue('K1', 'Dt_Pago');
	$sheet->setCellValue('L1', 'Login');
	$sheet->setCellValue('M1', 'BTipo_Envio');
	$sheet->setCellValue('N1', 'TTipo_Envio');

	
	foreach($contratos as $contrato) {
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Banco).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->numero_contrato).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Acordo).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Pagamento).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Vencimento).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Parcela).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Plano).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Parcela).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Venc).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Pago).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->Login).'"');
		$sheet->setCellValue('M'.$indiceLinha, '="'.utf8_encode($contrato->Tipo_Envio).'"');
		$sheet->setCellValue('N'.$indiceLinha, '="'.utf8_encode($contrato->TTipo_Envio).'"');

		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_pagamento_colchao.xlsx');

	return response()->download('rel_pagamento_colchao.xlsx')->deleteFileAfterSend();
});


Route::get('/report/acordos_preventivo/filter', function(){


	return view('filtros/boletos_preventivo_filter');
});


Route::get('/report/ult_acionamento_humano/filter', function(){


	return view('filtros/ult_acionamento_humano_filter');
});

Route::get('/report/ult_acionamento_alo/filter', function(){


	return view('filtros/ult_acionamento_alo_filter');
});



Route::get('/report/tipo_acordo/filter', function(){


	return view('filtros/tipo_acordo_filter');
});

Route::get('/report/ligacao_maquina/filter', function(){

	return view('filtros/ligacao_maquin_filter');
});

Route::post('/report/tipo_acordo', function() {

	if ($_POST['data_inicial'] == "" || $_POST['data_final'] == "") {

		return view('filtros/tipo_acordo_filter');
	}

	$sql = "select distinct g.descricao as Banco , c.numero_contrato , f.Cpfcnpj , datediff(day,p.Dt_Vencimento,getdate()) as atraso_original , t1.Nr_Acordo , t1.Situacao , t1.Status , pa.Dt_Pagamento , pa.Dt_Venc_Boleto , pa.Dt_Vencimento , t1.Dt_Liberacao , pa.Nr_Parcela , pa.Nr_Plano , pa.Vl_Parcela , b.Vl_Boleto , b.Dt_Emiss as Emissao_Boleto , b.Dt_Venc , b.Dt_Pago , u.Login , datediff(day,pa.dt_vencimento,getdate()) as atraso_acordo , b.Tipo_Envio , t1.Tipo_Envio ,( select count(*) from cob.Parcela_Acordo pac left join cob.Acordo ac on pac.Id_Acordo = ac.Id_Acordo left join cob.Contrato ct on ac.Id_Agrupamento = ct.Id_Agrupamento where ac.Id_Acordo = t1.Id_Acordo) as parcelas_acordo , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' and DATEDIFF(day, cob.Parcela.Dt_Vencimento, getdate()) > 0 ) p_em_atraso , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' and cob.parcela.Id_Acordo is not null ) p_originais_acordo , ( select count(*) from cob.Parcela where cob.Parcela.Id_Contrato = c.Id_Contrato and cob.Parcela.Status = 'A' ) p_originais_abertas from cob.contrato c with (nolock) left join cob.financiado f with (nolock) on c.Id_Financiado = f.Id_Financiado left join cob.acordo t1 with (nolock) on c.Id_Agrupamento = t1.Id_Agrupamento left join cob.Parcela_Acordo pa with (nolock) on t1.Id_Acordo = pa.Id_Acordo left join par.grupo g with (nolock) on c.Id_Grupo = g.Id_Grupo left join cob.Boleto b with (nolock) on pa.Id_Boleto = b.Id_Boleto left join cob.Negociacao n with (nolock) on b.Id_Negociacao = n.Id_Negociacao left join usu.Usuario u with (nolock) on n.Id_Usuario = u.Id_Usuario left join cob.parcela p with (nolock) on c.Id_Primeira_Parcela = p.Id_Parcela where 1=1 and pa.Nr_Parcela = 1 and u.Id_Usuario <> 170 and pa.Dt_Vencimento between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000'";

	$sql = $sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = $sql = str_replace('[data_final]', $_POST['data_final'], $sql);

	if ($_POST['idCarteira']  > 0) {

		$sql = $sql . ' and c.Id_Grupo = '  . $_POST['idCarteira'];
	}


	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:U1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Banco');
	$sheet->setCellValue('B1', 'numero_contrato');
	$sheet->setCellValue('C1', 'Cpfcnpj');
	$sheet->setCellValue('D1', 'atraso_original');
	$sheet->setCellValue('E1', 'Nr_Acordo');
	$sheet->setCellValue('F1', 'Situacao');
	$sheet->setCellValue('G1', 'Status');
	$sheet->setCellValue('H1', 'Dt_Pagamento');
	$sheet->setCellValue('I1', 'Dt_Venc_Boleto');
	$sheet->setCellValue('J1', 'Dt_Vencimento');
	$sheet->setCellValue('K1', 'Dt_Liberacao');
	$sheet->setCellValue('L1', 'Nr_Parcela');
	$sheet->setCellValue('M1', 'Nr_Plano');
	$sheet->setCellValue('N1', 'Vl_Parcela');
	$sheet->setCellValue('O1', 'Vl_Boleto');
	$sheet->setCellValue('P1', 'Emissao_Boleto');
	$sheet->setCellValue('Q1', 'Dt_Venc');
	$sheet->setCellValue('R1', 'Dt_Pago');
	$sheet->setCellValue('S1', 'Login');
	$sheet->setCellValue('T1', 'atraso_acordo');
	$sheet->setCellValue('U1', 'tipo_acordo');

	
	foreach($contratos as $contrato) {
		
		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Banco).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->numero_contrato).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->atraso_original).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Acordo).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Situacao).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Status).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Pagamento).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Venc_Boleto).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Vencimento).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Liberacao).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Parcela).'"');
		$sheet->setCellValue('M'.$indiceLinha, '="'.utf8_encode($contrato->Nr_Plano).'"');
		$sheet->setCellValue('N'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Parcela).'"');
		$sheet->setCellValue('O'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Boleto).'"');
		$sheet->setCellValue('P'.$indiceLinha, '="'.utf8_encode($contrato->Emissao_Boleto).'"');
		$sheet->setCellValue('Q'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Venc).'"');
		$sheet->setCellValue('R'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Pago).'"');
		$sheet->setCellValue('S'.$indiceLinha, '="'.utf8_encode($contrato->Login).'"');
		$sheet->setCellValue('T'.$indiceLinha, '="'.utf8_encode($contrato->atraso_acordo).'"');
		$sheet->setCellValue('U'.$indiceLinha, '="'.utf8_encode(def_tipo_acordo($contrato)).'"');


		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_tipo_acordo.xlsx');

	return response()->download('rel_tipo_acordo.xlsx')->deleteFileAfterSend();

});

Route::post('/report/ligacao_maquina', function() {

	$sql = "select * from( select ult_ligacao.Id_Agrupamento id_agrupamento , ult_ligacao.ult_ligacao , c.Numero_Contrato numero_contrato , c.Id_Grupo id_grupo , f.Cpfcnpj cpf_cnpj , lf.Ddd ddd , lf.Fone fone , lf.Dt_Inicio dt_inicio , lf.Id_Campaign id_campaign , lf.Id_Ocorrencia_Sistema id_ocorrencia_sistema , os.Cod_Ocorr_Sistema cod_ocorrencia_sistema , os.CPC cpc , os.Descricao desc_ocorencia_sistema , us.Login usuario from (select ct_ligacao.Id_Agrupamento , (select max(f.Id_Ligacao_Fone) id_ligacao from fon.Ligacao_Fone f where f.Dt_Inicio between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000' and f.Id_Agrupamento = ct_ligacao.Id_Agrupamento) ult_ligacao from (select distinct f.Id_Agrupamento from fon.Ligacao_Fone f where f.Dt_Inicio between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000') ct_ligacao) ult_ligacao left join cob.Contrato c on ult_ligacao.Id_Agrupamento = c.Id_Contrato left join cob.Financiado f on c.Id_Financiado = f.Id_Financiado left join fon.Ligacao_Fone lf on ult_ligacao.ult_ligacao = lf.Id_Ligacao_Fone left join par.Ocorrencia_Sistema os on lf.Id_Ocorrencia_Sistema = os.Id_Ocorrencia_Sistema left join usu.Usuario us on lf.Id_Usuario = us.Id_Usuario) lig";

	$sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = str_replace('[data_final]', $_POST['data_final'], $sql);

	if (!($_POST['idCarteira'] == 0)) {

		$sql = $sql . ' where lig.Id_Grupo = ' . $_POST['idCarteira'];
	}


	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:O1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'id_agrupamento');
	$sheet->setCellValue('B1', 'banco');
	$sheet->setCellValue('C1', 'ult_ligacao');
	$sheet->setCellValue('D1', 'numero_contrato');
	$sheet->setCellValue('E1', 'id_grupo');
	$sheet->setCellValue('F1', 'cpf_cnpj');
	$sheet->setCellValue('G1', 'ddd');
	$sheet->setCellValue('H1', 'fone');
	$sheet->setCellValue('I1', 'dt_inicio');
	$sheet->setCellValue('J1', 'id_campaign');
	$sheet->setCellValue('K1', 'id_ocorrencia_sistema');
	$sheet->setCellValue('L1', 'cod_ocorrencia_sistema');
	$sheet->setCellValue('M1', 'cpc');
	$sheet->setCellValue('N1', 'desc_ocorencia_sistema');
	$sheet->setCellValue('O1', 'usuario');


	foreach($contratos as $contrato) {

		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->id_agrupamento).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.def_banco($contrato->id_grupo).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->ult_ligacao).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->numero_contrato).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->id_grupo).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->cpf_cnpj).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->ddd).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->fone).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->dt_inicio).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->id_campaign).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->id_ocorrencia_sistema).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->cod_ocorrencia_sistema).'"');
		$sheet->setCellValue('M'.$indiceLinha, '="'.utf8_encode($contrato->cpc).'"');
		$sheet->setCellValue('N'.$indiceLinha, '="'.utf8_encode($contrato->desc_ocorencia_sistema).'"');
		$sheet->setCellValue('O'.$indiceLinha, '="'.utf8_encode($contrato->usuario).'"');


		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_ligacao_maquina.xlsx');

	return response()->download('rel_ligacao_maquina.xlsx')->deleteFileAfterSend();
});











Route::post('/report/ult_acionamento_humano', function() {

	$sql = "with base(id_contrato, ult_historico_inserido) as ( select his.Id_Contrato , max(his.Id_Historico) ult_historico_inserido from cob.Historico his left join cob.Contrato ct on his.Id_Contrato = ct.Id_Contrato where CT.CONTRATO_ABERTO = 1 [grupo] group by his.Id_Contrato) select ct.Id_Grupo , grp.Descricao , ct.Numero_Contrato , fin.Cpfcnpj , DATEDIFF(day, par.Dt_Vencimento, getdate()) atraso , replace(par.Vl_Original, '.', ',') Vl_Original , replace(ct.Vl_Risco, '.', ',') Vl_Risco , convert(char, his.Dt_Ocorr, 103) Dt_Ocorr , his.Observacao , his.Id_Ocorrencia_Sistema , ct.Contrato_Aberto ,os.Descricao descr_ocorrencia from base left join cob.Historico his on base.ult_historico_inserido = his.Id_Historico left join cob.Contrato ct on base.id_contrato = ct.Id_Contrato left join cob.Financiado fin on ct.Id_Financiado = fin.Id_Financiado left join cob.Parcela par on ct.Id_Primeira_Parcela = par.Id_Parcela left join par.Grupo grp on ct.Id_Grupo = grp.Id_Grupo left join par.Ocorrencia_Sistema os on his.Id_Ocorrencia_Sistema = os.Id_Ocorrencia_Sistema where 1=1 and his.Id_Ocorrencia_Sistema in (2, 66, 78, 49, 72, 82, 117, 50, 73, 83, 51, 74, 84, 114, 96, 3, 68, 79, 91, 4, 61, 76, 5, 67, 98, 7, 69, 80, 41, 40, 97, 47, 48, 88, 89, 95, 10, 71, 81, 52, 75, 85, 103, 128, 106, 129, 102, 130, 104, 131, 105, 132, 107, 133, 108, 134, 12, 53) and his.Dt_Ocorr between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000'";

	$sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = str_replace('[data_final]', $_POST['data_final'], $sql);

	if (!($_POST['idCarteira'] == 0)) {

		$sql_id_carteira = 'and ct.Id_Grupo = ' . $_POST['idCarteira'];

		$carteira = str_replace('[grupo]', $sql_id_carteira, $sql);

		$sql = $carteira;
	}
	else {

		$sql = str_replace('[grupo]', '', $sql);
	}


	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:L1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Id_Grupo');
	$sheet->setCellValue('B1', 'Descricao');
	$sheet->setCellValue('C1', 'Numero_Contrato');
	$sheet->setCellValue('D1', 'Cpfcnpj');
	$sheet->setCellValue('E1', 'atraso');
	$sheet->setCellValue('F1', 'Vl_Original');
	$sheet->setCellValue('G1', 'Vl_Risco');
	$sheet->setCellValue('H1', 'Dt_Ocorr');
	$sheet->setCellValue('I1', 'Observacao');
	$sheet->setCellValue('J1', 'Id_Ocorrencia_Sistema');
	$sheet->setCellValue('K1', 'Contrato_Aberto');
	$sheet->setCellValue('L1', 'descr_ocorrencia');


	foreach($contratos as $contrato) {

		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Id_Grupo).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->Descricao).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Numero_Contrato).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->atraso).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Original).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Risco).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Ocorr).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Observacao).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Id_Ocorrencia_Sistema).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->Contrato_Aberto).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->descr_ocorrencia).'"');


		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_ult_acionamento_humano_cpc.xlsx');

	return response()->download('rel_ult_acionamento_humano_cpc.xlsx')->deleteFileAfterSend();
});



Route::post('/report/ult_acionamento_alo', function() {

	$sql = "with base(id_contrato, ult_historico_inserido) as ( select his.Id_Contrato , max(his.Id_Historico) ult_historico_inserido from cob.Historico his left join cob.Contrato ct on his.Id_Contrato = ct.Id_Contrato where CT.CONTRATO_ABERTO = 1 [grupo] group by his.Id_Contrato) select ct.Id_Grupo , grp.Descricao , ct.Numero_Contrato , fin.Cpfcnpj , DATEDIFF(day, par.Dt_Vencimento, getdate()) atraso , replace(par.Vl_Original, '.', ',') Vl_Original , replace(ct.Vl_Risco, '.', ',') Vl_Risco , convert(char, his.Dt_Ocorr, 103) Dt_Ocorr , his.Observacao , his.Id_Ocorrencia_Sistema , ct.Contrato_Aberto ,os.Descricao descr_ocorrencia from base left join cob.Historico his on base.ult_historico_inserido = his.Id_Historico left join cob.Contrato ct on base.id_contrato = ct.Id_Contrato left join cob.Financiado fin on ct.Id_Financiado = fin.Id_Financiado left join cob.Parcela par on ct.Id_Primeira_Parcela = par.Id_Parcela left join par.Grupo grp on ct.Id_Grupo = grp.Id_Grupo left join par.Ocorrencia_Sistema os on his.Id_Ocorrencia_Sistema = os.Id_Ocorrencia_Sistema where 1=1 and his.Id_Ocorrencia_Sistema in (93, 13, 6, 92, 8, 70, 9, 136, 63, 94, 87, 11, 14, 135) and his.Dt_Ocorr between '[data_inicial] 00:00:00.000' and '[data_final] 23:59:59.000'";

	$sql = str_replace('[data_inicial]', $_POST['data_inicial'], $sql);
	$sql = str_replace('[data_final]', $_POST['data_final'], $sql);

	if (!($_POST['idCarteira'] == 0)) {

		$sql_id_carteira = 'and ct.Id_Grupo = ' . $_POST['idCarteira'];

		$carteira = str_replace('[grupo]', $sql_id_carteira, $sql);

		$sql = $carteira;
	}
	else {

		$sql = str_replace('[grupo]', '', $sql);
	}


	$contratos = DB::select($sql);

	$indiceLinha = 2;

	$spreadshett = new Spreadsheet();
	$sheet = $spreadshett->getActiveSheet();

	$sheet->getStyle("A1:L1")->getFont()->setBold(true);

	$sheet->setCellValue('A1', 'Id_Grupo');
	$sheet->setCellValue('B1', 'Descricao');
	$sheet->setCellValue('C1', 'Numero_Contrato');
	$sheet->setCellValue('D1', 'Cpfcnpj');
	$sheet->setCellValue('E1', 'atraso');
	$sheet->setCellValue('F1', 'Vl_Original');
	$sheet->setCellValue('G1', 'Vl_Risco');
	$sheet->setCellValue('H1', 'Dt_Ocorr');
	$sheet->setCellValue('I1', 'Observacao');
	$sheet->setCellValue('J1', 'Id_Ocorrencia_Sistema');
	$sheet->setCellValue('K1', 'Contrato_Aberto');
	$sheet->setCellValue('L1', 'descr_ocorrencia');


	foreach($contratos as $contrato) {

		$sheet->setCellValue('A'.$indiceLinha, '="'.utf8_encode($contrato->Id_Grupo).'"');
		$sheet->setCellValue('B'.$indiceLinha, '="'.utf8_encode($contrato->Descricao).'"');
		$sheet->setCellValue('C'.$indiceLinha, '="'.utf8_encode($contrato->Numero_Contrato).'"');
		$sheet->setCellValue('D'.$indiceLinha, '="'.utf8_encode($contrato->Cpfcnpj).'"');
		$sheet->setCellValue('E'.$indiceLinha, '="'.utf8_encode($contrato->atraso).'"');
		$sheet->setCellValue('F'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Original).'"');
		$sheet->setCellValue('G'.$indiceLinha, '="'.utf8_encode($contrato->Vl_Risco).'"');
		$sheet->setCellValue('H'.$indiceLinha, '="'.utf8_encode($contrato->Dt_Ocorr).'"');
		$sheet->setCellValue('I'.$indiceLinha, '="'.utf8_encode($contrato->Observacao).'"');
		$sheet->setCellValue('J'.$indiceLinha, '="'.utf8_encode($contrato->Id_Ocorrencia_Sistema).'"');
		$sheet->setCellValue('K'.$indiceLinha, '="'.utf8_encode($contrato->Contrato_Aberto).'"');
		$sheet->setCellValue('L'.$indiceLinha, '="'.utf8_encode($contrato->descr_ocorrencia).'"');


		$indiceLinha++;
	}

	$writer = new Xlsx($spreadshett);
	$writer->save('rel_ult_acionamento_alo_cpc.xlsx');

	return response()->download('rel_ult_acionamento_alo_cpc.xlsx')->deleteFileAfterSend();
});



function def_tipo_acordo($registro) {

	if ($registro->parcelas_acordo > 1) return "parcelamento";
	else if ($registro->parcelas_acordo == 1 && $registro->p_em_atraso == $registro->p_originais_acordo && $registro->p_originais_acordo < $registro->p_originais_abertas) return "atualizacao";
	else if (($registro->parcelas_acordo == 1 && $registro->p_em_atraso == $registro->p_originais_acordo && $registro->p_originais_acordo == $registro->p_originais_abertas)) return "quitacao";
	else if ($registro->parcelas_acordo == 1 && $registro->p_em_atraso < $registro->p_originais_abertas) return "avulso";
	else return "indef.";
}

function def_banco($id_banco) {

	if ($id_banco == 1) {

		return 'Consig. Itaú';
	}
	else if ($id_banco == 2) {

		return 'Consig. BMG';
	}
	else if ($id_banco == 4) {

		return 'Cartão BMG';
	}
	else if ($id_banco == 8) {

		return 'Consig. Exonerado';
	}

	else {

		return 'Indef.';
	}
}
