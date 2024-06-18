<?php 
	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	//require_once("../../../../php/mysql.php");
	//require_once("../../../../php/utils.php");

	//$campo = $_GET['tabela'] == "reoferta_matricula" ? "id_matricula" : "id_inscricao";
	$sql = "SELECT
				tabela 
			FROM
				reoferta_recebimento
			WHERE
				id_registro = ?";
	$tabela = $coopex->prepare($sql);
	$tabela->bindParam(1, $_GET['id']);
	$tabela->execute();
	$tabela = $tabela->fetch(PDO::FETCH_OBJ);
	
	//print_r($tabela->tabela);

	$campo = $tabela->tabela == "reoferta_matricula" ? "id_matricula" : "id_inscricao";
	$tipo = $tabela->tabela == "reoferta_matricula" ? "RM" : "RP";

	$sql = "SELECT
				* 
			FROM
				reoferta
				INNER JOIN ".$tabela->tabela." USING ( id_reoferta )
				INNER JOIN coopex_usuario.usuario USING ( id_usuario )
				LEFT JOIN coopex_usuario.evento_pessoa USING ( id_usuario ) 
			WHERE
				$campo = ?";
	$reoferta = $coopex->prepare($sql);
	$reoferta->bindParam(1, $_GET['id']);
	$reoferta->execute();
	$reoferta = $reoferta->fetch(PDO::FETCH_OBJ);

	//print_r($reoferta);
	//selecionas os boletos das matrículas
	$sql = "SELECT
					*
				FROM
					coopex_cascavel.reoferta_recebimento 
					INNER JOIN reoferta_tipo_pagamento USING ( id_reoferta_tipo_pagamento ) 
				WHERE
					id_registro = ? 
					AND tabela = ?";
	$pagamento = $coopex->prepare($sql);
	$pagamento->bindParam(1, $_GET['id']);
	$pagamento->bindParam(2, $tabela->tabela);
	$pagamento->execute();
	//$total_registros = $pagamento->rowCount();

	//print_r($_GET['id']);
