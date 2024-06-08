<div class="col">
  <div class="card p-0 border border-danger bg-dark text-white">
    <h6 class="card-header bg-danger text-white py-1">
      Pull Codes for <?= $login_as ?> 
      <?= $lastCodesPulledBtn_user ?>
    </h6>
    <div class="card-body p-2">
      <form action="?user" method="POST">
        <label class="form-label">Random Spots?
          <input type="checkbox" name="random" value="yes" /></label>
          <div class="input-group input-group-sm">
            <input class="form-control form-control-sm mb-1" type="hidden" name="creditAdd" value="creditAdd" />
            <input class="form-control form-control-sm mb-1" type="hidden" name="creditTo" value='<?= $login_as ?>'/>
            <input class="form-control form-control-sm mb-1" type="number" step="1" name="numberOfCodes"
            placeholder="How many?" required />
            <button class="btn btn-danger" type="submit">
              Get Codes
            </button>
          </div>
      </form>
    </div>
  </div>
</div>