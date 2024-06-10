  <div class='col-md-6 col-sm-12 py-1 px-4'>

    <div class='card border border-success bg-dark text-white'>
      <h6 class='card-header bg-success text-white' data-bs-toggle='collapse' data-bs-target='#collapseCodes'>
        <img src='./images/codes.jpg' alt='Codes: Picture of codes' height='18' class='ms-2' style='margin-top:-0.4rem;border-radius: 3rem;'>
        Codes
        <small class='text-muted'>Click/Tap to hide codes</small>
      </h6>
      <!-- <span>
        <input type='checkbox' id='invalidFilter'>
        <label for='invalidFilter'>Hide invalid</label>
      </span> -->
      <div class='card-body p-0' id='collapseCodes'>

        <div id='successCode'></div>

        <table class='table table-sm table-striped table-hover table-dark my-3 border  caption-top mt-0'>
          <caption>
            <input type='checkbox' id='invalidFilter' class='ms-2'>
            <label for='invalidFilter'>Hide invalid</label>
          </caption>
          <thead>

            <tr>
              <th class='no-select'>Code</th>
              <th class='no-select'>Status</th>
              <th class='no-select'>Credit</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <?php
            foreach ($jsonData['codes'] as $code => $data) {
              $ifSuccess = ($data['status'] == "success") ? "data-success-code='$code'" : "";
              $ifInvalid = ($data['status'] == "invalid") ? "text-decoration-line-through text-muted" : "";
              echo "
            <tr>
              <td class='font-monospace $ifInvalid' style='letter-spacing: 0.25rem;' $ifSuccess>$code</td>
              <td class='no-select $ifInvalid'>{$data['status']}</td>
              <td class='no-select $ifInvalid'>{$data['credit']}</td>
            </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>