?>
<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Tesouraria</a></li>
		<li class="breadcrumb-item">Recebimentos</li>
		<li class="breadcrumb-item active">Recibo</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-sm-6">
			<i class='subheader-icon fal fa-print'></i> Recibo
			<small>
				Pagamento do Reoferta
			</small>
			<button onClick="window.print();" id="bt_imprimir" type="button" class="btn btn-info mt-3"><span class="fal fa-print mr-1"></span>Imprimir Comprovante</button>
		</h1>
	</div>
	<div class="container">
		<div data-size="A4">
			<div class="row">
				<div class="col-sm-12">
					<div class="d-flex align-items-center mb-5">
						<h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
							Sistema Coopex
							<small class="text-muted mb-0 fs-xs">
								Recebimento de Reofertas
							</small>
							<!-- barcode demo only -->
							<img id="barcode" alt="" class="position-absolute pos-top pos-right height-3 mt-1 hidden-md-down" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAdAAAABkCAYAAAA2cWHXAAAVyUlEQVR4Xu2d25XjyBFEtUbJiXVCtskJOSGjRofD5lkCi+S9UYVu/cT+7TRIFKIy45EAyT9+/fr16x/iv3/++a/DUf/9z79////r31///zpo+vfzqdLjXq+n86XrPa/7vE57vROUdr3n89jrnc6b7s8ZN8LhvD7CnfA5r9euh+ourbPdfSDcUxxof6d9SOuO+iCtR7t/hNd0HbSetH4JZ8sDu/VjefIufC2Ou9c18QP171SX57qg+p32j9Y11cUfFdAjNFNB2sYhYpwKhYSDiNA2PhVq2pAV0KextPuT4jsRBBGKFSQ6brWeichWBZ+um85rBYAEjHCz51k1dnSdKb4V0OuAONX/698roCeEKqDHQkodfBPoEzEiWBKmlFhTwvxuQ3g3wTeBfjZqq/VEycv286qBtK+zxonqrgn0CyECnjb+ruhPG0bObnVDU4IlvNLrIHwpmZIwTw5+et15/fZ6bdKw+2jXsUp403oroM9bSlTHNhnauqA6swJlJw1kJGxfWUFK+zytf8LP1jsdR/1B+zT1axPoCZkm0CbQR0lYoqWkeTfhEVHYZGmPI6FPCXOV4AlH+jsZulTA7L4TMd9lhGmfaL1kEFP8ds9nA449rgI6PMQ0NTA5p7uAJ8dLhdkE+kTI4jARsCXyVaK0+2jXsUp4FdAnAlaYSLCnfZ1wtsZj14DR+VPe2RW0Cuh1xzaBnnBpAm0CvRL0CuixUezobfehqbuIPxUAEjBaVwXU3bO1dTQZhruCEBmyyfBWQCugBwRSwrPElL5v74FeE1ATaBPoew2s9pUVJJrkrRrL9HV2vZTM7aSLJlWv81RAK6AV0DcEzsmCnKlNItTYkxGhRiaCI4dP65+cN73vXddr19d7oNc7ZUfOJGjWKJ/3nfbF1lEFdOjE9CY6bTSNTuh8HeF2hPuoMaojSxREIOe2IMJrAm0CbQL9C4GOcL8eDlp12hORpc6cHDMRmx2l0Hp3iXsiWOv07HUQvuRYrXDY9awKGuHVBPo52VjcKclS0iQjQn+nerT8Y3ngp/t4F18KJil+tJ90PiuM9rhp/XafJnw7wj0h0wTaBNoE+venXCfDUwG9/vypJWaaiP0UviRoFdBrCa2AVkAPCNjE1wR6HCV+V8K3yYgctp180CSGiNYmwdX1Tuuz57XCZicTtO+rEww6f7pPu4mwAloB/Y0AOb4m0CbQJtAmUBIwEiQr1MRHTaDHnSBDQsZiGsHb9z3XRRNoE2gT6BsCfQr3mKyvfffPfXk+CRUl4vP6rbBVQK/rwOK3u2/23qY9jiYeFdDTw0g0YrwLeHI81OCrjugu52pHX3aEQ7gTIZOATYRonbodid5FFLv42vUSQUz4WKLbxT3dd7uf1F8VUPezkxbHu/qCzmf52R5H/VEBrYBechQR5C7BrworEdvUqLtEbgXpLqLYxdeulwiiAvpEwO5rE2gT6GT63v+9I9wTSr0H2nugCdF+t0GpgO59JRwZtSkJVUAroBXQNwSmUenUKNR4q8nCJgEaKXSEe0wUduRnBckmlQrokWhX+2IXR3teu68V0ApoBbQCij/sbIlndVSbjmJ7D/TzPSsyfGQUJ3xX68Aal4mM0vWezzclSDtJagI9TpyoDqwBIUNE+2bvbdrjJv6y1zPVb0e4HeEeEOjnQI8jw9VJgCWQdNJRAf2cjCaBtQZwIsrUeFhivmuStGtQSNBS/Gz9T4JthdEeVwHt74FeCl06krw7MdjGSoU5Ta72ulK8KqDX1GyJb3Xf7X5a4rd1WgG9/malFL8KKCSz1dHL5KAtsVHDkPMjx2dHPzTqmJLFRAw0urSEZfG1BU4JyTbWKpHa11nCtXVGdbR6Pns9VKdT0kr7crcOLA67CYn6jf5u67QCWgF91ADxxKtOOsIFo0DRfyKyCugTgTRJkpFIcSWCJ8PRBHotKdbQ7RoGK/AV0M/7dBeOdxlLG4Sm/pxeT3w8GUzq88lYVUAroAcEUsKzzj5931R4SSjTRHcXUVhit0bNEgoRpk2qlBwt7vQ+dr0TQVqc7b5aY0VGgoh5tS5JKGw92fXZPk/rgfCz9U7HER6EQwX0P+7pxo5w+znQT8nZNjwJwqqgEFFYYbTH0TpTwjy/n10HCST9PRWACugTgUlYSHBs/dt+ssaJjEUT6BdCBDxt/DTjroBWQCug/TL5CmgFdDKP7//eEe4JpQpoBbQCWgGtgFZAK6BvCNCoaDX60+vOo6Np5EUPz1DinjabRil29GVHYJT8aSRIOEwjQDtKpKfr6Px2Hwj3FAfa32l/aL2767S4r16vXZ+t494DdU/Z0si0I9wnQk2gTaAHBNKHfVaFdRJCErAK6BEBMiwV0CNeFdAK6KMiiGfIaL+qqgJaAa2AviGQNhYlJJuMbII8G4gK6BMRi3MFtAJaAf3QMNQg9Nh474H2Hug7Ie+OqiyxV0A//+oK4Uh/t5MSGpXTraC7DNhklOx12nqy9Z3iR8by/H50K4BGymQsCQ/CYaqLJtAm0CbQJlCd4FLiS28J0MjZEqk1wtP1kICRQJCRt4JBx92FL+1rBfRaQiugFdAKaAW0Avr1OfEmUDfipcRGBoME237M0B7XBNovk78UOrrZTYW8OxKyznTVKdvXWaee4rVLFLv42vUSQUz4UH1MgrKaFM/vl45Em0A/C9wqviRots9tH9L5rDDa46g/qM+nPmgCbQJtAm0CbQJtAv3dBWT4dkfTJFTpMyoV0K9kueq0yVmT8zg7C3vvhAptcoK03t0Cna7nuxKDdaY2SdJDGCmu5IgJr9WG3012RCSUxOj8Nvk1gT4RmOqS+mq1fqgup/1vAj3ul8WD9qkJtN+Fe5k0V40AfXyChHW1sCugRwRoHyzBr9YBGZRV45S+r12/NagkYKlBOfcDXR+dvwL6RKAJtAn0o8P97sazxEOCSMS0SqT2dURIaaKj61k9n72edL3T/lRAj1Jk95UErAJ6/Lgb1dkk+PZ19vVkLKbJSxPoSYhTZ74KPL3OCiCNLtNC+ymCXxXWJtAzAkdHXQF94kFCRf1FdTa9vgJ6xN/2ueUd2jebLO1xZDAroBXQS0YmAmoCPcJ2l5Eh3K/l8+8/QL76bACdv/dAPyeoCmgFdOrR93/vU7gnlPoQUb+J6FESdtRnhSqdVDSBNoG+UxPV2aoho+RFk7w0Sdrz2WRpj2sC7edADz2SEqwdAaf36OxoJ33fScCmkZwdHdlEVwF1RsrivkvwNCmhv9s6ndY5TSaor3YF46fwJSFM8bOCT/hNk4BpvWRUew/0CyECnpzTXc6FNowKc3VDK6BPZKkOiICmBq2AVkA/JUASiN36obpMeYfWSzxVAb22Vh3hnnDpCNcRJxkUargpSdI9yCbQIwK0D2QwLLHu4t4Emhm+CujxG5cmw3BXEKJJwVS/FdAK6AGBdNRKQpmOYiugT+JIJwzTPlRAjw1ukyEJGBkPe550n+2oOq2HJtDsobIXXhXQCmgF9A0BEnASpLNTvpvwJmJPCbNP4WaE2XugxwRNiY0MBgm2TZb2OOoPup4m0H4T0aVQ7hJ8E+je71jaUfbUwB3hfh6NEr4TkTeBPhGg+kqFkISKEjm9/mxgaf/tpKACWgGtgH746q8pOVLDktPeNSj2qWFy2HR9q+v87sRN+BKBE4FWQN03ClmjnNaDnehMwtgE2q/yOzg964jIqdlCXiXO1Jmm91bTe6er12uTBhG1JYJVHJpA3e9UpgmkAloB/ZS0p757/XvvgZ4Q6lO4fQr3URK7RsYmJ0pITaCfR+RkbAjfCmgFtAL64eeG7Ggrda6ryc2O0lZHh5SobFKbnBcJi02mRGxNoNfCMe2vrfP0YRiqg7vraTXJ236k+qWkbx++sue5a5JE+2QNHeG4e127vEavX+Vx+75nnJtAm0APCKQjxom46d8roNeURwRdAX0iQIRnJ0lNoE2gpp4mg1IBrYBWQN8Q6MdYrgVqMjxNoNf3ZneTWjpJagI9ImANlN2nCmifwr0UShrZUFKkpJmOYknAdonc3lO0jUWjsV187XqnfaCRKK2fiLkCWgF91ADV2blOpuRPI22aPEznof6w73vuhybQJtAm0CbQkQAroMdETiN2MoBWaJpA+1V+v2vAOmh7nHUu9DDLXZ8fIseTrnc3+aSNt5uQmkD7RQqPGthNsvR6qlP6u63TyTCkD1/9dB+T0SF8iadS/HbPZ/nZHtcE2p8zu0yK1nhY59uHiI6CSKMdIgpL7NTgROz29akQEDF3hNsRbke4U5dcjLZWG5UIwAoBOSVyfumMPb3eVcL5f41+dgneOtNUmNN7p5bIbZ1RHa2ebxUH6p+0Tu0ocrWe08nMrhGxdWz3lSY5dr2rBozOfze+xKu2z21f0PlssrTHUX/QPk190HugJ2TSp7fSQp4KrAJ6/SskVnBs41ZArxO2FaCU+Oz+WUGa+s2uvwK6941OFj/az7SO7L4TH0/GsQJ6+krA3gN9lhIVsiUe60AJd0o0ZCTOr09H0xXQCuh7X0xETgnQ9hURM03ErDFM+8oKku3ntA8roPCxkN3CmArYAk8bf1f0J8eTrnfX4VHj392Qq8I6CWEFtL8H+l4bJFTUX1RnFdB+kcKKoXrVTUe4pw7rCLffhfsoiV0jY4mfCN4m5snIUDLYXefdhozWu5qcrNGbklz68NVu/aRGuAn0iEDK4zQpmPCtgFZADwik96wsMaXv24eI+l2478mAbjXQ322dVkDdPVISHGvMrGGyxokmgr0H+oUQAd8R7hMoKmRLPJaACPfUKVvhtUnIJrrdBEG4pzgQsTeBfiZ+W7+Es316ebd+mkD7RQq/ayAlLCrgcyNMxElE3nug7meizvtBxEC4p8JRAb0mEnLkJOBWCGi/rHGh97HrpetOE8jEJyRgdr2rSY3OT9eZGiri1dSAED50PsvP9jjCg/Zpqt+OcE/IpLPztJBJ8EmgiLDSxmsCPSJGDzHR5IP2xxqC9CE8Ighbd3fVc/o+RLhUp/T3VACoj+x6iZjTfbbXmdYDCVqKH+FD57PCaI8jPGifKqD9MvlDDdgCPxcOCXwT6BOxu/C1ExwiiAroEwGq34koJ2NFhoqIuQL6+ZucJiMzCTAZt94D/UJotXCpYJtA+xRuQrQklHcnBksodl103CQo1H9EZKuCP72vxbkC6h4SIpxXjcHq62g91BdUb7SuJtAm0CbQhXvy1FgkQJbYqcEpGdnXp0mqAno9UbD7vlo/kyDcXU92fVN9TUmQ8ElfVwEdOpESoQXO3rui8zWBNoE2gf41gq6AVkDfa8Am+AroqXPo3pYd6VhnRZGczpeul0ZO3/3UojUA53Xae2SEF12/daCEOxEy4XB+fXpdKV7WoafrSHFoAt0bPdr6JZwtD+wKjeXJyfCnEwlKhCl+FdAK6AEB2zhEjJMAknBYgk4bb3ckZBtrtdHt68hY0OSB9oUIxu6PvZ50vSlhfnc9rxqyVRxtHVthoz4igbDnSffZXmdaD1Tfts9tH9L5JqNLhpbqrg8RfSFEjUZJyG7QaiGmSakC2l9jedQMCdtE7Gmd0nms0KeEOfWFFaSJIK2wWGGrgD4RIB5NhZAEkAwFvb4COkQ4As4KULpBKTENy//bF0pM67UNTtdhic0Sj3WgtuEskd5N5B3h9tdYPgnD1L/Ur1O/pbxlz09CscpbxAe7/LSLB72ecGkCbQI9OMNUEKywkhGwQjk1XAX0GmFKhk2gn5NRSqBTgmoCbQKdOPD93/tNRCeUpsRDxLablGyCJgGkxqfX2+uogB4Jhm4pELHbxFABrYC+10DarzZhTvXaBHrswApoBfSAQNqQE/HTvzeBNoE+ELDGdJf4qR7TSY69Z0sjx7tuxdiJ0S6OFdAK6G8E+hBRv0z+inTumgQQwa4SXhNoE2gT6F8I3MXjZHSmfm0CbQJtAn1DoAJ6LVDTxOA86qOEYycP6fva89oENRmVNDETMTeB9rtwLxPdbmFYpz01Gt2ju8u5nM9PyYLWaxt8F9+7EpIdjaWj4fThI0u4q6O683Wunm8Vh6mu0jpPnyZdree0L6geSSDp77ZOCecK6PHjZqt9kb5uqqe7eJyMThNovwv3Mmla4rEERIROhEwJcDcJVUD7MZZHDaWEmRqPnzbCaV9ZQbL9vPoQnX2dXS8Zt36M5QshAp42/i7nQhtmnRYJB12vdc42OdmGXBXWSQgJhwroEYG0zlMhoDq4u55snZMATknfCtv5ulPc7HnumiTRPlHCJ56yfZ7Wg93vCuiww2kB0UZT4dL5bOOtJrepwEg40kKzhbx6HUTc1HAV0OuGoBHhROwT3lQ3llh3jcsuwVOd0t+pHlcnExZfy1urRngXX1pfip+tK8LP1jsdR/1BhmzCtw8RnZCpgPbXWB4lQUbspwyKJXYiCGvcJqdfAc1G37v1MwmCNQppPVRAj7xHfffCqwJaAT0gkD7kYp1p+r4TAe0SOTXGXZMA68BtEqdkkhImJV27X9ZI7CYkEg76u61TwtniVgF1H5NrAv3zM1ApYVEBk1Oiwu0I94ngXQRvickSMglYBfSIAI3SiaB266AC+vljGDQaJD76KXyJV22fp+ul+pyS+LRemqBMBoj2adKlJtAm0CbQNwRIwG3D7wpTSqxNoJ9/LzQVgIm4m0D7MZb32qiAVkAroBXQ8deCaPSaJo5pgkCGg0a09PcK6N5INcWP9pMS7zS5tEkxfZbFvu+5fiugFdAKaAW0Avr1OfHJMEyTCZpIEDGnkwZrFNKJBAlaBfS6MiqgFdAKaAW0AloB/d0FJND0jEk6kSADMo3SJ8E/n99OPMjoTMaqAloBrYBWQJE4JyJKCdMSWvq+RPxpgpqIu/dAew/0vTYqoBXQCmgFtALaBNoE+sYD9OmR16EV0ApoBbQCWgGtgFZAK6D8DTJ00z59eotm7tOsnh5KoJk8XYcdgdnRlx2B0ecSp3sJ9vNZu6NEcpb9GMsTYfu5XVtn6b6n72vr2N7D6wj3ug4sfn0KF5JZei9gaqBUCFJBmojfPiZtX79LOHcRNzV+SkyTYFmhnBqO3jd9nb2uCmj2lXQkfBZ3eh8iXBJI+rs1gJan7Hp/ygjv4ku8muJH+ND5LD/b44jHaZ8mfDvCBaNAwJ8JhAp5IpwK6PHhhAro9ef2JoOU1qk1wKv1nPYFES4JJP09FQAyona9RMxpgLDXmdYDCVqKH+FD57PCaI8jPGifKqD9PdBDDdgCp6RoG8smdDIS03psEmoCbQJ91EpKmHTLhYzxqmDYuiajY/udBLoj3CPSTaBNoJfCahuyApp9F/SqkZj2g0bp9Dk7S6y7xsXWE613SrZE/LZOCWeb3HeFhhLw3QmfBD7Fz9aV3W+774SLfdaCjPbrPBXQCmgF9A0BSsC24S2BUMKnRq6APhGsgF5LvxX8Cujaz5n9D+a6DxyUf30QAAAAAElFTkSuQmCC">
						</h2>
					</div>
					<h3 class="fw-300 display-4 fw-500 color-primary-600 keep-print-font pt-4 l-h-n m-0">
						RECIBO
					</h3>
					<div class="text-dark fw-700 h1 mb-g keep-print-font">
						#<?php echo $_GET['id']?>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-4 d-flex">
					<div class="table-responsive">
						<table class="table table-clean table-sm align-self-end">
							<tbody>
								
								<tr>
									<td colspan="2">FUNDACAO ASSIS GURGACZ</td>
								</tr>
								<tr>
									<td>
										<strong>CNPJ:</strong> 2.203.539/0001-73
									</td>
								</tr>
								<tr>
									<td>
										Avenida das Torres, 500
									</td>
								</tr>
								<tr>
									<td>
										Bairro FAG - Cascavel - Paraná
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-4 ml-sm-auto">
					<div class="table-responsive">
						<table class="table table-sm table-clean text-right">
							<tbody>

								<tr>
									<td>
										<strong><?php echo utf8_encode($reoferta->nome)?></strong>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo utf8_encode($reoferta->endereco_rua).", ".$reoferta->endereco_numero?>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo utf8_encode($reoferta->endereco_bairro)." - ".utf8_encode($reoferta->endereco_cidade)." - ".utf8_encode($reoferta->endereco_estado)?>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo $reoferta->telefone1?>
									</td>
								</tr>
								<tr>
									<td>
										<?php echo $reoferta->email?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="mt-3">
				Disciplia
				<h1><?php echo utf8_encode($reoferta->disciplina_descricao)?></h1>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive">
						<table class="table mt-2">
							<thead>
								<tr>
									<th class="text-center border-top-0 table-scale-border-bottom fw-700">Parcela</th>
									<th class="border-top-0 table-scale-border-bottom fw-700">Forma de Pagamento</th>
									<th class="border-top-0 table-scale-border-bottom fw-700">Data Vencimento</th>
									<th class="border-top-0 table-scale-border-bottom fw-700">Data Recebimento</th>
									<th class="text-right border-top-0 table-scale-border-bottom fw-700">Valor</th>
								</tr>
							</thead>
							<tbody>
								<?
									$i=1;
									$total = 0;
									$pago = 0;
									$nappago = 0;
									while($row = $pagamento->fetch(PDO::FETCH_OBJ)){
								?>
								<tr>
									<td class="text-center fw-700"><?php echo $i?></td>
									<td class="text-left strong"><?php echo $row->tipo_pagamento?></td>
									<td class="text-left"><?php echo converterData($row->data_vencimento)?></td>
									<td><?php echo $row->recebido ? converterDataHora($row->data_recebimento) : "-"?></td>
									<td class="text-right"><?php echo number_format($row->valor, 2, ',', '.')?></td>
								</tr>
								<?
										$i++;
										$total += $row->valor;
										$pago  += $row->recebido ? $row->valor : 0;
										$nappago  += $row->recebido ? 0 : $row->valor;
									}
								?>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 ml-sm-auto">
					<table class="table table-clean">
						<tbody>
							<tr>
								<td class="text-left">
									<strong>Valor Total</strong>
								</td>
								<td class="text-right"><?php echo number_format($total, 2, ',', '.')?></td>
							</tr>
							
							<tr class="table-scale-border-top border-left-0 border-right-0 border-bottom-0">
								<td class="text-left keep-print-font">
									<h4 class="m-0 fw-700 h2 keep-print-font color-primary-700">Valor Pago</h4>
								</td>
								<td class="text-right keep-print-font">
									<h4 class="m-0 fw-700 h2 keep-print-font"><?php echo number_format($pago, 2, ',', '.')?></h4>
								</td>
							</tr>
							<tr>
								<td class="text-left keep-print-font">
									<h4 class="m-0 fw-700 h2 keep-print-font color-primary-700">Valor a pagar</h4>
								</td>
								<td class="text-right keep-print-font">
									<h4 class="m-0 fw-700 h2 keep-print-font text-danger"><?php echo number_format($nappago, 2, ',', '.')?></h4>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h4 class="py-5 text-primary">
						EMITIDO EM <?php echo date("d/m/Y H:i:s")?>
						<br>
						POR <?php echo $_SESSION['coopex']['usuario']['nome']?>
					</h4>
					<p class="mt-2 text-muted mb-0">
						Autenticação: <?php echo $tipo?>:<?php echo $_GET['id']?> ID:<?php echo md5($_GET['id']*73/7)?>
					</p>
				</div>
			</div>
		</div>
	</div>
</main>