<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Home</title>

        <style type="text/css">

            ::selection{ background-color: #E13300; color: white; }
            ::moz-selection{ background-color: #E13300; color: white; }
            ::webkit-selection{ background-color: #E13300; color: white; }

            body {
                background-color: #fff;
                margin: 40px;
                font: 13px/20px normal Helvetica, Arial, sans-serif;
                color: #4F5155;
            }

            a {
                color: #003399;
                background-color: transparent;
                font-weight: normal;
            }

            h1 {
                color: #444;
                background-color: transparent;
                border-bottom: 1px solid #D0D0D0;
                font-size: 19px;
                font-weight: normal;
                margin: 0 0 14px 0;
                padding: 14px 15px 10px 15px;
            }

            code {
                font-family: Consolas, Monaco, Courier New, Courier, monospace;
                font-size: 12px;
                background-color: #f9f9f9;
                border: 1px solid #D0D0D0;
                color: #002166;
                display: block;
                margin: 14px 0 14px 0;
                padding: 12px 10px 12px 10px;
            }

            #body{
                margin: 0 15px 0 15px;
            }

            p.footer{
                text-align: right;
                font-size: 11px;
                border-top: 1px solid #D0D0D0;
                line-height: 32px;
                padding: 0 10px 0 10px;
                margin: 20px 0 0 0;
            }

            #container{
                margin: 10px;
                border: 1px solid #D0D0D0;
                -webkit-box-shadow: 0 0 8px #D0D0D0;
            }
            .table{border:1px solid #D0D0D0;
                   width: 98%;
                   margin:0px 5px 10px 10px;}
            .table thead tr th{border-bottom: 2px solid #f0f0f0;}
            .table tbody tr td{border-bottom: 2px solid #f0f0f0;
             text-align: center;}
            .email{color: activeborder;}
            .btn{ padding: 5px 10px; text-decoration: none; border: 1px solid #999;
            margin-top: 10px;
            background: #f0f0f0;
            clear: both;
            float: right;
            }
        </style>
    </head>
    <body>

        <div id="container">
            <h1>Demo de Base de Dados Usando Arquivo XML </h1>

            <table class="table">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Senha</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $item):
                        ?>
                        <tr>
                            <td ><?php echo $item->login; ?></td>
                            <td><?php echo $item->senha; ?></td>
                            <td><span class="email"><?php echo $item->email; ?><span></td>
                                        <td><a href="<?php echo site_url('home/alterar') ?>/<?php echo $item->id; ?>">Editar</a> | <a href="<?php echo site_url('home/delete') ?>/<?php echo $item->id; ?>">Excluir</a></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>  

            <div>
                <a class="btn" href="<?php echo site_url('home/novo') ?>">Novo</a>
                
            </div>
        </div>

    </body>
</html>