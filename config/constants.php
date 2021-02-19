<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: constants.php
     MODULE NAME:  Common
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.28
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.28     MESPINOSA       Initial Draft
     100-00-02   1     2016.09.23     AKDELAROSA      Additional Modules for Phase 2 & 3
*******************************************************************************/
?>
<?php
//file : app/config/constants.php

return [
    'MODULE_CODE_USERS'    => '2001',
    'MODULE_CODE_SUPPLIER' => '2002',
    'MODULE_CODE_PRODUCT'  => '2003',
    'MODULE_CODE_REASON'   => '2004',
    'MODULE_CODE_DESTI'    => '2005',
    'MODULE_CODE_PCKNGLIST'=> '2006',
    'MODULE_CODE_MARKUP'   => '2007',
    'MODULE_CODE_CHECK'    => '3001',
    'MODULE_CODE_YPICS'    => '3002',
    'MODULE_CODE_MRA'      => '3003',
    'MODULE_CODE_PRRS'     => '3004',
    'MODULE_CODE_INVOICE'  => '3005',
    'MODULE_CODE_MATERIAL' => '3006',
    'MODULE_CODE_MRP'      => '3007',
    'MODULE_CODE_POSTATS'  => '3008',
    'MODULE_CODE_PRTSTATS' => '3009',
    'MODULE_CODE_DELWRNG'  => '3010',
    'MODULE_CODE_DATUPD'   => '3011',
    'MODULE_CODE_ANSMNGT'  => '3012',
    'MODULE_CODE_DOUJI'    => '3013',
    'MODULE_CODE_PRCHANGE' => '3014',
    'MODULE_CODE_PRBALANCE'=> '3015',
    'MODULE_CODE_STCKQUERY'=> '3016',
    'MODULE_CODE_MATRVC'   => '3017',
    'MODULE_CODE_IQCINS'   => '3018',
    'MODULE_CODE_MATKIT'   => '3019',
    'MODULE_CODE_SAKIISS'  => '3020',
    'MODULE_CODE_EMAIL'    => '3021',
    'MODULE_CODE_PHYINV'   => '3022',
    'MODULE_CODE_PRDMATREQ'=> '3023',
    'MODULE_CODE_PRDMATRET'=> '3024',
    'MODULE_CODE_WHSMATISS'=> '3025',
    'MODULE_CODE_MATDIS'   => '3026',
    'MODULE_CODE_WBSRPRT'  => '3027',
    'MODULE_CODE_PLSYSTEM' => '3028',
    'MODULE_CODE_IQCDB'    => '3029',
    'MODULE_CODE_OQCDB'    => '3030',
    'MODULE_CODE_FGSDB'    => '3031',
    'MODULE_CODE_PCKNGDB'  => '3032',
    'MODULE_CODE_OQCMLD'   => '3033',
    'MODULE_CODE_PCKNGMLD' => '3034',
    'MODULE_CODE_YLDPRFMNCE'=>'3035',
    'MODULE_CODE_INVCING'  => '3036',
    'MODULE_CODE_SEC'      => '4001',
    'MODULE_CODE_PLSET'    => '4002',
    'MODULE_CODE_TRANSET'  => '4003',
    'MODULE_CODE_COMSET'   => '4004',
    'MODULE_CODE_XHIKI'    => '5001',
    'MODULE_CODE_lOCMAT'   => '3037',
    'MODULE_CODE_MATRIX'   => '3038',
    'MODULE_WBS_INV'       => '3039',
    'MODULE_CODE_QCPERM'   => '3040',
    'MODULE_CODE_DISPATCH' => '5002',
    'DB_SQLSRV'            => 'mysql',
    'DB_SQLSRV_BARCODE'    => 'mysql_barcode',
    'DB_SQLSRV_CN'         => 'sqlsrvcn',
    'DB_SQLSRV_TS'         => 'sqlsrvbu',
    'DB_SQLSRV_YF'         => 'sqlsrvyf',
    'DB_SQLSRV_BU'         => 'sqlsrvbu',
    'DB_SHCEMA_TS'         => '',//iscd_main.
    'DB_SCHEMA_YF'         => '',//iscd_main.
    'DB_SCHEMA_BU'         => '',//iscd_main.
    'DB_SCHEMA_CN'         => '',
    'DB_SQLSRV_CN_PRRS'    => 'cn_v4.dbo.',
    'DB_SQLSRV_BU_PRRS'    => 'bu2_v4.dbo.',
    'DB_SQLSRV_TS_PRRS'    => 'probe_v4.dbo.',
    'DB_SQLSRV_YF_PRRS'    => 'yf_v4.dbo.',
    'TABLE_XFLOT'          => 'XSEIB',
    'TABLE_XCUST'          => 'XSEIB',
    'PROJECT_PATH'         => '',
    'PROJECT_EXPORT_PATH'  => trim('storage/exports/ '),
    'EMPTY_FILTER_VALUE'   => 'X',
    'PLSYSTEM_PREPAREDBY'  => 'Romeo Aguda',
    'PLSYSTEM_CHECKEDBY'   => 'Cherry Q/ Cris Sijeng H/ Kaye G/ Marissa M/ Acel R/ Jo C/ SCR',
    'PLSYSTEM_COPY'        => 'Traffic/Prod`n/LOC',
    'PLSYSTEM_PRINTLIMIT'  => 22,
    'MR_PREPEND'           => 'MAT',
    'PR_PREPEND'           => 'PR',
    'PI_PREPEND'           => 'A',
    'SI_PREPEND'           => 'A',
    'PUBLIC_PATH'          => 'public/',
];
