<?php
require_once 'Class/Verif.php';
# on verifie que la methode d'envoie est bien POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
# on verifie que le bouton submit est bien cliqué
    if (isset($_POST["submit"])) {

        # on declare la class verif
        $verif = new Verif();

        # on verifie que les champs sont bien remplis
        if (!isset($_POST['amount'])) {
            echo "Please fill all.";
            exit;
        }

        # on verifie que les champs sont bien remplis
        if (!isset($_POST['type'])) {
            echo "Please fill all.";
            exit;
        }

        # on filtre les données
        $amount = $verif->test_input($_POST["amount"]);
        if (empty($amount)) {
            echo "Please fill all.";
            exit;
        }

        # on filtre les données
        $type = $verif->test_input($_POST["type"]);
        if (empty($type)) {
            echo "Please fill all.";
            exit;
        }

        # on vérifie que le montant est un nombre
        if (!preg_match("/^[0-9]*$/", $amount)) {
            echo "Please enter a valid amount.";
            exit;
        }
        
        # on vérifie que le type de paiement est valide 
        if (!preg_match("/^[a-zA-Z0-9_]*$/", $type)) {
            echo "Please enter a valid type.";
            exit;
        }

        # on vérifie que le montant est supérieur à 1000
        if ($amount > 1000) {
            echo "Please enter a valid amount.";
            exit;
        }

        # on vérifie que le montant n'est pas inferieur a 15 
        if ($amount < 15) {
            echo "Please enter a valid amount.";
            exit;
        }
 
        # on vérifie le token de sécurité
        // $csrf = ($_POST['__csrf']);
        // if ($csrf != $_SESSION['token']) {
        //     echo "error token";
        //     exit;
        // }

        # on vérifie que le type de paiement est valide 
        $allowedtype = array('ETHEREUM', 'BITCOIN', 'LITECOIN', 'MONERO', 'BITCOIN_CASH', 'NANO', 'SOLANA');
        if (!in_array($type, $allowedtype)) {
            echo "Please fill all.";
            exit;
        }

        # on declare la variable plan avec la class ou ce trouve les fonction pour le paiement
        $plan = new ;

        # on cree un tableau avec les donnee 
        $new_payss = [
            'user' => $_SESSION["username"],
            'status' => 'PENDING',
        ];

        # si existe deja un paiement en attente on bloque le nouveau paiement
        if ($plan->check_depot($new_payss) >! 1) {
            $_SESSION["error"] = "Max 1 Deposit Open";
            exit;
        } else {

            $url = "https://dev.sellix.io/v1/payments";

            $data = array(

                "product_id" => "ID DU PRODUIT",

                "gateway" => $type,

                "quantity" => $amount,

                "confirmations" => 1,

                "email" => "youremail@gmail.com",

                "webhook" => "/webhooks/sellix.php",

                "white_label" => "true",

                "return_url" => "Url to return to after payment",

            );

            $content = json_encode($data);

            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, false);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json", "Authorization: Bearer (votre api key)"));

            curl_setopt($curl, CURLOPT_POST, true);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

            $json_response = curl_exec($curl);

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $response = json_decode($json_response, true);

            # si on a pas reussi a creer le paiement on affiche un message d'erreur
            if ($response["data"]["invoice"]["crypto_amount"] == null) {
                echo "error";
                exit;
            }

            # on cree un tableau avec les donnee
            $new_pay = [
                'transaction' => $response["data"]["invoice"]["uniqid"],
                'user' => $_SESSION["username"],
                'amount' => $response["data"]["invoice"]["gateway"],
                'amountreel' => $amount,
                'gateway' => $response["data"]["invoice"]["crypto_amount"],
                'status' => $response["data"]["invoice"]["status"],
                'date' => date("Y-m-d H:i:s"),
            ];

            # on insert le paiement dans la base de donnee
            if ($plan->insertdepot($new_pay)) {
                echo "Success";
                exit;
            } else {
            # on affiche un message d'erreur si on a pas reussi a les insert dans la base de donnee
                echo 'Error insert add';
                exit;
            }
            exit;
        }
    } else {
       
        exit;
    }
}
