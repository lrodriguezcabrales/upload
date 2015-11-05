<?php

namespace upload\model;
use upload\lib\data;
class contable {
    
    private $_conn;
    private $_nosource = array('68');
       
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    
    
    public function getDocument(array $param  ){
        
         if(array_key_exists('ANODCTO',$param) ) {
              
                    
             
             $query="SELECT     d.ANODCTO, d.FNTEDCTO, d.NUMEDCTO, d.FECHDCTO, d.NUMTDCTO, d.SUDBDCTO, d.SUCRDCTO, d.IACTDCTO, d.DESCDCTO, d.IDCENCO, 
                      d.IDTERCERO, d.IDCLIPRV, d.IDBANCO, d.CBADCTO, d.CHEDCTO, d.VCHDCTO, d.ENTREGADO, d.TPRECDCTO, d.NDRECDCTO, d.ENFDCTO, 
                      d.AUXILIAR, d.ITEM, d.STATUSDCTO, d.BENEFDCTO, d.IMPRICHEQUE, d.MONTOLETRAS, d.NReversiones, d.AjusteInflacion, d.Paag_Mes, d.Paag_Acu,
                       d.IndContabPrestamo, d.NumVales, d.NumValesConciliados, d.NCF, d.NCF_Modificado, d.Moneda, d.VrMoneda, d.MontoMoneda, d.VencCheque, 
                      d.TasaCambio, d.BU, d.GenEsquema, f.DESFUENTE,
                          (SELECT     NOMBRETER
                            FROM          TERCEROS
                            WHERE      (IDTERCERO = d.IDTERCERO)) AS tercero
                        FROM         [DOCUMENT] AS d INNER JOIN
                      FUENTES AS f ON d.FNTEDCTO = f.IDFUENTE WHERE d.ANODCTO='".$param['ANODCTO']."'";
             
         }  else {
              return array("Message"=>"Parametro invalido");
         
         }
        
          $r = $this->_conn->_query($query);
         
          $data = $this->_conn->_getData($r);
          
              
          
        return $data;
         
        
        
        
    }
    
     public function getEgresos(array $param  ){
        
         if(array_key_exists('ANODCTO',$param) ) {
              
                    
             
             $query="select  * from sifinca.dbo.vwEgresosDocument where ANODCTO='".$param['ANODCTO']."'";
             
         }  else {
              return array("Message"=>"Parametro invalido");
         
         }
        
          $r = $this->_conn->_query($query);
         
          $data = $this->_conn->_getData($r);
          
              
          
        return $data;
         
        
        
        
    }
    
    
    
    public function  getFuentes(){
            
        ## Analizar parametros
           $query ="SELECT   * FROM FUENTES"
                         . " ORDER BY IDFUENTE ";
         
        $r = $this->_conn->_query($query);
       
        $data =$this->_conn->_getData($r);
        
       
    
        return $data;
        
    }

    
     public function  getTransac(array $param){
    
         
         
           $query ="SELECT     ANOTRA, IDFUENTE, NUMDOCTRA, CONSECUTRA, FECHATRA, CODICTA, NITTRA, AUXIAUX, IDCENCO, IDITEM, DESCRITRA, VALORTRA, INDCPITRA, CONCILTRA, 
                      IDBANCO, IDVENDE, IDPLAZA, TIPOFAC, NUMEFAC, VENCEFAC, REFEFAC, IDUSUARIO, FGRATRA, IDZONA, CLIPRV, PORRETETRA, BASERETETRA, CODPRESU, 
                      NRESERVA, VALORMONEDA, STATUSTRA, CONSECUREV, SERIE, AUTORIZACION, FECHAFACT, Adicional_1, Adicional_2, Voucher, TasaCambio, BU, NCF, 
                      NCF_Modificado, FechaCaducidad
                        FROM         TRANSAC
                        WHERE     (ANOTRA = '".$param['ANOTRA']."') "
                   . "AND (NUMDOCTRA = '".$param['NUMDOCTRA']."') "
                   . "AND (IDFUENTE = '".$param['IDFUENTE']."')";
                      
         
           
         
         $r = $this->_conn->_query($query);
         
        return $this->_conn->_getData($r);
         
         
     }
     
     public function  getAccount(array $param){
     
     	 
     	$query ="SELECT     CODICTA, DESCCTA, NIVELCTA, TIPOCTA, CODDCTA, CATFCTA, FEAPCTA, INDPG3CTA, INDCPICTA, IDBANCO, INDCCOCTA, CIERRECTA, AJUINFCTA, 
                      CONINFCTA, NCDPGCTA, UNIADIC1, CODIAJU1, UNIADIC2, CODIAJU2, UNIADIC3, CODIAJU3, INDUNCAL, FORMUCAL, CONTROLPRESU, 
                      DISTRIBUCION, IDMONEDA, CODIAJUSTE, PORCEIMPUESTO, DATOSIMPUESTOS, CTACORRIENTE, IDENTICTA, CIERRE3ROCTA, HABILITARCTA, 
                      NATURALEZACTA, GRUPO, TasaAjuste, IndBaseP0, IndNCF, NIVELPARAMAYOR, CtaAjusteMonPerdida, ExigeItem
				FROM         MAECONT
				WHERE     (CODICTA = '".$param['CODICTA']."')";
     
     	 
     	 
     	 
     	$r = $this->_conn->_query($query);
     	 
     	return $this->_conn->_getData($r);
     	 
     	 
     }
     
}