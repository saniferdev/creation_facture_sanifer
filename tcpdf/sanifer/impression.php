<?php
require_once('../tcpdf.php');
include "autoload.php";

class SANPDF extends TCPDF {

    public  $num;
    private $pre;
    private $nom;
    private $mail;
    private $phone;
    private $adresse;
    private $nif;
    private $stat;

    private $url;
    private $key;

    private $sqlServerHost;
    private $sqlServerDatabase;
    private $sqlServerUser;
    private $sqlServerPassword;

    private $sqlServerHost2;
    private $sqlServerDatabase2;
    private $sqlServerUser2;
    private $sqlServerPassword2;

    public $isLastPage = false;
    protected $last_page_flag = false;

    public function __construct() {
        $this->num     = isset($_POST['num']) ? $_POST['num'] : "";
        $this->pre     = isset($_POST['pre']) ? $_POST['pre'] : "";
        $this->nom     = isset($_POST['nom']) ? $_POST['nom'] : "";
        $this->mail    = isset($_POST['mail']) ? $_POST['mail'] : "";
        $this->phone   = isset($_POST['phone']) ? $_POST['phone'] : "";
        $this->adresse = isset($_POST['adresse']) ? $_POST['adresse'] : "";
        $this->nif     = isset($_POST['nif']) ? $_POST['nif'] : "";
        $this->stat    = isset($_POST['stat']) ? $_POST['stat'] : "";

        //$this->num      = "0011630015719";
        $this->url      = "https://talys.clic-till.com/wsRest/1_4/wsServerReceipt/getReceipt/";
        $this->key      = "184565ial6lZJjb5SeaZmWm2tu";

        $this->sqlServerHost      = '192.168.130.50\TALYS';
        $this->sqlServerDatabase  = 'x3v12prod';
        $this->sqlServerUser      = 'reimpression_facture';
        $this->sqlServerPassword  = '8yR=apnE';

        $this->sqlServerHost2      = '192.168.130.64';
        $this->sqlServerDatabase2  = 'OUTILS_DEV';
        $this->sqlServerUser2      = 'dev';
        $this->sqlServerPassword2  = 'WesoKhu640Rfz0Yi';

        parent::__construct();
    }


