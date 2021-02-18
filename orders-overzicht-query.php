<?php
include_once 'conn.php';
include_once 'mssql-100-conn.php';

    $tsql = "SELECT orsrg.artcode,orsrg.aant_gelev,orsrg.ar_soort, orsrg.oms45, orkrg.ordernr,orkrg.ord_contactemail, orkrg.orddat , orkrg.docnumber, orkrg.valcode, orkrg.bdr_ev_val AS Amount, (CASE WHEN orkrg.fiattering = 'J' THEN 1 ELSE 0 END) AS Fiattering, (CASE WHEN orkrg.docattachmentID IS NOT NULL THEN 1 ELSE 0 END) AS Attachment, (CASE WHEN orkrg.DocumentID IS NOT NULL THEN 1 ELSE 0 END) AS Document, orkrg.afldat, orkrg.refer, cicmpy.debcode, orkrg.selcode, orkrg.ID, orkrg.debnr, (orkrg.bdr_ev_val * orkrg.koers) AS NetAmountDefCur, orkrg.bdr_val, orkrg.projectnr, orkrg.fakdebnr, orkrg.verzdebnr 
    FROM orkrg 
    INNER JOIN orkrg o ON o.id = orkrg.id 
    INNER JOIN cicmpy ON orkrg.debnr = cicmpy.debnr 
    INNER JOIN orsrg ON orkrg.ordernr = orsrg.ordernr
    WHERE orkrg.ord_soort = 'V'  AND orkrg.afgehandld = 0
    AND ISNULL(orkrg.selcode,'') <> '2' 
    AND orsrg.ar_soort = 'T'
    AND orsrg.aant_gelev = 0
    AND orkrg.selcode LIKE 'HO%' AND orkrg.docnumber  NOT LIKE '%www%' 
    AND orkrg.docnumber  NOT LIKE '%personeel%' AND orkrg.docnumber  NOT LIKE '%Thuislevering%'  
    AND orkrg.docnumber  NOT LIKE '%accessoires%' ORDER BY orkrg.ordernr DESC";
    $stmt = sqlsrv_query($msconn, $tsql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $data = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $temp = array();
        array_push($temp, 
        explode("|", $row['docnumber'])[0]."<br><span class=smalltext>" . explode("|", $row['docnumber'])[1] . "</span>",  
        '<span class="dateCollapse" Style=position:absolute;>' .$row['orddat']->format('Y-m-d').'</span>'.$row['orddat']->format('d-m-Y'), 
        $row['ord_contactemail'],
        $row['artcode']."<br><span class=smalltext>" . $row['oms45'] . "</span>");
        array_push($data, $temp);
    }

    $tsql2 = "SELECT RIGHT(orkrg.docnumber,LEN(orkrg.docnumber) - charindex('|', orkrg.docnumber)) AS x ,COUNT(*) AS y,SUM(orkrg.bdr_ev_val) AS total 
    FROM orkrg 
        LEFT OUTER JOIN cicmpy ON orkrg.debnr = cicmpy.debnr AND orkrg.debnr IS NOT NULL AND cicmpy.debnr IS NOT NULL  
        WHERE ( orkrg.ord_soort = 'V'  AND ISNULL(orkrg.selcode,'') <> '2' 
        AND orkrg.debnr IS NOT NULL  AND orkrg.orddat > DATEADD(year,-1,GETDATE())
        AND orkrg.selcode LIKE 'HO%' AND orkrg.afgehandld  ='1')
        AND orkrg.selcode LIKE 'HO%' AND orkrg.docnumber  NOT LIKE '%www%' 
        AND orkrg.docnumber  NOT LIKE '%personeel%' 
        AND orkrg.docnumber  NOT LIKE '%Thuislevering%'  
        AND orkrg.docnumber  NOT LIKE '%accessoires%'
        AND orkrg.docnumber  NOT LIKE '%annulatie%'
        AND orkrg.docnumber  NOT LIKE '%B-2020-18650%'
        AND orkrg.docnumber  NOT LIKE '%500056218 1276715%'
        GROUP BY RIGHT(orkrg.docnumber,LEN(orkrg.docnumber) - charindex('|', orkrg.docnumber));";
    
    $stmt2 = sqlsrv_query($msconn, $tsql2);
    if ($stmt2 === false) {
        die(print_r(sqlsrv_errors(), true));
    } 
    $data2 = array();
    while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
        array_push($data2,$row2);
    }
