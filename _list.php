<div class='row'>
  <div class='col-12 py-1 px-4'>

    <div class='card border border-success bg-dark text-white'>
      <h6 class='card-header bg-success text-white'>Codes
      </h6>
      <span>
        <input type='checkbox' id='invalidFilter'>
        <label for='invalidFilter'>Hide invalid</label>
      </span>
      <div class='card-body p-0'>

        <div id='successCode'></div>

        <table class='table table-sm table-dark my-3 border'>
          <tr>
            <th class='no-select'>Code</th>
            <th class='no-select'>Status</th>
            <th class='no-select'>Credit</th>
          </tr>

          <?php
          foreach ($jsonData['codes'] as $code => $data) {
            $ifSuccess = ($data['status'] == "success") ? "data-success-code='$code'" : "";
            echo "
            <tr>
              <td $ifSuccess>$code</td>
              <td class='no-select'>{$data['status']}</td>
              <td class='no-select'>{$data['credit']}</td>
            </tr>";
          }
          ?>

        </table>
      </div>
    </div>
  </div>
</div>