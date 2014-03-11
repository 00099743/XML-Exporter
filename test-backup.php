<?php
session_start();
                              $gebruikerID = $_SESSION['GebruikerID'];
				$groepID = $_SESSION['GroepID'];
				$bedrijfID = $_SESSION['BedrijfID'];
                              

?>
<?php

$conn = mysql_connect("localhost", "root", "");

if (!$conn) {
    echo "Unable to connect to DB: " . mysql_error();
    exit;
}

if (!mysql_select_db("rskms_kms")) {
    echo "Unable to select mydbname: " . mysql_error();
    exit;
}

$xml          = '<?xml version="1.0" standalone="yes"?>';
$xml          .= "\n";
$xml          .= '<OrderinfoDataSet xmlns="http://tempuri.org/OrderinfoDataSet.xsd">';
$xml          .= "\n";


 /*Kijkt van welke bedrijf bij welke groothandel hoort*/
$sql = "SELECT * FROM gebruiker where Aanmaker=$gebruikerID";

$result = mysql_query($sql);

if (!$result) {
    echo "Could not successfully run query ($sql) from DB: " . mysql_error();
    exit;
}

if (mysql_num_rows($result) == 0) {
    echo "No rows found, nothing to print so am exiting";
    exit;
}

// While a row of data exists, put that row in $row as an associative array
// Note: If you're expecting just one row, no need to use a loop
// Note: If you put extract($row); inside the following loop, you'll
//       then create $userid, $fullname, and $userstatus



