<?php

	if (basename($_SERVER["REQUEST_URI"]) === basename(__FILE__))
{
	exit('<h1>ERROR 404</h1>Entre em contato conosco e envie detalhes.');
}

?>
<?php
    $dias_acesso=0;

  if(isset($_GET["id_ssh"])){

	$diretorio="../../home.php?page=ssh/editar&id_ssh=".$_GET['id_ssh'];


	$SQLUsuarioSSH = "select * from usuario_ssh WHERE id_usuario_ssh = '".$_GET['id_ssh']."' ";
    $SQLUsuarioSSH = $conn->prepare($SQLUsuarioSSH);
    $SQLUsuarioSSH->execute();


    $usuario_ssh = $SQLUsuarioSSH->fetch();

	if(($SQLUsuarioSSH->rowCount()) > 0){

		$SQLServidor = "select * from servidor WHERE id_servidor = '".$usuario_ssh['id_servidor']."'  ";
        $SQLServidor = $conn->prepare($SQLServidor);
        $SQLServidor->execute();
        $ssh_srv = $SQLServidor->fetch();

        //Calcula os dias restante
	    $data_atual = date("Y-m-d ");
		$data_validade = $usuario_ssh['data_validade'];
		if($data_validade > $data_atual){
		   $data1 = new DateTime( $data_validade );
           $data2 = new DateTime( $data_atual );
           $dias_acesso = 0;
           $diferenca = $data1->diff( $data2 );
           $ano = $diferenca->y * 364 ;
	       $mes = $diferenca->m * 30;
		   $dia = $diferenca->d;
           $dias_acesso = $ano + $mes + $dia;

		}else{
			 $dias_acesso = 0;
		}

		$explo=explode("-",$data_validade);
		$ano=$explo[0];
		$mes=$explo[1];
		$dia=$explo[2];

		$SQLUsuario = "select * from usuario WHERE id_usuario = '".$usuario_ssh['id_usuario']."'  ";
        $SQLUsuario = $conn->prepare($SQLUsuario);
        $SQLUsuario->execute();


        $usuario_sistema = $SQLUsuario->fetch();

		$owner;

		if(($SQLUsuario->rowCount()) > 0){
			if($usuario_ssh['id_usuario']!=$_SESSION['usuarioID'])  {
				if($usuario_sistema['id_mestre']!=$_SESSION['usuarioID']){
					 echo '<script type="text/javascript">';
			echo 	'alert("Nao permitido!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';
				}
			}
		}else{
		    echo '<script type="text/javascript">';
			echo 	'alert("Nao encontrado!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';

	}

	}else{
		    echo '<script type="text/javascript">';
			echo 	'alert("Nao encontrado!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';

	}


  }else{
	        echo '<script type="text/javascript">';
			echo 	'alert("Preencha todos os campos!");';
			echo	'window.location="home.php?page=ssh/contas";';
			echo '</script>';

  }

  if($usuario_ssh['online'] >= 1){
		  $status= "<div class='alert alert-success'>

                <h4><center>ONLINE</center></h4>
				<center><p>".$usuario_ssh['online']." conexão de ".$usuario_ssh['acesso']."</p></center>

              </div>";
	  }else{
		   $status= "<div class='alert alert-danger'>

                <h4><center>Desconectado</center></h4>

              </div>";
	  }
