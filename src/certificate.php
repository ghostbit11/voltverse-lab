<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_login();
$u = pf_user();
if (is_superadmin()) { header('Location: /profile.php'); exit; }   // superadmins don't earn certificates
$org = org_name($u['email']);
$all = CATALOG(); $total = count($all);
$done = count(solved_ids($u['email']));
$pts = player_points($u['email']);
$pct = $total ? round(100*$done/$total) : 0;
$tier = level_name($pts);
$id = 'VV-' . strtoupper(substr(md5($u['email']),0,8));
$date = date('F j, Y');

/* ---- minimal, dependency-free PDF generator (A4 landscape certificate) ---- */
$esc = fn($s) => str_replace(['\\','(',')'], ['\\\\','\\(','\\)'], $s);
$centered = function($font,$size,$y,$text,$r,$g,$b) use ($esc) {
    $w = strlen($text) * $size * 0.5; $x = 421 - $w/2;   // page width 842 → centre = 421
    return sprintf("BT /%s %.1f Tf %.3f %.3f %.3f rg 1 0 0 1 %.1f %.1f Tm (%s) Tj ET\n",
        $font,$size,$r,$g,$b,$x,$y,$esc($text));
};
$C  = "0.13 0.20 0.45 RG 4 w 28 28 786 539 re S\n";               // outer border
$C .= "0.13 0.83 0.93 RG 1 w 40 40 762 515 re S\n";               // inner accent border
$C .= $centered('F1',14,495,'VOLTVERSE SECURITY RANGE',0.13,0.62,0.74);
$C .= $centered('F1',34,440,'Certificate of Achievement',0.06,0.09,0.16);
$C .= $centered('F2',13,395,'This certifies that',0.4,0.45,0.52);
$C .= $centered('F1',30,345,$u['name'],0.10,0.28,0.62);
$C .= $centered('F2',13,300,"has completed $done of $total security challenges ($pct%),",0.30,0.35,0.42);
$C .= $centered('F2',13,280,"earning $pts points and the rank of $tier.",0.30,0.35,0.42);
$C .= sprintf("BT /F2 10 Tf 0.55 0.60 0.66 rg 1 0 0 1 90 150 Tm (%s  -  Issuing authority) Tj ET\n", $esc($org));
$C .= sprintf("BT /F2 10 Tf 0.55 0.60 0.66 rg 1 0 0 1 560 150 Tm (Certificate ID: %s) Tj ET\n", $esc($id));
$C .= $centered('F2',11,110,"Issued $date",0.45,0.50,0.56);

$objs = [];
$objs[1] = "<</Type/Catalog/Pages 2 0 R>>";
$objs[2] = "<</Type/Pages/Kids[3 0 R]/Count 1>>";
$objs[3] = "<</Type/Page/Parent 2 0 R/MediaBox[0 0 842 595]/Resources<</Font<</F1 5 0 R/F2 6 0 R>>>>/Contents 4 0 R>>";
$objs[4] = "<</Length ".strlen($C).">>\nstream\n".$C."endstream";
$objs[5] = "<</Type/Font/Subtype/Type1/BaseFont/Helvetica-Bold>>";
$objs[6] = "<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>";

$pdf = "%PDF-1.4\n"; $offsets = [];
foreach ($objs as $n=>$body) { $offsets[$n] = strlen($pdf); $pdf .= "$n 0 obj\n$body\nendobj\n"; }
$xrefPos = strlen($pdf);
$pdf .= "xref\n0 ".(count($objs)+1)."\n0000000000 65535 f \n";
foreach ($objs as $n=>$body) $pdf .= sprintf("%010d 00000 n \n", $offsets[$n]);
$pdf .= "trailer\n<</Size ".(count($objs)+1)."/Root 1 0 R>>\nstartxref\n$xrefPos\n%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="VoltVerse-Certificate-'.$id.'.pdf"');
header('Content-Length: '.strlen($pdf));
echo $pdf;