while ($row = mysql_fetch_assoc($result)) {
    $MederwerkerID=$row["GebruikerID"];
//    $sql = "SELECT * FROM gebruiker JOIN bestelling ON gebruiker.GebruikerID = bestelling.GebruikerID WHERE gebruiker.Aanmaker=$gebruikerAanmaker AND bestelling.Geexporteerd=0";
////      echo $sql;
    
     /*Haalt de Medewerkers van het bedrijf op*/
    $sql_mederwerker = "SELECT * FROM gebruiker where Aanmaker=$MederwerkerID";
    $result_mederwerker = mysql_query($sql_mederwerker);


   if (mysql_num_rows($result_mederwerker) > 0) {   
    while($row = mysql_fetch_assoc($result_mederwerker)) 
    {
    $gebruiker_bestel=$row["GebruikerID"];   
    
   /*Zoekt welke bestelling horen bij een Medewerker*/
    $sql_gebruiker_bestel = "SELECT * FROM bestelling where GebruikerID=$gebruiker_bestel";
    $result_gebruiker_bestel = mysql_query($sql_gebruiker_bestel);

    $row = mysql_fetch_assoc($result_gebruiker_bestel); 
    {
    
    
    $bestel_gebruiker=$row["GebruikerID"];  
    /*Zoekt welke Medewerker bij de Bestelling en voert het uit*/
    $sql_bestel_gebruiker = "SELECT * FROM gebruiker JOIN bestelling ON gebruiker.GebruikerID = bestelling.GebruikerID WHERE gebruiker.GebruikerID=$bestel_gebruiker AND bestelling.Geexporteerd=0";
   
//    $sql = "SELECT * FROM gebruiker where GebruikerID=$gebruikerLOP";
    $result_bestel_gebruiker = mysql_query($sql_bestel_gebruiker);
 
    while($row = mysql_fetch_assoc( $result_bestel_gebruiker)) 
    {  
   
    $xml .= "<Klanten>\n";
    /*Zet de GebruikerID in een variable*/
    $gebruiker=$row["GebruikerID"];
    /*Zet bestelling id in een variable*/
    $bestel_bestelling=$row["BestellingID"]; 
   
   $bedrijf=$row["GebruikerID"];
   
    $xml .= '<Klantnummer>';
    $xml.= $row["GebruikerID"];
    $xml .= "</Klantnummer>\n";
    
    $xml .= '<Emailadres>';
    $xml .= $row["Email"];
    $xml .= "</Emailadres>\n";
   
    $xml .= '<Naam>';
    $xml.= $row["Voornaam"];
    $xml .= "</Naam>\n";
    
      $xml .= '<PostadresAdres>';
    $xml.= $row["Straat"]. " ". $row["Huisnummer"]; ;
    $xml .= "</PostadresAdres>\n";
    
      $xml .= '<PostadresPostcode>';
    $xml.= $row["Postcode"];
    $xml .= "</PostadresPostcode>\n";

    $xml .= '<PostadresPlaats>';
    $xml.= $row["Woonplaats"];
    $xml .= "</PostadresPlaats>\n";
  
     $xml .= '<Telefoon>';
    $xml.= $row["Telefoon"];
    $xml .= "</Telefoon>\n";
  
    $xml .= '<Kredietlimiet>';
    $xml.= $row["Budget"];
    $xml .= "</Kredietlimiet>\n";
    
$xml .= "</Klanten>\n";
    /*Haalt de BestellingID op*/ 
   $sql_ophalenbestel = "SELECT * FROM bestelling where GebruikerID=$gebruiker" ;
    $result_ophalenbestel = mysql_query($sql_ophalenbestel);
    $row = mysql_fetch_assoc($result_ophalenbestel); 
    {
    

    $Bestelling=$row['BestellingID'];
    /*Geeft gegeven van EXVAT weer*/ 
    $sql_exvat = "SELECT * FROM bestelling_product where BestellingID=$bestel_bestelling";
    $result_exvat = mysql_query($sql_exvat);
    $row = mysql_fetch_assoc($result_exvat); {
    $product=$row['ProductID'];
     
    
     
     
     
      /*Geeft gegevens van Order op*/

    $sql_order = "SELECT * FROM bestelling_product where BestellingID=$bestel_bestelling";
    $result_order = mysql_query($sql_order);
   $row = mysql_fetch_assoc($result_order); {
       

    
    /*Haalt gegevens van articlen op*/
     $sql_product = "SELECT * FROM product where ProductID=$product";
    $result_product = mysql_query($sql_product);
    while ($row = mysql_fetch_assoc($result_product)) {
          
    $xml .= "<Artikelen>\n";
    
    $xml .= '<Artikelcode>';
    $xml.=$row["ProductID"];
    $xml .= "</Artikelcode>\n";
    
    $xml .= '<Verkoopprijs>';
    $xml.= $row["Verkoopprijs"];
    $xml .= "</Verkoopprijs>\n";
   
    $xml .= '<Eenheid>';
    $xml.= 'Euro';
    $xml .= "</Eenheid>\n";
    
    $xml .= '<Omschrijving>';
    $xml.=$row["Omschrijving"];
    $xml .= "</Omschrijving>\n";
    
    $xml .= "</Artikelen>\n";
 
    }
    }
    
    }
  
    }
    /*Haalt gegevens van billing op*/
    $sql_order_status = "SELECT * FROM bestelling where BestellingID=$bestel_bestelling";
    $result_order_status = mysql_query($sql_order_status);
    $row = mysql_fetch_assoc($result_order_status);
    {
     $xml .= "<Verkooporders>\n";
    
      $xml .= '<OrderID>';
    $xml.= $row["BestellingID"];
    $xml .= "</OrderID>\n";
    
   $xml .= '<Relatiecode>';
    $xml.= $row["GebruikerID"];
    $xml .= "</Relatiecode>\n";
    
      $xml .= '<Datum>';
    $xml.= $row["Datum"];
    $xml .= "</Datum>\n";
    
      
    
    
    
    $xml .= "</Verkooporders>\n";
     
      
   
    }
     /*Haalt gegevens van shipping op*/
    $sql_shipping = "SELECT * FROM bestelling_product where BestellingID=$bestel_bestelling";
    $result_shipping = mysql_query($sql_shipping);
    $row = mysql_fetch_assoc($result_shipping); {
        
    $xml .= "<VerkooporderRegels>\n";
    
    
      $xml .= '<OrderID>';
    $xml.= $row["BestellingID"];
    $xml .= "</OrderID>\n";
    
    
      $xml .= '<Artikelcode>';
    $xml.= $row["ProductID"];
    $xml .= "</Artikelcode>\n";
    
    
      $xml .= '<Aantal>';
    $xml.= $row["Aantal"];
    $xml .= "</Aantal>\n";
    
    $xml .= '<Verkoopprijs>';
    $xml.= $row["Verkoopprijs"];
    $xml .= "</Verkoopprijs>\n";
    
    
     
    $xml .= "</VerkooporderRegels>\n";
    
    $xml .= "\n";
    }
    
  
      
//      /*Verandert gegevens in de database dat laat zien dat het geexporteerd is*/
//      $sql_update = "SELECT * FROM bestelling where GebruikerID=$gebruiker" ;
//    $result_update = mysql_query($sql_update);
//    while($row = mysql_fetch_assoc($result_update)) 
//    {
//        $bestelling=$row["BestellingID"];
// $done=0;
// mysql_query("UPDATE bestelling SET Geexporteerd='$done' WHERE bestellingid='$bestelling';")or die("Can't Insert");
//  }
//  


    
    }
    }
    
    }
   }
    
    
}
    
    
 
    

  

//close the root element
$xml .= "</OrderinfoDataSet>";
 
//send the xml header to the browser
header('Content-Disposition: attachment;filename=DatabaseXML.xml');
 
//output the XML data
echo $xml;

 
?>
