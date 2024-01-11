<?php
$arUrlRewrite=array (
  2 => 
  array (
    'CONDITION' => '#^/client/lk/contract/([^/]+?)/?#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => '',
    'PATH' => '/client/lk/contract/detail.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/lawyer/contract/([^/]+?)/?#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => '',
    'PATH' => '/lawyer/contract/detail.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/admin/lawyers/([^/]+?)/?#',
    'RULE' => 'LAWYER_ID=$1',
    'ID' => '',
    'PATH' => '/admin/lawyers/detail.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/lawyer/tasks/([^/]+?)/?#',
    'RULE' => 'ELEMENT_ID=$1',
    'ID' => '',
    'PATH' => '/lawyer/tasks/detail.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
);