?>
<!-- Main content -->
    <section class="content">

      <div class="row">
      	   <?php if($usuario_ssh['status'] == 2 ){?>
	  <div class="callout callout-danger">
          <h4>Conta Suspensa!</h4>
          <p>
            Olá a conta encontra-se Suspensa
            Entre em contato com o Administrador da sua conta para saber o motivo !.
          </p>
        </div>
	   <?php }?>
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
           <div class="box-header with-border">
            <center>  <h3 class="box-title">Informações</h3></center>
            </div>
            <div class="box-body box-profile">
              <?php echo $status; ?>

              <ul class="list-group list-group-unbordered">
                 <li class="list-group-item">
                  <b>Vencimento:</b> <a class="pull-right"><small><?php echo $dia;?>/<?php echo $mes;?> - <?php echo $ano;?></small></a>
                </li>
                <li class="list-group-item">
                  <b>Falta:</b> <a class="pull-right"><?php echo $dias_acesso." dias"; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Servidor:</b> <a href="home.php?page=servidor/listar"  data-toggle="tooltip" title="Clique Para Informações" class="pull-right"><?php echo $ssh_srv['nome'];?></a>
                </li>
                <li class="list-group-item">
                  <b>Login SSH:</b> <a class="pull-right"><?php echo $usuario_ssh['login'];?></a>
                </li>
                  <li class="list-group-item">
                  <b>Senha SSH:</b> <a class="pull-right"><?php echo $usuario_ssh['senha'];?></a>
                </li>
                <li class="list-group-item">
                  <b>Owner:</b> <a class="pull-right"><?php echo $usuario_sistema['nome'];?></a>
                </li>
              </ul>
			   <form role="form2" action="pages/system/funcoes.conta.ssh.php" onsubmit="return confirm('Tem certeza que deseja fazer isso?');" method="post" class="form-horizontal">
              <div class="box-footer">

					<input type="hidden"  id="diretorio" name="diretorio" value="../../home.php?page=ssh/contas"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="owner" name="owner" value="<?php echo $_SESSION['usuarioID']; ?>"  >





					 </div>






            </div>

            <!-- /.box-body -->
          </div>
          <!-- /.box -->
           <div class="box box-warning">
		    <div class="box-header with-border">
            <center>  <h3 class="box-title">Ações</h3></center>
            </div>
            <center> <br />
			<button type="submit" data-toggle="tooltip" title="Remove do Servidor" class="btn btn-danger" id="op" name="op" value="deletar" >Deletar conta SSH</button><br><br>
			<?php if($usuario_ssh['status']==2){?>
			<button type="submit" data-toggle="tooltip" title="Reativa a Conta SSH" class="btn btn-success" id="op" name="op" value="ususpender" >Reativar conta</button><br><br>
			<?php }else{ ?>
			<button type="submit" data-toggle="tooltip" title="Suspende Temporariamente" class="btn btn-warning" id="op" name="op" value="suspender" >Suspender conta</button><br><br>
			<?php } ?>
			</center>

		    </div>
		    	  </form>


        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               <?php if($usuario['tipo']=="revenda"){?>
               <li><a href="#dono" data-toggle="tab">Alterar Owner</a></li>
               <?php } ?>
               <li class="active"><a href="#senha" data-toggle="tab">PassWord</a></li>
			   <?php if($usuario['tipo']=="revenda"){?>
			   <li><a href="#vencimento" data-toggle="tab">Vencimento</a></li>
			   <li><a href="#acesso" data-toggle="tab">Quantidade de Acesso</a></li>
			   <?php } ?>



            </ul>
            <div class="tab-content">

            <div class="tab-pane" id="dono">

		   <?php if($usuario['tipo']=="revenda"){?>
		    <!-- Horizontal Form -->
          <div class="box box-primary">
            <div class="box-header with-border">
            <center>  <h3 class="box-title">Owner da conta SSH </h3></center>
            </div>
            <!-- /.box-header -->
            <!-- form start -->


            <form role="form2" action="pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">

                <div class="form-group">

                <select class="form-control select2" style="width: 70%;margin-left: 100px; "  name="n_owner" id="n_owner">

				  <?php if($usuario_sistema['id_usuario'] == $_SESSION['usuarioID']){
					  $owner = $_SESSION['usuarioID'];
					  ?>
				  <option selected="selected" value="<?php echo $_SESSION['usuarioID']; ?>">Usuário do Sistema</option>
				  <?php
				  } else{
                     $owner = $usuario_sistema['id_usuario'];
				  ?>
				     <option selected="selected" value="<?php echo $usuario_sistema['id_usuario']; ?>"><?php echo $usuario_sistema['login']; ?></option>
					  <option  value="<?php echo $_SESSION['usuarioID']; ?>">Usuário do Sistema</option>
				  <?php
				  }
				  ?>

				 <?php



	 $SQLUsuario = "SELECT * FROM usuario where id_mestre = '".$_SESSION['usuarioID']."'";
     $SQLUsuario = $conn->prepare($SQLUsuario);
     $SQLUsuario->execute();

if (($SQLUsuario->rowCount()) > 0) {
    // output data of each row
    while($row = $SQLUsuario->fetch()) {
		if($row['id_usuario'] != $usuario_sistema['id_usuario']){


		?>

	<option value="<?php echo $row['id_usuario'];?>" ><?php echo $row['login'];?></option>

   <?php }
	}
}

?>
                </select>
              </div>



              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                    <input type="hidden"  id="op" name="op" value="owner"  >

					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >

					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >

                    <input type="hidden"  id="owner" name="owner" value="<?php echo $owner; ?>"  >

                <center><button type="submit" class="btn btn-primary">Alterar Owner da conta SSH</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
          <?php }else{
			$owner = $_SESSION['usuarioID'];
		  }?>

            </div>



			   <div class="active tab-pane" id="senha">

      <!-- Horizontal Form  -->
          <div class="box box-primary">
                 <div class="box-header with-border">
               <center><h3 class="box-title">Alterar Senha</h3> </center>
            </div>
            <!-- /.box-header -->
            <!-- form start -->


            <form role="senha" id="senha" name="senha" action="pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-2 control-label">Senha</label>

                  <div class="col-sm-10">
                    <input required="required" type="text" class="form-control" id="senha_ssh" name="senha_ssh" placeholder="Digite a nova senha">
                  </div>

				    <input type="hidden"  id="op" name="op" value="senha"  >
                    <input type="hidden"  id="id_ssh" name="id_ssh" value="<?php echo $_GET["id_ssh"]; ?>"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >
					<input type="hidden"  id="id_servidor" name="id_servidor" value="<?php echo $ssh_srv['id_servidor']; ?>"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="owner" name="owner" value="<?php echo $_SESSION['usuarioID']; ?>"  >
                </div>


              </div>
              <!-- /.box-body -->
              <div class="box-footer">

                <center> <button type="submit" class="btn btn-primary">Alterar Senha</button> </center>
              </div>
              </div>
              <!-- /.box-footer -->
            </form>



              </div>

              <div class="tab-pane" id="vencimento">
              <?php if($usuario['tipo']=="revenda"){?>
               <div class="box box-primary">
            <div class="box-header with-border">
            <center>  <h3 class="box-title">Dias de acessos </h3></center>
            </div>
            <!-- /.box-header -->
            <!-- form start -->


            <form role="form2" action="pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Quantidade</label>

                  <div class="col-sm-10">
                    <input required="required" type="number" class="form-control" id="dias" name="dias" placeholder="Digite a quantidade dias de acesso" value="<?php echo $dias_acesso; ?>" >
                  </div>



                    <input type="hidden"  id="op" name="op" value="dias"  >
                    <input type="hidden"  id="id_usuarioSSH" name="id_usuarioSSH" value="<?php echo $_GET["id_ssh"]; ?>"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >

                    <input type="hidden"  id="owner" name="owner" value="<?php echo $_SESSION['usuarioID']; ?>"  >

                </div>


              </div>
              <!-- /.box-body -->
              <div class="box-footer">

                <center><button type="submit" class="btn btn-primary">Alterar dias de acesso</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
          <?php }?>
              </div>


                <div class="tab-pane" id="acesso">
              <?php if($usuario['tipo']=="revenda"){?>
               <!-- Horizontal Form -->
          <div class="box box-primary">
            <div class="box-header with-border">
            <center>  <h3 class="box-title">Acesso simultâneo </h3></center>
            </div>
            <!-- /.box-header -->
            <!-- form start -->


            <form role="form2" action="pages/system/funcoes.conta.ssh.php" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Quantidade</label>

                  <div class="col-sm-10">
                    <input required="required" type="number" class="form-control" id="acesso" name="acesso" placeholder="Digite a quantidade de acesso" value="<?php echo $usuario_ssh['acesso']; ?>">
                  </div>



                    <input type="hidden"  id="op" name="op" value="acesso"  >
					<input type="hidden"  id="diretorio" name="diretorio" value="<?php echo $diretorio; ?>"  >
					<input type="hidden"  id="id_usuario_ssh" name="id_usuario_ssh" value="<?php echo $usuario_ssh['id_usuario_ssh']; ?>"  >
                    <input type="hidden"  id="owner" name="owner" value="<?php echo $owner; ?>"  >
				    <input type="hidden"  id="sistema" name="sistema" value="<?php echo $_SESSION['usuarioID']; ?>"  >
                </div>


              </div>
              <!-- /.box-body -->
              <div class="box-footer">

                <center><button type="submit" class="btn btn-primary">Alterar conexão simultânea</button> </center>
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <?php } ?>
          <!-- /.box -->
			   <div >


            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->