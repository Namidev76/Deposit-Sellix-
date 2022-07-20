<div class="table-responsive">
<table class="table table-sm">
      <thead>
      <tr>
        <th>Transaction</th>
        <th>Gateway</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php


      $paye = new ;
      $info = new ;

      $infos = $info->getInfo();
      if ($stmt = $paye->getpayuser(['user' => $_SESSION['username']]));
      while ($result = $stmt->fetch()) {
        $ID = $result["ID"];
        $Transaction = $result["transaction"];
        $Amount = $result["amount"];
        $Gateway = $result["gateway"];
        $Status = $result["status"];
        $Date = $result["date"];

        $curl = curl_init('https://dev.sellix.io/v1/orders/' . $Transaction);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sellix WooCommerce (PHP ' . PHP_VERSION . ')');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer (your api)']);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $body = json_decode($response, true);

        if ($result["status"] == 'VOIDED') {
          $new_status = [
            'transaction' => $body["data"]["order"]["uniqid"],
            'status' => 'VOIDED',
            'user' => $_SESSION["username"],
          ];
          $plan = new ;
          if ($plan->deletedeposit($new_status)) {
            $_SESSION["actived"] = "Status pay delete cancel";
            header('Location: ../deposit');
            exit;
          } else {
            $_SESSION["error"] = "Error log!";
            header('Location: ../deposit');
            exit;
          }
        }

        if ($result["status"] != 'VALIDE') {
          if ($result["status"] != $body["data"]["order"]["status"]) {
            $new_status = [
              'transaction' => $body["data"]["order"]["uniqid"],
              'status' => $body["data"]["order"]["status"],
              'user' => $_SESSION["username"],
            ];
            $plan = new ;
            if ($plan->updatedepot($new_status)) {
              $_SESSION["actived"] = "Status change";
              header('Location: ../deposit');
              exit;
            } else {
              $_SESSION["error"] = "Error log!";
              header('Location: ../deposit');
              exit;
            }
          }
        }
        $usernn = new ;
        if ($Status == 'COMPLETED') {
          $array_users = [
            'username' => $_SESSION['username']
          ];
          $new_Point = $result["amountreel"] + $usernn->getUsers($array_users)["points"];
          $new_pointss = [
            'points' => $new_Point,
            'username' => $_SESSION["username"],
          ];
          if ($usernn->updateUserPlanpoint($new_pointss)) {
            $new_status = [
              'transaction' => $body["data"]["order"]["uniqid"],
              'status' => 'VALIDE',
              'user' => $_SESSION["username"],
            ];
            $plan = new ;
            if ($plan->updatedepot($new_status)) {
              $_SESSION["actived"] = "Success Your Balance Added";
              header('Location: ../deposit');
              exit;
            } else {
              $_SESSION["error"] = "Error log!";
              header('Location: ../deposit');
              exit;
            }
          }
        }
        if ($Status == 'VALIDE') {
          $echopay = ' <div class="col-lg-12 text-center"><span class="badge bg-success h6">Your payment has been success</span>  </div> <br>';
        } else {
          $echopay = ' 
    <div class="row">   <div class="col-4">
      <div style="margin-left:auto;margin-right:auto;display:block">
      <img class="btcQR" src="https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl=' . $body["data"]["order"]["crypto_uri"] . '">
      </div>
    <br>
    <p class="fs--1">After 1-2 Bitcoin confirmations of the transaction, the deposited amount will be automatically added to your account. Please notice: 1. complete the payment within 15 minutes 2. send the EXACT amount. Otherwise you might have to contact our support to manually apply the credits to your account.
    </p>

  </div>

    <div class="col-8">
      <div class="col-lg-12 mt-3">
        <label class="form-label">Currency</label>
        <input type="text" class="form-control" style="text-transform:uppercase" readonly="" value="' . $body["data"]["order"]["gateway"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Status</label>
        <input type="text" class="form-control" style="text-transform:uppercase" readonly="" value="' . $body["data"]["order"]["status"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Payment Address</label>
        <input type="text" class="form-control" onclick="this.select();document.execCommand(\'copy\');Toastify({text:\' Successfully copied crypto address!\', duration: 3000, backgroundColor: \'bleu\' }).showToast();" value="' . $body["data"]["order"]["crypto_address"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Crypto Amount</label>
        <input type="text" class="form-control" onclick="this.select();document.execCommand(\'copy\');Toastify({text:\' Successfully copied crypto amount!\', duration: 3000, backgroundColor: \'bleu\' }).showToast();" value="' . $body["data"]["order"]["crypto_amount"] . '">
      </div>
      <div class="col-lg-12 mt-3">
        <label class="form-label">Crypto recevin</label>
        <input type="text" class="form-control" style="text-transform:uppercase" readonly="" value="' . $body["data"]["order"]["crypto_received"] . '">
      </div>
      <br>
    </div>
 ';
        }
      ?>
        <tr>
          <td><a href="" type="button" data-bs-toggle="modal" data-bs-target="#error-modal-<?= $ID; ?>"><i class="fas fa-qrcode"></i></a>


            <div class="modal fade" id="error-modal-<?= $ID; ?>" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 700px">
                <div class="modal-content position-relative">
                  <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                    <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                      <h4 class="mb-1"><span class="fab fa-btc"></span> Payment </h4>
                    </div>
                    <div class="p-4 pb-0">
                      <?= $echopay; ?>
                    </div>
                  </div>
                </div>
              </div>


          </td>
          <td><?= $Amount; ?></td>
          <td><?= $Gateway; ?></td>
          <td><?= $Status; ?></td>
          <td><?= $Date; ?></td>
        </tr>

      <?php

      }
      ?>
    </tbody>
  </table>