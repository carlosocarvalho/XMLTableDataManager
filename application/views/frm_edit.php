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
            form{ margin: 20px;}
            input[type=text]{
                height: 25px;
                width: 300px;
            }
        </style>
    </head>
    <body>

        <div id="container">
            <?php
            $v = $result[0];
            echo form_open('home/gravar');
            echo form_hidden('action','edit');
            echo form_hidden('id',$v->id);
            echo form_label('Login');
            echo br();
            echo form_input(array('name'=>'login','value'=>$v->login));
            echo br();
            echo form_label('Senha');
            echo br();
            echo form_input(array('name'=>'senha','value'=>$v->senha));
            echo br();
            echo form_label('Email');
            echo br();
            echo form_input(array('name'=>'email','value'=>$v->email));
            echo br();
            echo form_submit(array('value'=>'Gravar'));
            echo form_close();
            
            ?>
            
        </div></body></html>