    public function getRest($url,$key,$num){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Receipts":[
                {
                    "receipt_number" : [
                        {
                            "receipt_number":"'.$num.'"
                        }
                    ]
                }
            ]
          }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Token: '.$key
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }

    public function getData($url,$key,$num){
      $rest     = $this->getRest($url,$key,$num);
      $data     = json_decode($rest);
      $donne    = $data->response->data;

      return $donne;
    }

    public function ModeDePaiement($code) {
        $array_paiement = array(
                    "ACOMPTE"   => "Acompte",
                    "AIRTEL"    => "AIRTEL Money",
                    "AVOCTL"    => "Avoir",
                    "AVOMA"     => "Avoir Manuel",
                    "EnCompte"  => "Compte client",
                    "MVOLA"     => "MVOLA",
                    "ORANGE"    => "ORANGE Money",
                    "VIR"       => "Virement",
                    "CBBMOI"    => "CARTE BANCAIRE BMOI",
                    "CBBNI"     => "CARTE BANCAIRE BNI",
                    "CBBOA"     => "CARTE BANCAIRE BOA",
                    "CBSGM"     => "CARTE BANCAIRE SGM",
                    "CHEQUE"    => "CHEQUE",
                    "CHEQACCBQ" => "CHEQUE ACCES BANQUE",
                    "CHEQBMOI"  => "CHEQUE BMOI",
                    "CHQBNI"    => "CHEQUE BNI",
                    "CHEQBOA"   => "CHEQUE BOA",
                    "CHQSBM"    => "CHEQUE SBM",
                    "CHEQSGM"   => "CHEQUE SGM",
                    "ESPECES"   => "Espèces" );

        return $array_paiement[$code];

    }

    public function Header() {
        $style = array(
            'position' => 'R',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => 0,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, 
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $this->setMargins(5, 60, 10);

        $donne = $this->getData($this->url,$this->key,$this->num);
        $site  = array("SAN01"=>1,"SAN02"=>2,"SAN03"=>3,"SAN04"=>4);

        foreach ($donne as $value) {
            $date_facture   = $value->created_date;
            $date_facture   = date('d/m/Y H:i:s',strtotime($date_facture));
            $caisse         = $value->nickname;
            $fact           = $value->invoice_num;
            $client         = (!empty($this->pre) || !empty($this->nom) ) ? $this->nom." ".$this->pre : $value->orderby_last_name.' '.$value->orderby_first_name;
            $adr_client     = !empty($this->adresse) ? $this->adresse : $value->orderby_adr1.' '.$value->orderby_adr2.' '.$value->orderby_adr3;
            $ville          = $value->orderby_zip.' '.$value->orderby_city.' '.$value->orderby_country_name;
            $tel            = !empty($this->phone) ? $this->phone : $value->orderby_phone;
            $email          = !empty($this->mail) ? $this->mail : $value->orderby_mail;
            $site_          = $value->shop_code;
            $cli            = $this->NIFSTAT_CLI($value->orderby_ext_code);
            $nif            = (empty($this->nif)) ? $cli["CRN_0"] : $this->nif;
            $stat           = (empty($this->stat)) ? $cli["NAF_0"] : $this->stat;
            $cif            = (!empty(trim($cli["YCIF_0"]))) ? "CIF : ".$cli["YCIF_0"] : "";
            $rcs            = (!empty(trim($cli["YRC_0"]))) ? "RCS : ".$cli["YRC_0"] : "";
        }

        $image_file = K_PATH_IMAGES.'sanifer_logo.jpg';
        $this->Image($image_file, 10, 20, 45, '', 'JPG', '', 'T', true, 300, 'C', false, false, 0, false, false, false);

        $this->SetFont('helvetica', '', 10);
        $html = '<h1 style="">SANIFER '.$site[$site_].'</h1>Lot II I 20 AA Morarano<br>101 Antananarivo<br>Madagascar<br>Tel : +261 20 22 530 81<br>info@sanifer.mg<br>STAT : 46900 11 1993 0 10053<br>NIF : 2000036135<br>RCS : 2002B00420<br>CIF : 0241134 /DGI -K du 30/06/2023';

        $this->writeHTMLCell(0, 0, 6, 4, $html, 0, 0, false, true, 'L', true);
        
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);

        $this->SetY(-288);
        $this->setMargins(15, 10, 5);

        $this->write1DBarcode($this->num, 'C128', '', '', '', 18, 0.4, $style, 'N');
        
        $this->Ln(3);
        $this->setMargins(15, 10, 0);
        $this->Cell(179, 0, 'Date '.$date_facture, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        $this->Cell(184, 0, 'N° de facture : '.$fact, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();
         $this->Cell(185, 0, 'N° de ticket : '.$this->num, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        //$this->Cell(171, 0, 'Caissier : '.$caisse, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(168, 0, 'Caissier : '.$caisse, 0, false, 'R', 0, '', 0, false, 'M', 'M');

        $this->setMargins(5, 102, 10);

        $this->setFont('helvetica', 'B', 18);
        $this->Ln(10);
        $this->Write(0, 'FACTURE', '', 0, 'C', true, 0, false, false, 0);
        $this->Ln();
        $this->setFont('helvetica', '', 10);

        // -----------------------------------------------------------------------------

        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="0" border="0">
             <tr>
                <td><b>Facturé à :</b></td>
                <td>Mail : $email</td>
            </tr>
            <tr>
                <td>$client</td>
                <td>STAT : $stat</td>
            </tr>
            <tr>
                <td>$adr_client</td>
                <td>NIF : $nif</td>
                
            </tr>
            <tr>
                <td>$ville</td>
                <td>$cif</td>
                
            </tr>
            <tr>
                <td>Tel : $tel</td>
                <td>$rcs</td>
            </tr>

        </table>
        EOD;

        $this->writeHTML($tbl, true, false, false, false, '');
        $this->Ln();
        
        if ($this->last_page_flag) {
            $tbl_ = "";
        }
        else{
            $tbl_   = <<<EOD
                <table cellpadding="2" style="border-collapse: collapse;" nobr="true">
                <thead>
                     <tr nobr="true">
                      <td style="border-bottom: 1px solid black;"><b>Référence</b></td>
                      <td style="border-bottom: 1px solid black;"><b>Réf F</b></td>
                      <td style="border-bottom: 1px solid black;"><b>Description</b></td>
                      <td style="border-bottom: 1px solid black;"><b>Dépôt</b></td>
                      <td style="border-bottom: 1px solid black;"><b>Qté</b></td>
                      <td style="border-bottom: 1px solid black;"><b>PU HT</b></td>
                      <td style="border-bottom: 1px solid black;"><b>TVA</b></td>
                      <td style="border-bottom: 1px solid black;"><b>HT</b></td>
                      <td style="border-bottom: 1px solid black;"><b>MT TVA</b></td>
                     </tr>
                </thead>
                </table>
                EOD;
        }    
        
        $this->setFont('helvetica', '', 9);
        $this->writeHTML($tbl_, true, false, false, false, '');
        $this->Ln();
        
    }

    public function Ref_Fournisseur($article) {
        $connectionInfo     = array("Database" => $this->sqlServerDatabase, "UID" => $this->sqlServerUser, "PWD" => $this->sqlServerPassword, "CharacterSet" => "UTF-8");
        $link               = sqlsrv_connect($this->sqlServerHost, $connectionInfo);
        if (!$link) {
             die( print_r( sqlsrv_errors(), true));
        }
        $queryParams    = array();
        $queryOptions   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $query          = "SELECT TOP 1 [ITMREFBPS_0] AS REF_F FROM [x3v12prod].[dbo].[ZITMBPS] WHERE [ITMREF_0] = '".$article."' AND [DEFBPSFLG_0] = 2";

        $result = sqlsrv_query($link, $query, $queryParams, $queryOptions);
        if ($result == FALSE){
          return "";
        }
        elseif (sqlsrv_num_rows($result) == 0){
          return "";
        }
        else{
          $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
          return $row['REF_F'];
        }
    }

    public function NIFSTAT_CLI($cli) {
        $connectionInfo     = array("Database" => $this->sqlServerDatabase, "UID" => $this->sqlServerUser, "PWD" => $this->sqlServerPassword, "CharacterSet" => "UTF-8");
        $link               = sqlsrv_connect($this->sqlServerHost, $connectionInfo);
        if (!$link) {
             die( print_r( sqlsrv_errors(), true));
        }
        $queryParams    = array();
        $queryOptions   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $query          = "SELECT * FROM [x3v12prod].[dbo].[ZBPARTNER] WHERE [BPRNUM_0] = '".$cli."' ";

        $result = sqlsrv_query($link, $query, $queryParams, $queryOptions);
        if ($result == FALSE){
          return "";
        }
        elseif (sqlsrv_num_rows($result) == 0){
          return "";
        }
        else{
          $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
          return $row;
        }
    }

    public function Dep($article) {
        $connectionInfo     = array("Database" => $this->sqlServerDatabase, "UID" => $this->sqlServerUser, "PWD" => $this->sqlServerPassword, "CharacterSet" => "UTF-8");
        $link               = sqlsrv_connect($this->sqlServerHost, $connectionInfo);
        if (!$link) {
             die( print_r( sqlsrv_errors(), true));
        }
        $queryParams    = array();
        $queryOptions   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $query          = "SELECT TOP 1 [Depot] AS num FROM [x3v12prod].[dbo].[ZITMFACILIT] WHERE [Ref] = '".$article."' ";

        $result = sqlsrv_query($link, $query, $queryParams, $queryOptions);
        if ($result == FALSE){
          return "";
        }
        elseif (sqlsrv_num_rows($result) == 0){
          return "";
        }
        else{
          $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
          $dep = ($row['num'] == "SE") ? "DEPL" : "DEP".$row['num'];

          return $dep;
        }

    }

    public function Articles() {
        $donne = $this->getData($this->url,$this->key,$this->num);
        foreach ($donne as $value) {
            $art    = $value->articles;            
        }
        return $art;
    }

    public function InsertData($n){
        $connectionInfo     = array("Database" => $this->sqlServerDatabase2, "UID" => $this->sqlServerUser2, "PWD" => $this->sqlServerPassword2, "CharacterSet" => "UTF-8");
        $link               = sqlsrv_connect($this->sqlServerHost2, $connectionInfo);
        if (!$link) {
             die( print_r( sqlsrv_errors(), true));
        }
        $queryParams    = array();
        $queryOptions   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $query = "INSERT INTO [dbo].[reimpression_facture] ([num],[site]) VALUES ('".$n."','SAN')";
        sqlsrv_query($link, $query);
    }

    public function Commentaire() {
        $memo_content = "";
        $donne = $this->getData($this->url,$this->key,$this->num);
        foreach ($donne as $value) {
            $art = $value->posflags;               
            if($art){
                $posflags = $value->posflags->{'Commentaire Vente'}->{'Commentaire Vente-3'};
                if($posflags){
                    foreach ($posflags as $val1) {
                        if(is_array($val1)){
                            foreach ($val1 as $val2) {
                                $memo_content .= $val2->memo_content;
                            }
                        }
                    }
                }
            }
        }
        return $memo_content;
    }

    public function Total(){
        $donne = $this->getData($this->url,$this->key,$this->num);
        $total = array();

        foreach ($donne as $value) {
            $total['ht_']    = number_format($value->total_net_wo_tax, 2, ',', ' ');
            $total['tva_']   = number_format( ($value->total_net_wo_tax * 20)/100 , 2, ',', ' ');
            $total['ttc_']   = number_format($value->total_net_wt_tax, 2, ',', ' ');
        }
        return $total;
    }

    public function Footer() {        
        if($this->isLastPage) {            
            $this->SetY(-75);
            $this->setMargins(5, 60, 10);        
            $donne = $this->getData($this->url,$this->key,$this->num);
            $marge = 0;
            foreach ($donne as $value) {
                $p_     = $value->receipt_tender;            
                $s_     = 0;
                $r_     = "";

                $Y      = -97;
                $Y_     = -122;
                $YM_    = -130;

                $ht_    = number_format($value->total_net_wo_tax, 2, ',', ' ');
                $tva_   = number_format( ($value->total_net_wo_tax * 20)/100 , 2, ',', ' ');
                $ttc_   = number_format($value->total_net_wt_tax, 2, ',', ' ');
                $mt_    = (new Numbers_Words)->toWords($value->total_net_wt_tax,"fr");

                foreach ($p_ as $val) {
                    $p = $val->code_tender;
                    $t_ = number_format($val->amount_taken, 2, ',', ' ');
                    if($val->amount_taken == 0 && $val->amount_given > 0){
                        $s_  = number_format($val->amount_given, 2, ',', ' ');
                        $r_  = '<tr>
                                  <td>Rendu espèces</td>
                                  <td align="right">'.$s_.' Ar</td>
                                </tr>';

                        $Y   = -105;
                        $Y_  = -132;
                        $YM_ = -136;
                    }

                }
                $mode_de_paiement = $this->ModeDePaiement($p);
            }

            $tb_ = <<<EOD
                <table border="1" cellpadding="5" width="73%">
                 <tr>
                  <th><b>Taux</b></th>
                  <th><b>H.T</b></th>
                  <th><b>T.V.A</b></th>
                  <th><b>T.T.C</b></th>
                 </tr>
                 <tr>
                  <td align="right">20,00 %</td>
                  <td align="right">$ht_ Ar</td>
                  <td align="right">$tva_ Ar</td>
                  <td align="right">$ttc_ Ar</td>
                 </tr>
                 <tr>
                  <td><b>Total :</b></td>
                  <td align="right">$ht_ Ar</td>
                  <td align="right">$tva_ Ar</td>
                  <td align="right">$ttc_ Ar</td>
                 </tr>
                </table>
                EOD;

            $tb = <<<EOD
                <table border="1" cellpadding="5" width="73%">
                 <tr>
                  <th colspan="2"><b>Encaissement</b></th>
                 </tr>
                 <tr>
                  <td>$mode_de_paiement</td>
                  <td align="right">$t_ Ar</td>
                 </tr>
                 <tr>
                  <th colspan="2"><b>Rendu</b></th>
                 </tr>
                 $r_
                </table>
                EOD;

            $nb_articles = count($this->Articles());
            if($nb_articles > 4) $marge = -30;

            $commentaire = $this->Commentaire();
                
            $this->SetY($YM_ + $marge);    
            $this->SetFont('helvetica', 'B', 10);
            $this->Cell(0, 0, 'Net A Payer : '.$mt_.' Ariary', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Ln();

            if(!empty($commentaire)){
                $this->SetFont('helvetica', '', 8);
                $this->Cell(0, 0, 'Commentaire Vente: '.$commentaire, 0, false, 'L', 0, '', 0, false, 'M', 'M');
                $this->Ln();
            }

            $this->SetY($Y_ + $marge);
            $this->SetFont('helvetica', '', 10);
            $this->writeHTML($tb_, true, false, false, false, '');
            $this->Ln();

            $this->SetY($Y + $marge);
            $this->writeHTML($tb, true, false, false, false, '');
            $this->Ln();

            //$image_file = K_PATH_IMAGES.'signature.png';
            //$this->Image($image_file, 0, 222, 42, '', 'PNG', '', 'T', true, 300, 'R', false, false, 0, false, false, false);

            //$this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetFont('helvetica', 'U', 10);
            $this->Cell(0, 0, 'Nos références bancaires', 0, false, 'L', 0, '', 0, false, 'M', 'M'); $this->Ln();
            $this->SetFont('helvetica', 'U', 10);
            $this->Cell(70, 0, 'N° Comptes bancaires:', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', '', 10);
            $this->Cell(48, 0, 'BMOI : 00004 00004 01510400172 32', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(122, 0, 'BFV-SG : 00008 00005 21010040646 35', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(115, 0, 'BNI : 00005 00007 26448490100 15', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(117, 0, 'BOA : 00009 07000 15909420006 54', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln(8);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 0, 'Compte MVOLA : 034 76 378 07 / 034 86 609 87 / 034 97 460 82 / 034 12 634 41', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(0, 0, 'Compte AIRTEL MONEY : 033 01 479 73 / 033 12 897 12 / 033 01 479 43 / 033 68 685 85', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(60, 0, 'Compte ORANGE', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Cell(23, 0, ': 102 068 / 117 439 / 108 823 / 163 174', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Cell(101, 0, '#144 OK / TAPEZ 3 OK / TAPEZ 2 OK', 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->Ln();

            $this->setMargins(5, 60, -13);

            $this->SetFont('helvetica', '', 9);

            $tbl = <<<EOD
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td>Le fournisseur</td>
                    <td>Le magasinier</td>
                    <td>Le livreur</td>
                    <td>
                        <u><b>Le client accepte</b></u><br>
                        <u><b>la conformité des</b></u><br>
                        <u><b>marchandises</b></u>
                    </td>
                </tr>
            </table>
            EOD;

 
            $this->writeHTML($tbl, true, false, false, false, '');

        }
        $this->Ln(5);

        $text = $this->getAliasNumPage().'/'.$this->getAliasNbPages();
        $this->Cell(0, 10, $text, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    public function lastPage($resetmargins=false) {
        $nb_articles = count($this->Articles());
        if($nb_articles > 3){
            $this->AddPage();
            $this->setPage($this->getNumPages(), $resetmargins);
        }
        $this->isLastPage = true;
        
    }

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }

}

$pdf = new SANPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('WINNY');
$pdf->setTitle('SANIFER REIMPRESSION FACTURE');
$pdf->setSubject('REIMPRESSION FACTURE');
$pdf->setKeywords('SANIFER, FACTURE, TICKET, REIMPRESSION');

$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' SANIFER TICKET', PDF_HEADER_STRING);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->setMargins(PDF_MARGIN_LEFT-9, PDF_MARGIN_TOP+20, PDF_MARGIN_RIGHT-12);
$pdf->setHeaderMargin(60);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

//$pdf->setAutoPageBreak(TRUE,134);
$pdf->setAutoPageBreak(TRUE);
$pdf->AddPage();
$pdf->SetY(-196);
  
$pdf->SetFont('helvetica', '', 9);

$tbl   = <<<EOD
            <table cellpadding="0" cellspacing="0" style="border-collapse: collapse;" nobr="true">
            EOD;

$rem  = $rc = $mt_c = $total_ht = $total_tva = $ii = $remise = 0;

$art  = $pdf->Articles();

foreach ($art as $val) {
    $l    = $pr = $pr_ = $round = "";

    $Ref_Fournisseur = $pdf->Ref_Fournisseur($val->reference);
    $Dep             = $pdf->Dep($val->reference);
    $ht_             = $val->net_price_wo_tax;
    $tva_            = $val->rate_tax;
    $tht_            = $val->total_net_wo_tax;
    $tva_ht          = $val->total_tax_net_net;
    
    if($val->total_discount_spread_wo_tax > 0){
        $rem         = $val->total_discount_spread_wo_tax * -1;
        $ht_         = $val->brut_price_wo_tax;
        $tht_        = $val->total_brut_wo_tax;
        $tva_ht      = $val->total_tax_brut;
        $rc          = ($val->total_discount_spread_wo_tax * 100) / $tht_;
        $round       = "<br> C".round($rc)." %";
        $mt_c        = ($val->total_tax_brut * round($rc) ) / 100 * -1;
        $pr          = "<br> ".number_format($rem, 2, ',', ' ');
        $pr_         = "<br> ".number_format($mt_c, 2, ',', ' ');
    }

    if( $val === end($art) ) $l = "<br><hr />";

    $total_ht  += $tht_ + $rem;
    $total_tva += $tva_ht + $mt_c;
                
    $tbl  .= '<tr nobr="true">
                      <td>'.$val->reference.'</td>
                      <td>'.$Ref_Fournisseur.'</td>
                      <td>'.$val->desc1.'</td>
                      <td>'.$Dep.$round.'</td>
                      <td>'.number_format($val->quantity, 2, ',', ' ').'</td>
                      <td>'.number_format($ht_, 2, ',', ' ').'</td>
                      <td>'.number_format($tva_, 2, ',', ' ').' %</td>
                      <td>'.number_format($tht_, 2, ',', ' ').$pr.'</td>
                      <td>'.number_format($tva_ht, 2, ',', ' ').$pr_.$l.'</td>
                </tr>';
}

$tbl    .= <<<EOD
                </table>
            EOD;

$total = $pdf->Total();

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdf->Ln(-5);
$pdf->Cell(0, 0, 'TOTAL HT   '.$total['ht_'].' Ar', 0, false, 'R', 0, '', 0, false, 'M', 'M');
$pdf->Ln();
$pdf->Cell(0, 0, 'TVA       '.$total['tva_'].' Ar', 0, false, 'R', 0, '', 0, false, 'M', 'M');
$pdf->Ln();
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 0, 'NET A PAYER   '.$total['ttc_'].' Ar', 0, false, 'R', 0, '', 0, false, 'M', 'M');
$pdf->Ln(10);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__).'/lang/fra.php')) {
    require_once(dirname(__FILE__).'/lang/fra.php');
    $pdf->setLanguageArray($l);
}

$dossier     = dirname(__FILE__)."/uploads/";
$nom_fichier = "Ticket_".$pdf->num."_";
$pdf->InsertData($pdf->num);

$pdf->Output($dossier.$nom_fichier.time().".pdf","FI");
    

