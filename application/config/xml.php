<?php
/*
 * Arquivo de configuracao para usar a biblioteca XMLDataManager
 * @author:Carlos O Carvalho
 * @create:20-12-2012
 * @descriptio:Aqui devem estar as configuraçoes  que serão importantes 
 * para que a biblioteca funcione.
 */

//configurando path do arquivo database xml
$config['path']                        = APPPATH.'db/db_estados.xml';
//configurando elementos xml para linha da base de dados
$config['table']['row']['element']     = 'row_table';
//attributos das linhas
$config['table']['row']['attribute']   = 'name';
//configurando os elementos de das colunas para base de dados
$config['table']['column']['element']  = 'column';
//configurando os atributos
$config['table']['column']['attribute']= 'name';